<?php

namespace Modules\ProAccount\Repositories;

use Carbon\Carbon;
use Modules\ProAccount\Entities\Transaction;
use Maatwebsite\Excel\Facades\Excel;
use Modules\ProAccount\Export\PartnerAccountSummaryExport;

class LeadgerReportRepository
{
    public function balanceBeforeDate($dateFrom, $beforedateAccount, $showroom_id)
    {
        $beforeDateTransactions = Transaction::query();
        if ($showroom_id) {
            $beforeDateTransactions = $beforeDateTransactions->where('showroom_id', $showroom_id);
        }
        $beforeDateTransactions = $beforeDateTransactions->where('leadger_id', $beforedateAccount['id']);

        $beforeDateTransactions = $beforeDateTransactions->whereHas('voucher', function ($query) use ($dateFrom) {
                                                            $query->where('is_approve', 1)->where('date', '<', $dateFrom);
                                                        })
                                                        ->select('type', 'amount', 'id')
                                                        ->latest()->get();
        if ($beforedateAccount->type == 1 || $beforedateAccount->type == 4) {
            $balance = $beforeDateTransactions->where('type', 'Dr')->sum('amount') - $beforeDateTransactions->where('type', 'Cr')->sum('amount');
        } else {
            $balance = $beforeDateTransactions->where('type', 'Cr')->sum('amount') - $beforeDateTransactions->where('type', 'Dr')->sum('amount');
        }
        return $balance;
    }

    public function search($dateFrom, $dateTo, $account_id, $showroom_id)
    {
        if ($dateFrom != null && $dateTo != null) {

            $DebitList = Transaction::query();

            if ($showroom_id) {
                $DebitList = $DebitList->where('showroom_id', $showroom_id);
            }

            $DebitList = $DebitList->whereHas('voucher', function ($query) use ($account_id, $dateFrom, $dateTo) {
                                        $query->where('is_approve', 1)->whereBetween('date', array($dateFrom, $dateTo));
                                    })
                                    ->where('leadger_id', $account_id)
                                    ->with(['leadger' => function ($query) {
                                        $query->select('id', 'type', 'name', 'code');
                                    }])
                                    ->with(['voucher' => function ($query) {
                                        $query;
                                    }])
                                    ->select('type', 'narration', 'amount', 'voucher_id')
                                    ->get()->sortBy('voucher.date');
        } else {
            $DebitList = Transaction::query();

            if ($showroom_id) {
                $DebitList = $DebitList->where('showroom_id', $showroom_id);
            }

            $DebitList = $DebitList->whereHas('voucher', function ($query) use ($account_id) {
                                        $query->where('is_approve', 1);
                                    })
                                    ->where('leadger_id', $account_id)
                                    ->with(['leadger' => function ($query) {
                                        $query->select('id', 'type', 'name', 'code');
                                    }, 'voucher' => function ($query) {
                                        $query->select('id', 'type', 'amount', 'txn_id', 'date');
                                    }])
                                    ->select('type', 'narration', 'amount', 'voucher_id')
                                    ->get()->sortBy('voucher.date');
        }

        return $DebitList;
    }

    public function balanceBeforeDateSubleadger($dateFrom, $beforedateAccount)
    {
        $beforeDateTransactions = Transaction::with(['voucher' => function ($q) {
            return $q->select('id', 'is_approve', 'date');
        }])
            ->where('sub_leadger_id', $beforedateAccount['id'])
            ->whereHas('voucher', function ($query) use ($dateFrom) {
                $query->where('is_approve', 1)->where('date', '<', $dateFrom);
            })
            ->select('type', 'amount', 'id')
            ->latest()
            ->get();
        if ($beforedateAccount->leadger->type == 1 || $beforedateAccount->leadger->type == 4) {
            $balance = $beforeDateTransactions->where('type', 'Dr')->sum('amount') - $beforeDateTransactions->where('type', 'Cr')->sum('amount');
        } else {
            $balance = $beforeDateTransactions->where('type', 'Cr')->sum('amount') - $beforeDateTransactions->where('type', 'Dr')->sum('amount');
        }
        return $balance;
    }

    public function searchSubleadger($dateFrom, $dateTo, $account_id, $showroom_id, $relational_data = [], $selected_data = ['*'])
    {
        if ($dateFrom != null && $dateTo != null) {
            $results = Transaction::whereBetween('date', array($dateFrom, $dateTo))
                                    ->whereHas('voucher', function ($query) use ($account_id) {
                                        $query->where('is_approve', 1);
                                    })
                                    ->where('sub_leadger_id', $account_id)
                                    ->with($relational_data)
                                    ->select('id', 'voucher_id')
                                    ->get();

            $voucher_ids = $results->pluck(['voucher_id'])->toArray();
            $partner_transaction_ids = $results->pluck('id')->toArray();

            $real_transactions = Transaction::whereBetween('date', array($dateFrom, $dateTo))
                                            ->whereHas('voucher', function ($query) use ($account_id) {
                                                $query->where('is_approve', 1);
                                            })
                                            ->whereIn('voucher_id', $voucher_ids)
                                            ->whereIn('id', $partner_transaction_ids)
                                            ->with($relational_data)
                                            ->select($selected_data)
                                            ->get()
                                            ->groupBy('leadger.name');
            $data['real_transactions'] = $real_transactions;
            $data['opening_transactions'] = [];
        } else {

            $results = Transaction::whereHas('voucher', function ($query) use ($account_id) {
                                        $query->where('is_approve', 1);
                                    })
                                    ->where('sub_leadger_id', $account_id)
                                    ->with($relational_data)
                                    ->select('id', 'voucher_id')
                                    ->get();

            $voucher_ids = $results->pluck(['voucher_id'])->toArray();
            $partner_transaction_ids = $results->pluck('id')->toArray();

            $real_transactions = Transaction::whereHas('voucher', function ($query) use ($account_id) {
                                                $query->where('is_approve', 1);
                                            })
                                            ->whereIn('voucher_id', $voucher_ids)
                                            ->whereIn('id', $partner_transaction_ids)
                                            ->with($relational_data)
                                            ->select($selected_data)
                                            ->get()
                                            ->groupBy('leadger.name');

            $data['real_transactions'] = $real_transactions;
            $data['opening_transactions'] = [];
        }

        return $data;
    }

    public function searchSubleadgerSummary($dateFrom, $dateTo, $account_id, $voucher_type)
    {
        $start_date = ($dateFrom) ? $dateFrom : Carbon::now()->startOfMonth()->format('Y-m-d');
        $end_date = ($dateTo) ? $dateTo : Carbon::now()->endOfMonth()->format('Y-m-d');
        if ($voucher_type == "cash") {
            $type[] = "cash";
            $type[] = "rec_cash";
            $type[] = "pay_cash";
        }
        if ($voucher_type == "bank") {
            $type[] = "bank";
            $type[] = "rec_bank";
            $type[] = "pay_bank";
        }
        if ($voucher_type == "misc") {
            $type[] = "misc";
        }
        if ($voucher_type != null) {
            $DebitList = Transaction::whereHas('voucher', function ($query) use ($account_id, $start_date, $end_date, $type) {
                $query->with(['transactions' => function ($q) {
                    $q->select('sub_leadger_id', 'type', 'id');
                }])->where('is_approve', 1)->whereIn('type', $type)->whereBetween('date', array($start_date, $end_date))->whereHas('transactions', function ($query) use ($account_id) {
                    $query->where('sub_leadger_id', $account_id)->where('type', 'Dr');
                });
            })
                ->where('type', 'Cr')
                ->with(['leadger' => function ($query) {
                    $query->select('name', 'code', 'type');
                }, 'voucher' => function ($query) {
                    $query->select('id', 'date', 'amount', 'type', 'txn_id', 'narration');
                }, 'fiscal_year' => function ($query) {
                    $query->select('start_date', 'end_date', 'id');
                }])
                ->with(['voucher.transactions' => function ($query) use ($account_id, $start_date, $end_date) {
                    $query->where('sub_leadger_id', $account_id)->whereBetween('date', array($start_date, $end_date));
                }])
                ->select('leadger_id', 'type', 'voucher_id', 'amount', 'narration', 'id', 'accounting_period_id', 'date')
                ->get();

            $CreditList = Transaction::whereHas('voucher', function ($query) use ($account_id, $start_date, $end_date, $type) {
                $query->where('is_approve', 1)->whereIn('type', $type)->whereBetween('date', array($start_date, $end_date))->whereHas('transactions', function ($query) use ($account_id, $start_date, $end_date) {
                    $query->where('sub_leadger_id', $account_id)->where('type', 'Cr');
                });
            })
                ->where('type', 'Dr')
                ->with(['leadger' => function ($query) {
                    $query->select('name', 'code', 'type');
                }, 'voucher' => function ($query) {
                    $query->select('id', 'date', 'amount', 'type', 'txn_id', 'narration');
                }, 'fiscal_year' => function ($query) {
                    $query->select('start_date', 'end_date', 'id');
                }])
                ->with(['voucher.transactions' => function ($query) use ($account_id, $start_date, $end_date) {
                    $query->where('sub_leadger_id', $account_id)->whereBetween('date', array($start_date, $end_date));
                }])
                ->select('leadger_id', 'type', 'voucher_id', 'amount', 'narration', 'id', 'accounting_period_id', 'date')
                ->get();
        } else {
            $DebitList = Transaction::with(['leadger' => function ($query) {
                $query->select('name', 'code', 'type', 'id');
            }, 'voucher' => function ($query) {
                $query->select('id', 'date', 'amount', 'type', 'txn_id', 'narration');
            }, 'fiscal_year' => function ($query) {
                $query->select('start_date', 'end_date', 'id');
            }])
                ->with(['voucher.transactions' => function ($query) use ($account_id, $start_date, $end_date) {
                    $query->where('sub_leadger_id', $account_id)->whereBetween('date', array($start_date, $end_date));
                }])
                ->whereHas('voucher', function ($query) use ($account_id, $start_date, $end_date) {
                    $query->where('is_approve', 1)->whereBetween('date', array($start_date, $end_date))->whereHas('transactions', function ($query) use ($account_id, $start_date, $end_date) {
                        $query->where('sub_leadger_id', $account_id)->where('type', 'Dr');
                    });
                })
                ->where('type', 'Cr')
                ->select('leadger_id', 'type', 'voucher_id', 'amount', 'narration', 'id', 'accounting_period_id', 'date')
                ->get();

            $CreditList = Transaction::with(['leadger' => function ($query) {
                $query->select('name', 'code', 'type', 'id');
            }, 'voucher' => function ($query) {
                $query->select('id', 'date', 'amount', 'type', 'txn_id', 'narration');
            }, 'fiscal_year' => function ($query) {
                $query->select('start_date', 'end_date', 'id');
            }])
                ->with(['voucher.transactions' => function ($query) use ($account_id, $start_date, $end_date) {
                    $query->where('sub_leadger_id', $account_id)->whereBetween('date', array($start_date, $end_date));
                }])
                ->whereHas('voucher', function ($query) use ($account_id, $start_date, $end_date) {
                    $query->where('is_approve', 1)->whereBetween('date', array($start_date, $end_date))->whereHas('transactions', function ($query) use ($account_id, $start_date, $end_date) {
                        $query->where('sub_leadger_id', $account_id)->where('type', 'Cr');
                    });
                })
                ->where('type', 'Dr')
                ->select('leadger_id', 'type', 'voucher_id', 'amount', 'narration', 'id', 'accounting_period_id', 'date')
                ->get();
        }
        return $DebitList->merge($CreditList)->sortBy('leadger_id');
    }

    public function csvDownload($dateFrom, $dateTo, $account_id, $voucher_type)
    {
        $start_date = ($dateFrom) ? $dateFrom : Carbon::now()->startOfMonth()->format('Y-m-d');
        $end_date = ($dateTo) ? $dateTo : Carbon::now()->endOfMonth()->format('Y-m-d');

        if (file_exists(public_path("uploads/csv/partner_accounts_summary.xlsx"))) {
            unlink(public_path("uploads/csv/partner_accounts_summary.xlsx"));
        }
        return Excel::store(new PartnerAccountSummaryExport($account_id, $start_date, $end_date, $voucher_type), 'uploads/csv/partner_accounts_summary.xlsx', 'public_folder');
    }
}
