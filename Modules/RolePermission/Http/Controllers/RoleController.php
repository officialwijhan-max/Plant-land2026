<?php

namespace Modules\RolePermission\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\RolePermission\Entities\Role;
use Modules\RolePermission\Http\Requests\RoleFormRequest;
use Modules\RolePermission\Repositories\RoleRepositoryInterface;
use Toastr;
use App\Repositories\UserRepository;

class RoleController extends Controller
{
    protected $roleRepository;

    public function __construct(RoleRepositoryInterface $roleRepository)
    {
        $this->middleware(['auth']);
        $this->roleRepository = $roleRepository;
    }

    public function index(Request $request)
    {
        try{
            $row_count = ($request->has('row')) ? $request->row : 10 ;
            $sort = ($request->has('sort')) ? $request->sort : 'desc' ;
            $column = ($request->has('col')) ? $request->col : null ;
            $quick_search = ($request->has('quick_search')) ? $request->quick_search : null ;
            $name = ($request->has('name')) ? $request->name : null ;
            $data['items'] = $this->roleRepository->withPaginate($row_count,$quick_search,$name,$sort,$column);
            if ($request->ajax()) {
                return view('rolepermission::paginates.list', $data);
            }
            if ($request->has("import_as")) {
                set_time_limit(-1);
                $data['items'] = $this->roleRepository->withPaginate("all",$quick_search,$name,$sort,$column);
                if ($request->import_as == "print") {
                    return view('rolepermission::paginates.print', $data);
                }
                if ($request->import_as == "csv") {
                    $this->roleRepository->csvDownload($data);
                    $filePath = public_path("uploads/csv/role-list.xlsx");
                    $headers = ['Content-Type: text/csv'];
                    $fileName = time().'-role-list.xlsx';

                    return response()->download($filePath, $fileName, $headers);
                }
            }
            return view('rolepermission::role', $data);
        }catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            Toastr::error('Operation failed');
            return back();
        }
    }

    public function create()
    {
        return view('rolepermission::create');
    }

    public function store(RoleFormRequest $request)
    {
        try {
            $this->roleRepository->create($request->except("_token"));
            \LogActivity::successLog('New Role - ('.$request->name.') has been created.');
            Toastr::success(__('common.Role Create Successful'), __('common.Success'));
            return redirect()->route('permission.roles.index');
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage().' - Error has been detected for Role creation');
            return back();
        }
    }

    public function show($id)
    {
        return view('rolepermission::show');
    }

    public function edit(Role $role)
    {
        try {
            $data['items'] = $this->roleRepository->withPaginate(10,null,null,'asc','id');
            $data['role'] = $role;
            return view('rolepermission::role', $data);
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function update(RoleFormRequest $request, $id)
    {
        try {
            $role = $this->roleRepository->findRole($id);
            $default_roles = getVar('default_role');

            if (env('APP_SYNC') and in_array($role->name, $default_roles)){
                Toastr::error('Restricted in demo mode', 'Failed');
                return redirect()->back();
            }
            $role = $this->roleRepository->update($request->except("_token"), $id);
            Toastr::success(__('common.Role Update Successful'), __('common.Success'));
            \LogActivity::successLog($request->name.'- has been updated.');
            return redirect()->back();
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage().' - Error has been detected for Role update');
            return redirect()->route('permission.roles.index');
        }
    }

    public function destroy($id)
    {
        try {
            $delete = $this->roleRepository->delete($id);

            if ($delete){
                \LogActivity::successLog('A Role has been destroyed.');
                Toastr::success(__('common.Role Delete Successful'), __('common.Success'));
            } else{
                Toastr::error(__('common.Role is assign to staffs.'));
            }
            return redirect()->back();
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage().' - Error has been detected for Role Destroy');
            Toastr::error('Something went wrong');
            return redirect()->back();
        }
    }

    public function roleUsers(Request $request)
    {
        $repo = new UserRepository();
        $users = $repo->roleUsers($request->role_id);

        if (count($users) > 0)
        {
            $output ='<option value="">'.trans('common.Select One').'</option>';
            foreach ($users as $user)
            {
                $output .= '<option value="'.$user->id.'">'.$user->name.'</option>';
            }
        }
        else
            $output = '<option>'.trans('common.No data Found').'</option>';

        return $output;
    }
}
