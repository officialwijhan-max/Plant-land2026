<?php



namespace App\Repositories;



use App\User;

use App\Staff;

use Carbon\Carbon;

use App\StaffDocument;

use App\Traits\ImageStore;

use Illuminate\Support\Arr;

use App\Imports\StaffImport;

use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\File;

use Illuminate\Support\Facades\Hash;

use Modules\Leave\Entities\LeaveDefine;

use Modules\RolePermission\Entities\Role;

use Modules\Account\Entities\ChartAccount;

use Modules\Setting\Model\BusinessSetting;

use Illuminate\Foundation\Auth\RegistersUsers;

use Modules\Account\Entities\TimePeriodAccount;

use Modules\Account\Repositories\OpeningBalanceHistoryRepository;

use Modules\Account\Repositories\OpeningBalanceHistoryRepositoryInterface;

use Modules\Leave\Entities\ApplyLeave;

use Maatwebsite\Excel\Facades\Excel;

use App\Exports\StaffExport;

use App\Exports\CarryForwardExport;

use Modules\ProAccount\Repositories\JournalRepository as ProJournalRepository;

use Modules\ProAccount\Entities\SubLeadger;



class UserRepository implements  UserRepositoryInterface

{

    use ImageStore;



    public function user()

    {

        return User::with('leaves','leaveDefines')->latest()->get();

    }



    public function csvDownloadCarryForward()

    {

        if (file_exists(public_path("uploads/csv/carryForward.xlsx"))) {

            unlink(public_path("uploads/csv/carryForward.xlsx"));

        }

        return Excel::store(new CarryForwardExport, 'uploads/csv/carryForward.xlsx', 'public_folder');

    }



    public function withPaginateCarryForward($row_count, $quick_search, $name, $sort, $column)

    {

        $items = User::query();

        $items = $items->with('leaves', 'leaveDefines');

        if ($quick_search != null) {

            $items = $items->whereLike(['name', 'email', 'username'], $quick_search);

        }

        $items = $items->whereNotIn('role_id', [1, 4, 5]);

        if ($row_count == "all") {

            $total_number = User::count();



            if ($column != null) {

                return $items->orderBy($column, $sort)->paginate($total_number);

            } else {

                return $items->latest()->paginate($total_number);

            }

        } else {

            if ($column != null) {

                return $items->orderBy($column, $sort)->paginate($row_count);

            } else {

                return $items->latest()->paginate($row_count);

            }

        }

    }



    public function csvDownload($data)

    {

        if (file_exists(public_path("uploads/csv/staff-list.xlsx"))) {

            unlink(public_path("uploads/csv/staff-list.xlsx"));

        }

        return Excel::store(new StaffExport($data), 'uploads/csv/staff-list.xlsx', 'public_folder');

    }



    public function withPaginate($row_count, $quick_search, $name, $sort, $column, $relational_data = [], $selected_data = ['*'])

    {

        $items = Staff::query();

        $items = $items->with($relational_data);

        if ($quick_search != null) {

            $items = $items->whereLike(['employee_id', 'phone'], $quick_search)

                ->orWhereHas('user', function ($q) use ($quick_search) {

                    return $q->whereLike(['name', 'email', 'username'], $quick_search);

                });

        }

        if ($row_count == "all") {

            $total_number = Staff::with('user')->count();



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



    public function getUserList($request)

    {

        if ($request->search != '') {

            $items = User::with(['staff:employee_id,id,user_id'])->whereHas('staff')

                                    ->whereLike(['name', 'email'], $request->search)

                                    ->paginate(10);

        } else {

            $items = User::with(['staff:employee_id,id,user_id'])->whereHas('staff')->paginate(10);

        }



        $response = [];

        foreach ($items as $item) {

            $response[]  = [

                'id'    => $item->id,

                'text'  => '('.$item->staff->employee_id.') '.$item->name

            ];

        }

        $data['results'] =  $response;

        if ($items->count() > 0) {

            $data['pagination'] =  ["more" => true];

        }

        return $data;

    }



    public function all($relational_keyword = [])

    {

        if (count($relational_keyword) > 0) {

            return Staff::with($relational_keyword)->latest()->get();

        }else {

            return Staff::latest()->get();

        }



    }



    public function create(array $data)

    {

        $user = User::create($data);



        if(BusinessSetting::where('type', 'email_verification')->first()->status != 1){

            $user->email_verified_at = date('Y-m-d H:m:s');

            $user->save();

        }

        else {

            $user->sendEmailVerificationNotification();

        }

        $staff = new Staff;

        $staff->user_id = $user->id;

        $staff->save();

        if (moduleStatusCheck('ProAccount') && Settings('current_active_accounting') == "pro") {

            $sub_leadger = Subleadger::create([

                'leadger_id' => Settings('leadger_account_for_employee') ? Settings('leadger_account_for_employee') : 0,

                'code' => 'Emp-' . sprintf("%06d", $staff->id),

                'name' => $user->name . ' ' . sprintf("%03d", $staff->id),

                'morphable_type' => get_class($staff),

                'morphable_id' => $staff->id,

                'description' => 'Employee Account Created when added Employee'

            ]);

        } else{

            $chart_account = new ChartAccount;

            $chart_account->level = 2;

            $chart_account->is_group = 0;

            $chart_account->name = $staff->user->name;

            $chart_account->description = null;

            $chart_account->parent_id = 6;

            $chart_account->status = 1;

            $chart_account->configuration_group_id = 4;

            $chart_account->type = 1;

            $chart_account->contactable_type = "App\User";

            $chart_account->contactable_id = $user->id;

            $chart_account->save();

            ChartAccount::findOrFail($chart_account->id)->update(['code' => '0' . $chart_account->type . '-' . $chart_account->parent_id . '-' . $chart_account->id]);

        }

        return $staff;

    }



    public function store(array $data)

    {

        $role = explode('-', $data['role_id']);

        $user = new User;

        $user->name = $data['name'];

        $user->email = $data['email'];

        $user->username = $data['username'] ?? null;

        $user->role_id = $role[0];

        if (isset($data['photo'])) {

            $data = Arr::add($data, 'avatar', $this->saveAvatar($data['photo']));

            $user->avatar = $data['avatar'];

        }

        if (isset($data['signature_photo'])) {

            $data = Arr::add($data, 'signature', $this->saveAvatar($data['signature_photo'],120,60));

            $user->signature = $data['signature'];

        }

        $user->password = Hash::make($data['password']);

        if($user->save()){

            $staff = new Staff;

            $staff->user_id = $user->id;

            $staff->department_id = $data['department_id'];

            // $staff->showroom_id = $data['showroom_id'];
            $staff->branches = json_encode($data['branches']);
            $staff->warehouses = json_encode($data['warehouses'] ?? []);

            // $staff->warehouse_id = (!empty($data['warehouse_id'])) ? $data['warehouse_id'] : null;

            $staff->phone = $data['username'] ?? null;

            if ($role[1] != "system_user") {

                $staff->opening_balance = $data['opening_balance'];

                $staff->bank_name = $data['bank_name'];

                $staff->bank_branch_name = $data['bank_branch_name'];

                $staff->bank_account_name = $data['bank_account_name'];

                $staff->bank_account_no = $data['bank_account_no'];

                $staff->basic_salary = $data['basic_salary'] ?? 0 ;

                $staff->employment_type = $data['employment_type']?? 'Permanent';

                $staff->date_of_joining = isset($data['date_of_joining']) ? Carbon::parse($data['date_of_joining'])->format('Y-m-d') : date('Y-m-d');

                if (!empty($data['provisional_months'])) {

                    $staff->provisional_months = $data['provisional_months'];

                }

                $staff->date_of_birth = Carbon::parse($data['date_of_birth'])->format('Y-m-d');

                $staff->leave_applicable_date = Carbon::parse($data['leave_applicable_date'])->format('Y-m-d');

                $staff->current_address = $data['current_address'];

                $staff->permanent_address = $data['permanent_address'];

            }

            if($staff->save()){

                if(BusinessSetting::where('type', 'email_verification')->first()->status != 1){

                    $user->email_verified_at = date('Y-m-d H:m:s');

                    $user->save();

                }

                else {

                    $user->sendEmailVerificationNotification();

                }

            }

            if ($role[1] != "system_user") {

                if (moduleStatusCheck('ProAccount') && Settings('current_active_accounting') == "pro") {

                    $sub_leadger = Subleadger::create([

                        'leadger_id' => Settings('leadger_account_for_employee') ? Settings('leadger_account_for_employee') : 0,

                        'code' => 'Emp-' . sprintf("%06d", $staff->id),

                        'name' => $user->name . ' ' . sprintf("%03d", $staff->id),

                        'morphable_type' => get_class($staff),

                        'morphable_id' => $staff->id,

                        'description' => 'Employee Account Created when added Employee'

                    ]);



                    if ($staff->opening_balance != null || $staff->opening_balance > 0) {

                        $debit_amounts[] = null;

                        $debit_account_id[] = null;

                        $debit_partner_id[] = null;

                        $debit_narration[] = null;

                        $debit_cash_flow_account_id[] = 0;



                        $credit_amounts[] = $staff->opening_balance;

                        $credit_account_id[] = Settings('leadger_account_for_employee');

                        $credit_partner_id[] = $sub_leadger->id;

                        $credit_narration[] = $user->name.' - Opening Balance Amount Staff';

                        $credit_cash_flow_account_id[] = 0;



    

                        $journalRecieveRepository = new ProJournalRepository();

                        $voucher = $journalRecieveRepository->create([

                            'type' => "misc",

                            'is_cash_flow_journal' => 0,

                            'amount'=> $staff->opening_balance,

                            'date'=> Carbon::now()->format('Y-m-d'),

                            'credit_account_id'=> $credit_account_id,

                            'credit_sub_account_id'=> $credit_partner_id,

                            'credit_cash_flow_account_id'=> $credit_cash_flow_account_id,

                            'credit_account_amount'=> $credit_amounts,

                            'credit_narration'=> $credit_narration,

                            'narration_voucher'=> $user->name.' - Opening Balance Amount Staff',

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

                } else{

                    $chart_account = new ChartAccount;

                    $chart_account->level = 2;

                    $chart_account->is_group = 0;

                    $chart_account->name = $staff->user->name;

                    $chart_account->description = null;

                    $chart_account->parent_id = 9;

                    $chart_account->status = 1;

                    $chart_account->configuration_group_id = 4;

                    $chart_account->type = 1;

                    $chart_account->contactable_type = "App\User";

                    $chart_account->contactable_id = $user->id;

                    $chart_account->save();

                    ChartAccount::findOrFail($chart_account->id)->update(['code' => '0' . $chart_account->type . '-' . $chart_account->parent_id . '-' . $chart_account->id]);

                    if ($staff->opening_balance != null || $staff->opening_balance > 0) {



                        $repo = new OpeningBalanceHistoryRepository;

                        $repo->createForUser([

                            'asset_account_id' => $chart_account->id,

                            'asset_amount' => $staff->opening_balance,

                            'date' => Carbon::now()->format('Y-m-d'),

                            'time_period_id' => TimePeriodAccount::latest()->first()->id,

                            'liability_account_id' => ChartAccount::where('code', '02-09')->first()->id,

                            'liability_amount' => $staff->opening_balance,

                        ]);

                        $repo->createForHistory([

                            'account_id' => $chart_account->id,

                            'type' => 'staff',

                            'amount' => $staff->opening_balance,

                        ]);

                    }

                }

                return $staff;

            }

            return $staff;

        }

    }



    public function find($id)

    {

        return Staff::with('user')->findOrFail($id);

    }



    public function findUser($id)

    {

        return User::findOrFail($id);

    }



    public function findDocument($id)

    {

        return StaffDocument::where('staff_id', $id)->get();

    }



    public function update(array $data, $id)

    {

                if ($id == 1){

                    $role = [1, 'system_user'];

                } else{

                    $role = explode('-', $data['role_id']);

                }

            



        $user = User::findOrFail($id);

        $staff = $user->staff;

            if (isset($data['photo'])) {

                $data = Arr::add($data, 'avatar', $this->saveAvatar($data['photo']));

                $user->avatar = $data['avatar'];

            }

            if (isset($data['signature_photo'])) {

                $data = Arr::add($data, 'signature', $this->saveAvatar($data['signature_photo'],120,60));

                $user->signature = $data['signature'];

            }



            if (isset($data['password'])) {

                $user->password = Hash::make($data['password']);

            }

            $user->name = $data['name'];

            $user->username = $data['username'] ?? null;

            $user->email = $data['email'] ?? null;

            $user->role_id = $role[0];

            $result = $user->save();

            if($result){

                $staff->user_id = $user->id;

                $staff->department_id = $data['department_id'];

                if ($role[1] != "system_user") {

                    $staff->opening_balance = $data['opening_balance'];

                    $staff->bank_name = $data['bank_name'];

                    $staff->bank_branch_name = $data['bank_branch_name'];

                    $staff->bank_account_name = $data['bank_account_name'];

                    $staff->bank_account_no = $data['bank_account_no'];

                    $staff->basic_salary = $data['basic_salary'];

                    $staff->employment_type = $data['employment_type'];

                    $staff->date_of_joining = Carbon::parse($data['date_of_joining'])->format('Y-m-d');

                    if (!empty($data['provisional_months'])) {

                        $staff->provisional_months = $data['provisional_months'];

                    }

                    $staff->date_of_birth = Carbon::parse($data['date_of_birth'])->format('Y-m-d');

                    $staff->current_address = $data['current_address'];

                    $staff->permanent_address = $data['permanent_address'];

                }

                $staff->branches = json_encode($data['branches']);
                $staff->warehouses = json_encode($data['warehouses'] ?? []);

                // $staff->showroom_id = $data['showroom_id'];

                // $staff->warehouse_id = (!empty($data['warehouse_id'])) ? $data['warehouse_id'] : null;

                $staff->phone = $data['username'] ?? null;

          



                $staff->save();

            }

        return $staff;

    }



    public function updateProfile(array $data, $id)

    {

        $user = User::findOrFail($id);

        if (isset($data['avatar'])) {

            $user->avatar = $this->saveAvatar($data['avatar'],60,60);

        }

        $user->name = $data['name'];

         if(isset($data['password']) and $data['password']){

            $user->password = Hash::make($data['password']);

        }



        $result = $user->save();

        $staff = $user->staff;

        if($result){

            $staff->phone = $data['phone'];

            if ($user->role_id != 1) {

                $staff->bank_name = $data['bank_name'];

                $staff->bank_branch_name = $data['bank_branch_name'];

                $staff->bank_account_name = $data['bank_account_name'];

                $staff->bank_account_no = $data['bank_account_no'];

                $staff->current_address = $data['current_address'];

                $staff->permanent_address = $data['permanent_address'];

            }



            $staff->save();

        }

        return $staff;

    }



    public function delete($id)

    {

        $user = User::findOrFail($id);

        if (File::exists($user->avatar)) {

            File::delete($user->avatar);

        }

        if ($user->staff){

            if ($user->staff->payrolls){

                $user->staff->payrolls()->delete();

            }

            $user->staff->delete();

        }

        $applys=ApplyLeave::where('user_id',$user->id)->get();



        if($applys){

            foreach($applys as $apply_Leave){

                ApplyLeave::destroy($apply_Leave->id);

            }

        }

        $leave_defines=LeaveDefine::where('role_id',$user->role_id)->where('user_id',$user->id)->get();

        if($leave_defines){

            foreach($leave_defines as $leave_define){

                LeaveDefine::destroy($$leave_define->id);

            }

        }





        $user->delete();

    }



    public function statusUpdate($data)

    {

        $user = User::find($data['id']);

        $user->is_active = $data['status'];

        $user->save();

    }



    public function deleteStaffDoc($id)

    {

        $document = StaffDocument::findOrFail($id)->delete();

    }



    public function normalUser()

    {

        $normal_roles_id = Role::where('type', 'regular_user')->pluck('id');

        return User::where('id',Auth::id())->orwhereIn('role_id',$normal_roles_id)->get()->except(1);

    }



    public function roleUsers($role_id)

    {

        return User::where('role_id', $role_id)->where('is_active',1)->get();

    }



    public function csv_upload_staff($data)

    {

        if (!empty($data['file'])) {

            $fileName = time() . '_' . $data['file']->getClientOriginalName();

            request()->file('file')->storeAs('reports', $fileName, 'public');



            Excel::import(new StaffImport, request()->file('file'));

        }

    }



    public function create_chart_account($user, $staff)

    {

        $chart_account = new ChartAccount;

        $chart_account->level = 2;

        $chart_account->is_group = 0;

        $chart_account->name = $staff->user->name;

        $chart_account->description = null;

        $chart_account->parent_id = 9;

        $chart_account->status = 1;

        $chart_account->configuration_group_id = 4;

        $chart_account->type = 1;

        $chart_account->contactable_type = "App\User";

        $chart_account->contactable_id = $user->id;

        $chart_account->save();

        ChartAccount::findOrFail($chart_account->id)->update(['code' => '0' . $chart_account->type . '-' . $chart_account->parent_id . '-' . $chart_account->id]);

        if ($staff->opening_balance != null || $staff->opening_balance > 0) {



            $repo = new OpeningBalanceHistoryRepository;

            $repo->createForUser([

                'asset_account_id' => $chart_account->id,

                'asset_amount' => $staff->opening_balance,

                'date' => Carbon::now()->format('Y-m-d'),

                'time_period_id' => TimePeriodAccount::latest()->first()->id,

                'liability_account_id' => ChartAccount::where('code', '02-09')->first()->id,

                'liability_amount' => $staff->opening_balance,

            ]);

            $repo->createForHistory([

                'account_id' => $chart_account->id,

                'type' => 'staff',

                'amount' => $staff->opening_balance,

            ]);

        }

    }

}

