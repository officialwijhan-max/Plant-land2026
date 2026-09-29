<?php

namespace Modules\Attendance\Http\Controllers;

use App\User;
use App\Traits\ImageStore;
use Illuminate\Support\Arr;
use App\Traits\Notification;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Modules\Attendance\Entities\Event;
use Illuminate\Contracts\Support\Renderable;
use Modules\Attendance\Repositories\EventRepositoryInterface;
use Modules\RolePermission\Repositories\RoleRepositoryInterface;

class EventController extends Controller
{
    use ImageStore,Notification;

    protected $eventRepository,$roleRepository;

    public function __construct(EventRepositoryInterface $eventRepository,RoleRepositoryInterface $roleRepository)
    {
        $this->eventRepository = $eventRepository;
        $this->roleRepository = $roleRepository;
    }

    public function index(Request $request)
    {
        try {
            $row_count = ($request->has('row')) ? $request->row : 10;
            $sort = ($request->has('sort')) ? $request->sort : 'desc';
            $column = ($request->has('col')) ? $request->col : null;
            $quick_search = ($request->has('quick_search')) ? $request->quick_search : null;
            $name = ($request->has('name')) ? $request->name : null;
            $data['items'] = $this->eventRepository->withPaginate($row_count, $quick_search, $name, $sort, $column, null, ['id', 'title', 'from_date', 'to_date', 'for_whom', 'location', 'image']);
            if ($request->ajax()) {
                return view('attendance::events.paginates.event_list', $data);
            }
            if ($request->has("import_as")) {
                set_time_limit(-1);
                $data['items'] = $this->eventRepository->withPaginate("all", $quick_search, $name, $sort, $column, null, ['id', 'title', 'from_date', 'to_date', 'for_whom', 'location', 'image']);
                if ($request->import_as == "print") {
                    return view('attendance::events.paginates.event_print', $data);
                }
                if ($request->import_as == "csv") {
                    $this->eventRepository->csvDownload($data);
                    $filePath = public_path("uploads/csv/events.xlsx");
                    $headers = ['Content-Type: text/csv'];
                    $fileName = time() . '-events.xlsx';

                    return response()->download($filePath, $fileName, $headers);
                }
            }
            $events = $this->eventRepository->all();
            $data['roles'] = $this->roleRepository->normalRoles();
            return view('attendance::events.index', $data);
        } catch (\Exception $e) {
            Toastr::error(trans('common.Something Went Wrong'));
            return back();
        }
    }

    public function create()
    {
        abort(404);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'from_date' => 'required',
        ]);
        try {
            if (!empty($request->image)) {
                 $this->saveAvatar($request->image);
            }
            $event = $this->eventRepository->create($request->except('_token'));
            $users=User::whereIn('role_id',[1,2])->where('id','!=',auth()->user()->id)
            ->where('is_active','1')
            ->get(['id','role_id']);

            $subject = $request->title;
            $class = $event;
            $data = $request->description ?? 'A Event Has been Created';
            $url = route('events.index');
            $this->sendNotification($class,null,$subject,null,null,$data,$users,$role_id=null,$url);
            Toastr::success(trans('event.Event Has Been Created Successfully'));
            return back();
        } catch (\Exception $e) {
            Toastr::error(trans('common.Something Went Wrong'));
            return back();
        }
    }

    public function show($id)
    {
        return view('attendance::show');
    }

    public function edit($id)
    {
        try {
            $data['editData'] = $this->eventRepository->find($id);
            $data['roles'] = $this->roleRepository->normalRoles();
            $data['items'] = $this->eventRepository->withPaginate(10, null, null, 'asc', 'id');
            return view('attendance::events.index', $data);
        } catch (\Exception $e) {
            Toastr::error(trans('common.Something Went Wrong'));
            return back();
        }
    }

    public function update(Request $request, $id)
    {
        $validate_rules = [
            'title' => 'required',
            'from_date' => 'required',
        ];
        $request->validate($validate_rules, validationMessage($validate_rules));
        try {
            $event = $this->eventRepository->update($request->except('_token'),$id);

            Toastr::success(trans('event.Event Has Been Updated Successfully'));
            return redirect()->route('events.index');
        } catch (\Exception $e) {
            Toastr::error(trans('common.Something Went Wrong'));
            return back();
        }
    }

    public function destroy($id)
    {
        try {
            $this->eventRepository->delete($id);
            Toastr::success(trans('event.Event Has Been Deleted Successfully'));
            return back();
        } catch (\Exception $e) {
            Toastr::error(trans('common.Something Went Wrong'));
            return back();
        }
    }
}
