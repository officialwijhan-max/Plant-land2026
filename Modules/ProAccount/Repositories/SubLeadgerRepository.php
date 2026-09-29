<?php

namespace Modules\ProAccount\Repositories;

use App\User;
use Modules\Sales\Entities\Agent;
use Illuminate\Support\Arr;
use Modules\ProAccount\Entities\SubLeadger;
use Modules\ProAccount\Entities\Leadger;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Hash;
use update\Modules\ProAccount\Exports\SubLeadgerExport;
use Modules\Contact\Entities\ContactModel;
use Illuminate\Support\Facades\Event;
use Modules\ProAccount\Events\EventForContacts;
use update\Modules\ProAccount\Exports\PartnerAccountExport;

class SubLeadgerRepository
{
    public function getAll()
    {
        return SubLeadger::with('leadger')->select('name','code','is_active', 'leadger_id','id')->paginate(10);
    }
    public function all()
    {
        return SubLeadger::with('leadger')->latest()->get();
    }

    public function withPaginate($row_count,$quick_search,$name,$sort,$column,$relational_data = [], $selected_data = ['*'])
    {
        $sub_leadgers = SubLeadger::query();
        $sub_leadgers = $sub_leadgers->with($relational_data);
        if ($quick_search != null) {
            $sub_leadgers = $sub_leadgers->whereLike(['name'], $quick_search);
        }
        if ($name != null) {
            $sub_leadgers = $sub_leadgers->whereLike(['name'], $quick_search);
        }
        if ($row_count == "all") {
            $total_number = SubLeadger::count();

            if ($column != null) {
                return $sub_leadgers->orderBy($column, $sort)->paginate($total_number, $selected_data);
            }else {
                return $sub_leadgers->latest()->paginate($total_number, $selected_data);
            }
        }else {
            if ($column != null) {
                return $sub_leadgers->orderBy($column, $sort)->paginate($row_count, $selected_data);
            }else {
                return $sub_leadgers->latest()->paginate($row_count, $selected_data);
            }
        }
    }

    public function getPureRecievableSubLeadgerAll()
    {
        return SubLeadger::with('leadger')->where('morphable_type', null)->select('name','code','is_active', 'leadger_id','id')->get();
    }

    public function getActiveAll()
    {
        return SubLeadger::where('is_active', 1)->get(['name','id', 'code', 'leadger_id']);
    }

    public function csvDownloadRecievableAcc($leadger_id)
    {
        if (file_exists(public_path("uploads/csv/recievable_account.xlsx"))) {
          unlink(public_path("uploads/csv/recievable_account.xlsx"));
        }
        return Excel::store(new SubLeadgerExport($leadger_id, "account_recievable_list"), 'uploads/csv/recievable_account.xlsx', 'public_folder');
    }

    public function withPaginateRecievableAcc($row_count,$quick_search,$sort,$column,$relational_data = [], $selected_data = ['*'])
    {
        $items = SubLeadger::query();
        $items = $items->where('leadger_id', Settings('account_recievable'));
        if ($quick_search != null) {
            $items = $items->whereLike(['name', 'code'], $quick_search);
        }
        if ($row_count == "all") {
            $total_number = SubLeadger::count();

            if ($column != null) {
                return $items->orderBy($column, $sort)->paginate($total_number, $selected_data);
            }else {
                return $items->latest()->paginate($total_number, $selected_data);
            }
        }else {
            if ($column != null) {
                return $items->orderBy($column, $sort)->paginate($row_count, $selected_data);
            }else {
                return $items->latest()->paginate($row_count, $selected_data);
            }
        }
    }

    public function csvDownloadPayableAcc($leadger_id)
    {
        if (file_exists(public_path("uploads/csv/payable_account.xlsx"))) {
          unlink(public_path("uploads/csv/payable_account.xlsx"));
        }
        return Excel::store(new SubLeadgerExport($leadger_id, "account_payable_list"), 'uploads/csv/payable_account.xlsx', 'public_folder');
    }

    public function withPaginatePayableAcc($row_count,$quick_search,$sort,$column,$relational_data = [], $selected_data = ['*'])
    {
        $items = SubLeadger::query();
        $items = $items->where('leadger_id', Settings('account_payable'));
        if ($quick_search != null) {
            $items = $items->whereLike(['name', 'code'], $quick_search);
        }
        if ($row_count == "all") {
            $total_number = SubLeadger::count();

            if ($column != null) {
                return $items->orderBy($column, $sort)->paginate($total_number, $selected_data);
            }else {
                return $items->latest()->paginate($total_number, $selected_data);
            }
        }else {
            if ($column != null) {
                return $items->orderBy($column, $sort)->paginate($row_count, $selected_data);
            }else {
                return $items->latest()->paginate($row_count, $selected_data);
            }
        }
    }

    public function create(array $data)
    {
        if ($data['contact_type'] != "Retailer") {
            $contact = new ContactModel();

            $contact->fill($data)->save();
            $sub_leadger = Subleadger::create([
                'leadger_id' => (strtolower($contact->contact_type) == "supplier") ? Settings('account_payable') : Settings('account_recievable'),
                'code' => $contact->contact_id,
                'name' => $contact->name,
                'morphable_type' => get_class($contact),
                'morphable_id' => $contact->id,
                'description' => $contact->contact_type.' Account Created'
            ]);

            return $contact;
        }
        if ($data['contact_type'] == "Retailer") {
            $user = new User();
            $user->name = $data['name'];
            $user->role_id = 6;
            $user->email = ($data['email']) ? $data['email'] : Null;
            $user->username = $data['email'];
            $user->password = Hash::make(12345678);
            $user->save();

            $agent = new Agent();
            $agent->user_id = $user->id;

            $agent->phone = $data['mobile'];
            $agent->address = $data['address'] ?? null;
            $agent->save();

            Subleadger::create([
                'leadger_id' => Settings('account_recievable'),
                'code' => $user->name.' - '.sprintf("%05d", $user->id),
                'name' => $user->name,
                'morphable_type' => get_class($agent),
                'morphable_id' => $agent->id,
                'description' => 'Account Created By Instant Transaction'
            ]);

            return $agent;
        }
    }

    public function find($id,$relational_data = [], $selected_data = ['*'])
    {
        return SubLeadger::with($relational_data)->findOrFail($id,$selected_data);
    }

    public function update(array $data)
    {
        $sub_leadger = $this->find($data['account_id']);
        $leadger = Leadger::find($data['leadger_id']);
        if ($sub_leadger->leadger->type == $leadger->type) {
            $sub_leadger->update($data);
            return "done";
        }else {
            return "type_mismatch";
        }
    }

    public function delete($id)
    {
        $leadger = $this->find($id);
        if ($leadger->is_blocked) {
            return false;
        }else {
            if ($leadger->morphable_type != null) {
                $leadger->morphable->delete();
            }
            $leadger->delete();
            return true;
        }
    }

    public function subLeadgerByLeadger($id)
    {
        return SubLeadger::where('leadger_id', $id)->get(['id','name']);
    }

    public function getSubAccountByAjax($leadger_id, $search)
    {
        if ($search != '') {
            $items = SubLeadger::where('leadger_id', $leadger_id)->whereLike(['name', 'code'], $search)->paginate(10);
        } else {
            $items = SubLeadger::where('leadger_id', $leadger_id)->paginate(10);
        }

        $response = [];
        foreach($items as $item){
            $response[]  =[
                'id'    => $item->id,
                'text'  => $item->name
            ];
        }
        $data['results'] =  $response;
        if ($items->count() > 0)
        {
            $data['pagination'] =  ["more" => true];
        }
        return $data;
    }

    public function subLeadgerForSelect($search)
    {
        if ($search != '') {
            $items = SubLeadger::with(['transactions:id,sub_leadger_id,type,amount,is_approve'])->whereLike(['name','code'], $search)->paginate(10);
        } else {
            $items = SubLeadger::with(['transactions:id,sub_leadger_id,type,amount,is_approve'])->paginate(10);
        }

        $response = [];
        foreach($items as $item){
            $response[]  =[
                'id'    => $item->id,
                'text'  => '('.$item->code.') '.$item->name .' => '. single_price($item->BalanceAmount)
            ];
        }
        $data['results'] =  $response;
        if ($items->count() > 0)
        {
            $data['pagination'] =  ["more" => true];
        } else {
            $response[]  =[
                'id'    => "create_new",
                'text'  => '<span class="add_new_icon_for_select"><i class="fa fa-plus"></i></span> Add New <span class="add_new_font_for_select"> -> '.$search.'</span>'
            ];
            $data['results'] =  $response;
        }
        return $data;
    }

    public function subLeadgerForSupplierSelect($search)
    {
        $items = SubLeadger::query();
        if ($search != '') {
            $items = $items->whereLike(['name', 'code'], $search);
        }
        $items = $items->where('morphable_type', 'Modules\Purchase\Entities\Supplier')

                        ->paginate(10);
        $response = [];
        foreach($items as $item){
            $response[]  =[
                'id'    => $item->id,
                'text'  => '('.$item->code.') '.$item->name
            ];
        }
        $data['results'] =  $response;
        if ($items->count() > 0)
        {
            $data['pagination'] =  ["more" => true];
        }
        return $data;
    }

    public function csvDownloadLeadgers()
    {
        if (file_exists(public_path("uploads/csv/partner_account_list.xlsx"))) {
          unlink(public_path("uploads/csv/partner_account_list.xlsx"));
        }
        return Excel::store(new SubLeadgerExport(null, null), 'uploads/csv/partner_account_list.xlsx', 'public_folder');
    }

    public function csvDownloadPartnerAccount($data)
    {
        if (file_exists(public_path("uploads/csv/partner_account_list.xlsx"))) {
          unlink(public_path("uploads/csv/partner_account_list.xlsx"));
        }
        return Excel::store(new PartnerAccountExport($data), 'uploads/csv/partner_account_list.xlsx', 'public_folder');
    }
}
