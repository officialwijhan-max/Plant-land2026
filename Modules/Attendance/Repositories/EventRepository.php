<?php

namespace Modules\Attendance\Repositories;

use App\Traits\ImageStore;
use Carbon\CarbonPeriod;
use DateTime;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Modules\Attendance\Entities\Attendance;
use Carbon\Carbon;
use Modules\Attendance\Entities\Event;
use Modules\Attendance\Entities\Holiday;
use Modules\RolePermission\Repositories\RoleRepository;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Attendance\Exports\EventExport;

class EventRepository implements EventRepositoryInterface
{
    use ImageStore;

    public function all()
    {
        return Event::latest()->get();
    }

    public function csvDownload($data)
    {
        if (file_exists(public_path("uploads/csv/events.xlsx"))) {
          unlink(public_path("uploads/csv/events.xlsx"));
        }
        return Excel::store(new EventExport($data), 'uploads/csv/events.xlsx', 'public_folder');
    }

    public function withPaginate($row_count,$quick_search,$name,$sort,$column, $relational_data=[], $selected_data=['*'])
    {
        $items = Event::query();
        if ($quick_search != null) {
            $items = $items->whereLike(['title','from_date','to_date'], $quick_search);
        }
        if ($row_count == "all") {
            $total_number = Event::count();

            if ($column != null) {
                return $items->orderBy($column, $sort)->paginate($total_number,$selected_data);
            }else {
                return $items->latest()->paginate($total_number,$selected_data);
            }
        }else {
            if ($column != null) {
                return $items->orderBy($column, $sort)->paginate($row_count,$selected_data);
            }else {
                return $items->paginate($row_count,$selected_data);
            }
        }
    }

    public function create(array $data)
    {
        $event = new Event();
        if (!empty($data['image'])) {
            $event->image = isset($data['image']) ? $this->saveImage($data['image']) : '';
        }
        $event->title = $data['title'];
        $event->for_whom = $data['for_whom'];
        $event->location = $data['location'];
        $event->description = $data['description'];
        $event->from_date = date('Y-m-d',strtotime($data['from_date']));
        $event->to_date = date('Y-m-d',strtotime($data['to_date']));
        $event->save();

        return $event;
    }

    public function find($id)
    {
        return Event::find($id);
    }

    public function update(array $data, $id)
    {
        $event = Event::find($id);

        if (!empty($data['image'])) {
            $event->image = isset($data['image']) ? $this->saveImage($data['image']) : $event->image;
        }

        $event->title = $data['title'];
        $event->for_whom = $data['for_whom'];
        $event->location = $data['location'];
        $event->description = $data['description'];
        $event->from_date = date('Y-m-d',strtotime($data['from_date']));
        $event->to_date = date('Y-m-d',strtotime($data['to_date']));
        $event->save();

        return $event;
    }

    public function delete($id)
    {
        Event::destroy($id);
    }

    public function roleWiseEvents()
    {
        return Event::where('for_whom','all')->orWhere('for_whom',Auth::user()->role->name)->get();
    }
}
