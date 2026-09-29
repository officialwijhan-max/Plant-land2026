<?php

namespace Modules\Inventory\Http\Controllers;

use App\User;
use App\Traits\Notification;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Support\Renderable;
use Modules\Inventory\Http\Requests\WarehouseFormRequest;
use Modules\Inventory\Repositories\WareHouseRepositoryInterface;

class WareHouseController extends Controller
{
    use Notification;
    protected $wareHouseRepository;

    public function __construct(WareHouseRepositoryInterface $wareHouseRepository)
    {
        $this->middleware(['auth', 'verified']);
        $this->wareHouseRepository = $wareHouseRepository;
    }

    public function index(Request $request)
    {
        try{
            $row_count = ($request->has('row')) ? $request->row : 10;
            $sort = ($request->has('sort')) ? $request->sort : 'asc';
            $column = ($request->has('col')) ? $request->col : null;
            $quick_search = ($request->has('quick_search')) ? $request->quick_search : null;
            $name = ($request->has('name')) ? $request->name : null;
            $data['items'] = $this->wareHouseRepository->withPaginate($row_count, $quick_search, $name, $sort, $column, [], ['id', 'name', 'email', 'phone', 'address', 'status']);
            if ($request->ajax()) {
                return view('inventory::warehouses.list', $data);
            }
            if ($request->has("import_as")) {
                set_time_limit(-1);
                $data['items'] = $this->wareHouseRepository->withPaginate('all', $quick_search, $name, $sort, $column, [], ['id', 'name', 'email', 'phone', 'address', 'status']);
                if ($request->import_as == "print") {
                    return view('inventory::warehouses.print', $data);
                }
                if ($request->import_as == "csv") {
                    $this->wareHouseRepository->csvDownload($data);
                    $filePath = public_path("uploads/csv/warehouse_list.xlsx");
                    $headers = ['Content-Type: text/csv'];
                    $fileName = time() . '-warehouse_list.xlsx';

                    return response()->download($filePath, $fileName, $headers);
                }
            }
            return view('inventory::warehouses.index', $data);

        }catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            Toastr::error(__('common.Something Went Wrong'), __('common.Error'));
            return back();
        }
    }

    public function store(WarehouseFormRequest $request)
    {
        try {
            $warehouse = $this->wareHouseRepository->create($request->except("_token"));
            $users=User::whereIn('role_id',[1,2])->where('id','!=',auth()->user()->id)
                        ->where('is_active','1')
                        ->get(['id','role_id']);
            $role_id = $request->for_whom;
            $subject = $warehouse->name;
            $class = $warehouse;
            $data =  __('notification.A WareHouse Has been Created');
            $url = "showroom.index";
            $this->sendNotification($class,null,$subject,null,null,$data,$users,$role_id,$url);

            \LogActivity::successLog('New WareHouse - ('.$request->name.') has been created.');
            Toastr::success(__('inventory.WareHouse has been added Successfully'));
            return back();
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage().' - Error has been detected for WareHouse creation');
            Toastr::error(__('common.Something Went Wrong'), __('common.Error'));
            return back();
        }
    }


    public function edit(Request $request)
    {
        try {
            $warehouse = $this->wareHouseRepository->find($request->id);
            return view('inventory::warehouses.edit', [
                "warehouse" => $warehouse
            ]);
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            return $e->getMessage();
        }
    }

    public function update(WarehouseFormRequest $request, $id)
    {
        try {
            $warehouse = $this->wareHouseRepository->update($request->except("_token"), $id);
            \LogActivity::successLog($request->name.'- has been updated.');
            Toastr::success(__('inventory.WareHouse has been updated Successfully'));
            return redirect()->route('warehouse.index');
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage().' - Error has been detected for WareHouse update');
            Toastr::error(__('common.Something Went Wrong'), __('common.Error'));
            return back();
        }
    }

    public function destroy($id)
    {
        try {
            $warehouse = $this->wareHouseRepository->delete($id);
            \LogActivity::successLog('A WareHouse has been destroyed.');
            Toastr::success(__('inventory.WareHouse has been deleted Successfully'));
            return back();
        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage().' - Error has been detected for WareHouse Destroy');
            Toastr::error(__('common.Something Went Wrong'), __('common.Error'));
            return back();
        }
    }

}
