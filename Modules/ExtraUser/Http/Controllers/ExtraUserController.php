<?php

namespace Modules\ExtraUser\Http\Controllers;

use App\User;
use Exception;
use App\Traits\Notification;
use Illuminate\Http\Request;
use Modules\Setup\Entities\City;
use Modules\Setup\Entities\State;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Setup\Entities\Country;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Support\Renderable;
use Modules\UserActivityLog\Traits\LogActivity;
use Modules\Account\Repositories\VoucherRepository;
use Modules\ExtraUser\Http\Requests\ExtraUserRequest;
use Modules\Account\Repositories\JournalRepositoryInterface;
use Modules\ExtraUser\Repositories\ExtraUserRepositoriesInterface;

class ExtraUserController extends Controller
{
 use Notification;

    protected $extraUserRepository;

    public function __construct(ExtraUserRepositoriesInterface $extraUserRepository)
    {
        $this->middleware(['auth']);
        $this->extraUserRepository = $extraUserRepository;
    }

    public function agencies()
    {
        try {
            $contacts = $this->extraUserRepository->agency();
            return view('extrauser::index', [
                'contact_type' => 'Agency',
                "contacts" => $contacts
            ]);
        } catch (Exception $e) {
            LogActivity::errorLog($e->getMessage());
            Toastr::error('common.Operation failed');
            return back();
        }
    }

    public function strategicPartner()
    {
        try {
            $contacts = $this->extraUserRepository->strategicPartner();
            return view('extrauser::index', [
                'contact_type' => 'Strategic Partner',
                "contacts" => $contacts
            ]);
        } catch (Exception $e) {
            LogActivity::errorLog($e->getMessage());
            Toastr::error('common.Operation failed');
            return back();
        }
    }

    public function benches()
    {
        try {
            $contacts = $this->extraUserRepository->benches();
            return view('extrauser::index', [
                'contact_type' => 'Bench',
                "contacts" => $contacts
            ]);
        } catch (Exception $e) {
            LogActivity::errorLog($e->getMessage());
            Toastr::error('common.Operation failed');
            return back();
        }
    }

    public function kiosks()
    {
        try {
            $contacts = $this->extraUserRepository->kiosks();
            return view('extrauser::index', [
                'contact_type' => 'Kiosk',
                "contacts" => $contacts
            ]);
        } catch (Exception $e) {
            LogActivity::errorLog($e->getMessage());
            Toastr::error('common.Operation failed');
            return back();
        }
    }

    public function create(Request $request)
    {
        $countries = Country::all();
        $contact_type = $request->contact_type ;
        return view('extrauser::create', compact('countries', 'contact_type'));
    }

    public function store(ExtraUserRequest $request)
    {
        DB::beginTransaction();
        try {
            $contact = $this->extraUserRepository->create($request->except("_token"));
            $users=User::whereIn('role_id',[1,2])->where('id','!=',auth()->user()->id)
            ->where('is_active','1')
            ->get(['id','role_id']);
            $created_by = Auth::user()->name;
            $company = app('general_setting')->company_name;
            $content = __('notification.You Have Been added as') . $contact->contact_type . __('notification.by') . $created_by . __('notification.for') . $company . ' ';
            $number = $contact->mobile;
            $subject = $contact->contact_type . __('notification.Add');
            $message = __('notification.Congrats ! You Have Been added as'). $contact->contact_type . __('notification.by') . $created_by . __('notification.for') . $company . ' ';
            $this->sendNotification($contact, $contact->email, $subject, $content, $number, $message, $users);

            DB::commit();

            LogActivity::successLog($contact->contact_type . ' Added Successfully');

            Toastr::success(__('contact.Contact Added Successfully'));
            return back();

        } catch (Exception $e) {
            DB::rollBack();
            LogActivity::errorLog($e->getMessage());

            Toastr::error(__('common.Something Went Wrong'));
            return back();
        }
    }


    public function edit($id)
    {
        $contact = $this->extraUserRepository->find($id);
        $countries = Country::all();
        $states = [];
        $cities = [];
        if ($contact->country_id){
            $states = State::where('country_id', $contact->country_id)->get();
        }

        if ($contact->state_id){
            $cities = City::where('state_id', $contact->state_id)->get();
        }

        return view('extrauser::edit', [
            "contact" => $contact,
            "countries" => $countries,
            "states" => $states,
            "cities" => $cities
        ]);

    }

    public function update(ExtraUserRequest $request, $id)
    {

        DB::beginTransaction();
        try {
            $contact = $this->extraUserRepository->update($id, $request->except(["_token"]));
            DB::commit();
            $users=User::whereIn('role_id',[1,2])->where('id','!=',auth()->user()->id)
            ->where('is_active','1')
            ->get(['id','role_id']);
            $created_by = Auth::user()->name;
            $company = app('general_setting')->company_name;
            $content =  __('notification.Your info has Been updated as') . $contact->contact_type .  __('notification.by') . $created_by .  __('notification.for') . $company . ' ';
            $number = $contact->mobile;
            $subject = $contact->contact_type . __('notification.Added');
            $message =  __('notification.Your info Have Been updated as') . $contact->contact_type .  __('notification.by') . $created_by .  __('notification.for') . $company . ' ';
            $this->sendNotification($contact, $contact->email, $subject, $content, $number, $message,$users);

            LogActivity::successLog('Contact Updated Successfully');
            Toastr::success(__('contact.Contact Updated Successfully'));
            return back();

        } catch (Exception $e) {
            DB::rollBack();
            LogActivity::errorLog($e->getMessage());

            Toastr::error(__('common.Something Went Wrong'));
            return back();
        }
    }


    public function show($id)
    {
        try {
            $contact = $this->extraUserRepository->find($id);
            return view('extrauser::show', [
                "contact" => $contact,
            ]);
        } catch (Exception $e) {
            LogActivity::errorLog($e->getMessage());
            Toastr::error('common.Operation failed');
            return back();
        }
    }
}
