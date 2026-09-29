<?php

namespace Modules\Contact\Repositories;

use Brian2694\Toastr\Facades\Toastr;
use Modules\ProAccount\Entities\SubLeadger;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Contact\Exports\ContactExports;
use Illuminate\Validation\ValidationException;
use Modules\ProAccount\Repositories\JournalRepository as ProJournalRepository;
use Modules\Account\Repositories\OpeningBalanceHistoryRepository;
use Modules\Contact\Entities\ContactModel;
use Illuminate\Support\Arr;
use App\Traits\ImageStore;
use App\Traits\SendMail;
use Modules\Account\Entities\ChartAccount;
use Modules\Account\Repositories\OpeningBalanceHistoryRepositoryInterface;
use Modules\Account\Entities\TimePeriodAccount;
use Carbon\Carbon;
use Modules\Sale\Entities\Sale;
use Modules\Purchase\Entities\ProductItemDetail;
use Modules\Purchase\Entities\PurchaseOrder;
use Modules\Contact\Imports\ContactModelImport;
use App\User;

class ContactRepository implements ContactRepositoriesInterface
{
    use ImageStore, SendMail;

    public function all()
    {
        return ContactModel::all();
    }

    public function serachBasedSupplier($search_keyword)
    {
        return ContactModel::whereLike(['business_name', 'name', 'email', 'mobile', 'tax_number'], $search_keyword)->supplier()->get();
    }

    public function serachBasedCustomer($search_keyword)
    {
        return ContactModel::whereLike(['business_name', 'name', 'email', 'mobile', 'tax_number'], $search_keyword)->customer()->get();
    }

    public function csvDownload($type, $data)
    {
        if ($type == "customer") {
            if (file_exists(public_path("uploads/csv/customer_list.xlsx"))) {
                unlink(public_path("uploads/csv/customer_list.xlsx"));
            }
            return Excel::store(new ContactExports($data), 'uploads/csv/customer_list.xlsx', 'public_folder');
        } else {
            if (file_exists(public_path("uploads/csv/supplier_list.xlsx"))) {
                unlink(public_path("uploads/csv/supplier_list.xlsx"));
            }
            return Excel::store(new ContactExports($data), 'uploads/csv/supplier_list.xlsx', 'public_folder');
        }
    }

    public function withPaginate($row_count, $quick_search, $name, $sort, $column, $type, $relational_data = [], $selected_data = ['*'])
    {
        $items = ContactModel::query();
        $items = $items->with($relational_data);
        if ($quick_search != null) {
            $items = $items->whereLike(['name', 'contact_id'], $quick_search);
        }
        if ($name != null) {
            $items = $items->whereLike(['name', 'contact_id'], $name);
        }
        if ($type == "customer") {
            $items = $items->customer();
        } else {
            $items = $items->supplier();
        }
        if ($row_count == "all") {
            if ($type == "customer") {
                $total_number = ContactModel::customer()->count();
            } else {
                $total_number = ContactModel::supplier()->count();
            }

            if ($column != null) {
                return $items->orderBy($column, $sort)->paginate($total_number, $selected_data);
            } else {
                return $items->latest()->paginate($total_number, $selected_data);
            }
        } else {
            if ($column != null) {
                return $items->orderBy($column, $sort)->paginate($row_count, $selected_data);
            } else {
                return $items->latest()->paginate($row_count, $selected_data);
            }
        }
    }

    public function create(array $data)
    {

        $contact = new ContactModel();
        if (isset($data['file'])) {
            $data = Arr::add($data, 'avatar', $this->saveAvatar($data['file']));
        }
        $data['opening_balance'] = $data['opening_balance'] ?? 0;

        $contact->fill($data)->save();
        if (in_array($contact->contact_type, ['Supplier', 'Customer'])) {
            if (app('general_setting')->first()->contact_login) {
                $user = new User();
                $user->name = $data['name'];
                $user->avatar = $data['avatar'] ?? '';
                $user->email = $data['email'];
                $user->is_active = 1;
                $user->password = bcrypt($data['password']);
                if ($contact->contact_type == "Supplier") {
                    $user->role_id = 4;
                } else {
                    $user->role_id = 5;
                }
                $user->contact_id = $contact->id;
                $user->save();
                $contact->user_id = $user->id;
                $contact->save();
            }

            if (moduleStatusCheck('ProAccount') && Settings('current_active_accounting') == "pro") {
                $this->partnerAccountCreate($contact);
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

                $data = ChartAccount::findOrFail($chart_account->id)->update(['code' => '0' . $chart_account->type . '-' . sprintf("%02d", $chart_account->parent_id) . '-' . $chart_account->id]);
                if ($contact->opening_balance != null && $contact->opening_balance > 0) {
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
        return $contact;
    }

    private function partnerAccountCreate($item)
    {
        $sub_leadger = Subleadger::create([
            'leadger_id' => (strtolower($item->contact_type) == "supplier") ? Settings('account_payable') : Settings('account_recievable'),
            'code' => $item->contact_id,
            'name' => $item->name,
            'morphable_type' => get_class($item),
            'morphable_id' => $item->id,
            'description' => $item->contact_type.' Account Created'
        ]);
        if ($item->opening_balance != null && $item->opening_balance > 0) {
            if (strtolower($item->contact_type) == "supplier") {
                $debit_amounts[] = null;
                $debit_account_id[] = null;
                $debit_partner_id[] = null;
                $debit_narration[] = null;
                $debit_cash_flow_account_id[] = 0;

                $credit_amounts[] = $item->opening_balance;
                $credit_account_id[] = Settings('account_payable');
                $credit_partner_id[] = $sub_leadger->id;
                $credit_narration[] = $item->name.' - Opening Balance Amount';
                $credit_cash_flow_account_id[] = 0;
            } else {
                $debit_amounts[] = $item->opening_balance;
                $debit_account_id[] = Settings('account_recievable');
                $debit_partner_id[] = $sub_leadger->id;
                $debit_narration[] = $item->name.' - Opening Balance Amount';
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
                'amount'=> $item->opening_balance,
                'date'=> Carbon::now()->format('Y-m-d'),
                'credit_account_id'=> $credit_account_id,
                'credit_sub_account_id'=> $credit_partner_id,
                'credit_cash_flow_account_id'=> $credit_cash_flow_account_id,
                'credit_account_amount'=> $credit_amounts,
                'credit_narration'=> $credit_narration,
                'narration_voucher'=> $item->name.' - Opening Balance Amount',
                'referable_type'=> null,
                'referable_id'=> null,
                'is_invoiced'=> 0,
                'is_manual_entry'=> 1,
    
                'debit_account_id'=> $debit_account_id,
                'debit_sub_account_id'=> $debit_partner_id,
                'debit_cash_flow_account_id'=> $debit_cash_flow_account_id,
                'debit_account_amount'=> $debit_amounts,
                'debit_narration'=> $debit_narration,
                'is_approve' => 1,
                'sale_or_purchase' => 'opening',
                'ref_no' => null,
            ]);
        }
    }

    public function find($id)
    {
        return ContactModel::findOrFail($id);
    }

    public function update(array $data, $id)
    {

        $contact = ContactModel::findOrFail($id);
        if (isset($data['file'])) {
            if (File::exists($contact->avatar)) {
                File::delete($contact->avatar);
            }
            $data = Arr::add($data, 'avatar', $this->saveAvatar($data['file']));
        }
        $contact->update($data);
        if(app('general_setting')->first()->contact_login){
            $user = User::find($contact->user_id);
            if(!$user){
                $user = new User();
            }
            $user->name = $data['name'];
            $user->photo = $data['avatar'] ?? '';
            $user->avatar = $data['avatar'] ?? '';
            $user->email = $data['email'];
            if(isset($data['password']) and $data['password']){
                $user->password = bcrypt($data['password']);
            }

            if ($contact->contact_type == "Supplier") {
                $user->role_id = 4;
            } else {
                $user->role_id = 5;
            }


            $user->contact_id = $contact->id;

            $user->save();

            $contact->user_id = $user->id;
            $contact->save();


        }

        return $contact;
    }
    public function updateProfile(array $data, $id)
    {
        $contact = ContactModel::findOrFail($id);
        if (isset($data['file'])) {
            if (File::exists($contact->avatar)) {
                File::delete($contact->avatar);
            }
            $data = Arr::add($data, 'avatar', $this->saveAvatar($data['file']));
        }

        $contact->update($data);

        $user = User::findOrFail($contact->user_id);
        $user->name = $data['name'];
        $user->photo = $data['avatar'] ?? '';
        $user->avatar = $data['avatar'] ?? '';
        $user->email = $data['email'];
        $user->save();

        $contact->user_id = $user->id;
        $contact->save();

        return $contact;
    }

    public function delete($id)
    {
        $contact = ContactModel::findOrFail($id);
        if ($contact->contact_type == 'Customer'){
            if ($contact->sales->count()){
                Toastr::error('Contact has Invoice');
                return;
            }
        } if ($contact->contact_type == 'Supplier'){
                if ($contact->purchases->count()){
                    Toastr::error('Supplier has Purchase');
                    return;
                }
        }
        Toastr::success(__('contact.Contact Deleted Successfully'));

        if ($contact->user_id){
            User::find($contact->user_id)->delete();
        }
        $contact->delete();

    }

    public function supplier()
    {
        return ContactModel::supplier()->latest()->get();
    }

    public function aciveSupplier()
    {
        return ContactModel::supplier()->where('is_active', 1)->get();
    }

    public function customer()
    {
        return ContactModel::customer()->latest()->get();
    }

    public function witoutWalkInCustomer()
    {
        return ContactModel::witoutWalkInCustomer()->get();
    }

    public function posCustomer()
    {
        return ContactModel::where('is_active', 1)->where('contact_type', 'Customer')->get();
    }

    public function statusChange(array $data)
    {
        $contact = ContactModel::findOrFail($data['id']);
        $contact->is_active = $data['status'];
        $contact->save();

        $user = $contact->user;
        if($user){
            $user->is_active = $data['status'];
            $user->save();
        }
    }

    public function customerSaleHistory($id)
    {
        $saleHistory = ProductItemDetail::with('itemable','productSku.product')->whereHasMorph('itemable', [Sale::class], function($query) use ($id){
            $query->where('customer_id', $id);
        })->get();

        return $saleHistory;
    }

    public function supplierPurchaseHistory($id)
    {
        $purchaseHistory = ProductItemDetail::with('itemable','productSku.product')->whereHasMorph('itemable', [PurchaseOrder::class], function($query) use ($id){
            $query->where('supplier_id', $id);
        })->get();

        return $purchaseHistory;
    }

    public function csv_contact_upload($data)
    {
        if (!empty($data['file'])) {
            $fileName = time() . '_' . $data['file']->getClientOriginalName();
            request()->file('file')->storeAs('reports', $fileName, 'public');

            Excel::import(new ContactModelImport, request()->file('file'));
        }
    }

    public function create_chart_account($contact)
    {
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

    public function listForSelectSupplier($request)
    {
        if ($request->search != '') {
            $items = ContactModel::where("contact_type", "Supplier")
                ->whereLike(['business_name', 'name', 'email', 'mobile', 'tax_number'], $request->search)
                ->paginate(10);
        } else {
            $items = ContactModel::where("contact_type", "Supplier")->paginate(10);
        }

        $response = [];
        foreach ($items as $item) {
            if (($item->mobile != null)) {
                $mobile = " (".$item->mobile.")";
            } else {
                $mobile = "";
            }
            $response[]  = [
                'id'    => $item->id,
                'text'  => $item->name.$mobile
            ];
        }
        $data['results'] =  $response;
        if ($items->count() > 0) {
            $data['pagination'] =  ["more" => true];
        }
        return $data;
    }

    public function listForSelectCustomer($request)
    {
        if ($request->search != '') {
            $items = ContactModel::where("contact_type", "Customer")
                ->whereLike(['business_name', 'name', 'email', 'mobile', 'tax_number'], $request->search)
                ->paginate(10);
        } else {
            $items = ContactModel::where("contact_type", "Customer")->paginate(10);
        }

        $response = [];
        foreach ($items as $item) {
            if (($item->mobile != null)) {
                $mobile = " (".$item->mobile.")";
            } else {
                $mobile = "";
            }
            $response[]  = [
                'id'    => 'customer-'.$item->id,
                'text'  => $item->name.$mobile
            ];
        }
        $data['results'] =  $response;
        if ($items->count() > 0) {
            $data['pagination'] =  ["more" => true];
        }
        return $data;
    }

    public function listForSelectCustomerWithID($request)
    {
        if ($request->search != '') {
            $items = ContactModel::where("contact_type", "Customer")
                ->whereLike(['business_name', 'name', 'email', 'mobile', 'tax_number'], $request->search)
                ->paginate(10);
        } else {
            $items = ContactModel::where("contact_type", "Customer")->paginate(10);
        }

        $response = [];
        foreach ($items as $item) {
            if (($item->mobile != null)) {
                $mobile = " (".$item->mobile.")";
            } else {
                $mobile = "";
            }
            $response[]  = [
                'id'    => $item->id,
                'text'  => $item->name.$mobile
            ];
        }
        $data['results'] =  $response;
        if ($items->count() > 0) {
            $data['pagination'] =  ["more" => true];
        }
        return $data;
    }
}
