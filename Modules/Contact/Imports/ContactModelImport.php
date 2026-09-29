<?php

namespace Modules\Contact\Imports;

use Modules\Contact\Entities\ContactModel;
use Modules\Account\Entities\TimePeriodAccount;
use Modules\Account\Repositories\OpeningBalanceHistoryRepository;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\Event;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Validation\Rule;
use Modules\Account\Entities\ChartAccount;
use Modules\ProAccount\Entities\SubLeadger;
use Modules\ProAccount\Repositories\JournalRepository as ProJournalRepository;
use Carbon\Carbon;

class ContactModelImport implements ToCollection, WithHeadingRow, WithValidation
{

    public function headingRow(): int
    {
        return 0;
    }
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */

    public function collection(Collection $rows)
    {
        foreach ($rows->skip(1) as $row) 
        {
            $contact = ContactModel::create([
                str_replace(' ', '', $rows[0][0]) => $row[0],
                str_replace(' ', '', $rows[0][1]) => $row[1],
                str_replace(' ', '', $rows[0][2]) => $row[2],
                str_replace(' ', '', $rows[0][3]) => $row[3],
                str_replace(' ', '', $rows[0][4]) => $row[4] ?? 0,
                str_replace(' ', '', $rows[0][5]) => $row[5],
                str_replace(' ', '', $rows[0][6]) => $row[6],
                str_replace(' ', '', $rows[0][7]) => $row[7],
                str_replace(' ', '', $rows[0][8]) => $row[8],
                str_replace(' ', '', $rows[0][9]) => $row[9],
                str_replace(' ', '', $rows[0][10]) => (!empty($row[10])) ? $row[10] : "n/a",
            ]);
            if (moduleStatusCheck('ProAccount') && Settings('current_active_accounting') == "pro") {
                $sub_leadger = Subleadger::create([
                    'leadger_id' => (strtolower($contact->contact_type) == "supplier") ? Settings('account_payable') : Settings('account_recievable'),
                    'code' => $contact->contact_id,
                    'name' => $contact->name,
                    'morphable_type' => get_class($contact),
                    'morphable_id' => $contact->id,
                    'description' => $contact->contact_type.' Account Created'
                ]);
                if ($contact->opening_balance != null && $contact->opening_balance > 0) {
                    if (strtolower($contact->contact_type) == "supplier") {
                        $debit_amounts[] = null;
                        $debit_account_id[] = null;
                        $debit_partner_id[] = null;
                        $debit_narration[] = null;
                        $debit_cash_flow_account_id[] = 0;
        
                        $credit_amounts[] = $contact->opening_balance;
                        $credit_account_id[] = Settings('account_payable');
                        $credit_partner_id[] = $sub_leadger->id;
                        $credit_narration[] = $contact->name.' - Opening Balance Amount';
                        $credit_cash_flow_account_id[] = 0;
                    } else {
                        $debit_amounts[] = $contact->opening_balance;
                        $debit_account_id[] = Settings('account_recievable');
                        $debit_partner_id[] = $sub_leadger->id;
                        $debit_narration[] = $contact->name.' - Opening Balance Amount';
                        $debit_cash_flow_account_id[] = 0;
        
                        $credit_amounts[] = null;
                        $credit_account_id[] = null;
                        $credit_partner_id[] = null;
                        $credit_narration[] = null;
                        $credit_cash_flow_account_id[] = null;
                    }
            
                    $journalRecieveRepository = new ProJournalRepository();
                    $voucher = $journalRecieveRepository->create([
                        'type' => "misc",
                        'is_cash_flow_journal' => 0,
                        'amount'=> $contact->opening_balance,
                        'date'=> Carbon::now()->format('Y-m-d'),
                        'credit_account_id'=> $credit_account_id,
                        'credit_sub_account_id'=> $credit_partner_id,
                        'credit_cash_flow_account_id'=> $credit_cash_flow_account_id,
                        'credit_account_amount'=> $credit_amounts,
                        'credit_narration'=> $credit_narration,
                        'narration_voucher'=> $contact->name.' - Opening Balance Amount',
                        'referable_type'=> null,
                        'referable_id'=> null,
                        'is_invoiced'=> 0,
                        'is_manual_entry'=> 0,
            
                        'debit_account_id'=> $debit_account_id,
                        'debit_sub_account_id'=> $debit_partner_id,
                        'debit_cash_flow_account_id'=> $debit_cash_flow_account_id,
                        'debit_account_amount'=> $debit_amounts,
                        'debit_narration'=> $debit_narration,
                        'is_approve' => 1,
                        'sale_or_purchase' => null,
                        'ref_no' => null,
                    ]);
                }
            } else {
                $chart_account = new ChartAccount;
                $chart_account->level = 2;
                $chart_account->is_group = 0;
                $chart_account->name = $contact->name;
                $chart_account->description = null;
                $chart_account->configuration_group_id = null;
                $chart_account->status = 1;
                if ($contact->contact_type == "Supplier") {
                    $chart_account->parent_id = 8;
                    $chart_account->type = 2;
                } else {
                    $chart_account->parent_id = 5;
                    $chart_account->type = 1;
                }
                $chart_account->contactable_type = get_class(new ContactModel);
                $chart_account->contactable_id = $contact->id;
                $chart_account->save();

                $data = ChartAccount::findOrFail($chart_account->id)->update(['code' => '0' . $chart_account->type . '-' . sprintf("%02d",$chart_account->parent_id) . '-' . $chart_account->id]);
                if($contact->opening_balance != null && $contact->opening_balance > 0){
                    $repo = new OpeningBalanceHistoryRepository();
                    if ($contact->contact_type == "Supplier") {
                        $repo->createForUser([
                            'time_period_id' => TimePeriodAccount::where('is_closed', 0)->latest()->first()->id,
                            'liability_account_id' => $chart_account->id,
                            'liability_amount' => $contact->opening_balance,
                            'date' => Carbon::now()->format('Y-m-d'),
                        ]);
                        $repo->createForUser([
                            'time_period_id' => TimePeriodAccount::where('is_closed', 0)->latest()->first()->id,
                            'liability_account_id' => ChartAccount::where('code', '02-09-11')->first()->id,
                            'liability_amount' => '-' . $contact->opening_balance,
                            'date' => Carbon::now()->format('Y-m-d'),
                        ]);
                    } else {
                        $repo->createForUser([
                            'asset_account_id' => $chart_account->id,
                            'asset_amount' => $contact->opening_balance,
                            'date' => Carbon::now()->format('Y-m-d'),
                            'time_period_id' => TimePeriodAccount::where('is_closed', 0)->latest()->first()->id,
                            'liability_account_id' => ChartAccount::where('code', '02-09-11')->first()->id,
                            'liability_amount' => $contact->opening_balance,
                        ]);

                    }
                    $repo->createForHistory([
                        'account_id' => $chart_account->id,
                        'type' => strtolower($contact->contact_type),
                        'amount' => $contact->opening_balance,
                    ]);
                }
            }
        }
    }

    public function rules(): array
   {
       return [
            // 'contact_type' => 'required',
            // 'email' => 'required|unique:contacts,email',
            // 'mobile' => 'required|unique:contacts,mobile',
       ];
   }
   public function customValidationMessages()
   {
       return [
           'email.unique' => 'Email has already been taken.',
           'mobile.unique' => 'Mobile Name has already been taken',
       ];
   }
} 