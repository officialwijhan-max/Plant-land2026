<?php



namespace App\Http\Controllers;



use App\Traits\Dashboard;

use App\User;

use Brian2694\Toastr\Facades\Toastr;

use Carbon\Carbon;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Hash;

use Modules\Account\Repositories\ChartAccountRepositoryInterface;

use Modules\Account\Repositories\VoucherRepositoryInterface;

use Modules\ProAccount\Repositories\VoucherRepository as ProVoucherRepository;

use Modules\Attendance\Entities\ToDo;

use Modules\Attendance\Repositories\EventRepositoryInterface;

use Modules\Leave\Repositories\HolidayRepository;

use Modules\Inventory\Repositories\ShowRoomRepositoryInterface;

use Modules\Inventory\Repositories\StockTransferRepositoryInterface;

use App\Notification;

use Modules\Product\Repositories\ProductRepositoryInterface;

use Modules\Purchase\Repositories\PurchaseOrderRepositoryInterface;

use Modules\RolePermission\Entities\Permission;

use Modules\Sale\Repositories\SaleRepositoryInterface;

use Modules\Setting\Model\BusinessSetting;

use Modules\Setting\Model\GeneralSetting;

use Modules\Setting\Model\EmailTemplate;

use Illuminate\Support\Facades\Validator;
use Modules\Sale\Entities\Sale;

class HomeController extends Controller
{

    use Dashboard;



    protected $purchaseOrderRepository, $saleRepository, $voucherRepository, $productRepository, $stockTransferRepository, $eventRepository, $showRoomRepository,

    $holidayRepository, $chartAccountRepository;



    public function __construct(

        PurchaseOrderRepositoryInterface $purchaseOrderRepository,

        SaleRepositoryInterface $saleRepository,

        VoucherRepositoryInterface $voucherRepository,

        ProductRepositoryInterface $productRepository,

        EventRepositoryInterface $eventRepository,

        ShowRoomRepositoryInterface $showRoomRepository,

        HolidayRepository $holidayRepository,

        StockTransferRepositoryInterface $stockTransferRepository,

        ChartAccountRepositoryInterface $chartAccountRepository

    ) {

        $this->middleware(['auth']);

        $this->middleware('prohibited.demo.mode')->only('post_change_password');

        $this->purchaseOrderRepository = $purchaseOrderRepository;

        $this->saleRepository = $saleRepository;

        $this->productRepository = $productRepository;

        $this->voucherRepository = $voucherRepository;

        $this->stockTransferRepository = $stockTransferRepository;

        $this->eventRepository = $eventRepository;

        $this->showRoomRepository = $showRoomRepository;

        $this->holidayRepository = $holidayRepository;

        $this->chartAccountRepository = $chartAccountRepository;

    }



    public function index()
    {

        if (auth()->user()->role->type == 'normal_user') {

            return redirect()->route('contact.my_details');

        }

        if (auth()->check() && !session()->get('showroom_id')) {

            auth()->logout();

            Toastr::warning('common.missing_showroom_for_user_please_login_again');

            return redirect()->route('login');

        }

        // try {



            if (moduleStatusCheck('ProAccount') && Settings('current_active_accounting') == "pro") {

                $proVoucherRepository = new ProVoucherRepository();

            }

            $data['purchases'] = $this->purchaseOrderRepository->approvePurchase();

            $data['sales'] = $this->saleRepository->approvedSales();

            $data['purchase_payments'] = $this->purchaseOrderRepository->purchasePayments('all');

            $data['sale_payments'] = $this->saleRepository->salePayments('all');

            $data['salesTotalAmount'] = $this->saleRepository->saleTotalPayments('all');

            $data['sale_due'] = $this->saleRepository->saleDue('all');

            $data['purchase_due'] = $this->purchaseOrderRepository->purchaseDue('all');

            // home.blade.php adds this into the purchase/sale due totals but
            // no controller ever actually computed it - it only worked on
            // the old database by accident. Defaulting to 0 keeps the
            // existing dashboard math unchanged rather than inventing a
            // specific accounting figure.
            $data['opening_balance_total'] = 0;

            $data['expenses'] = (moduleStatusCheck('ProAccount') && Settings('current_active_accounting') == "pro") ? $proVoucherRepository->expenses('all') : $this->voucherRepository->expenses('all');

            $data['monthly_sales'] = $this->monthlySales();

            $data['yearly_sales'] = $this->yearlySales();

            $data['dues'] = $this->saleRepository->dueList('latest', ['payments:id,payable_type,payable_id,amount'], ['id', 'invoice_no', 'date', 'saleable_id', 'saleable_type', 'payable_amount', 'customer_id', 'status']);

            $data['stock_alerts'] = $this->stockTransferRepository->suggestList(['productSku:id,product_id,sku', 'productSku.product:id,image_source,product_name,unit_type_id', 'productSku.product.unit_type:id,name'], ['*'])->take(10);

            $data['calendar_events'] = $this->calendarEvents();

            $data['toDos'] = ToDo::all();

            $data['daily_profit'] = $this->dailyProfit();

            $data['weekly_profit'] = $this->weeklyProfit();

            $data['monthly_profit'] = $this->monthlyProfit();

            $data['yearly_profit'] = $this->yearlyProfit();

            $data['product_quantity'] = $this->productQuantity();

            $data['bank'] = $this->totalBank('all');

            $data['cash'] = $this->totalCash('all');
            $data['income'] = $this->totalIncome();

            return view('home')->with($data);

        // } catch (\Exception $e) {

        //     \LogActivity::errorLog($e->getMessage());

        //     Toastr::error(trans('common.Something Went Wrong'));

        //     return back();

        // }

    }



    public function dashboardCards($type)
    {



        if (moduleStatusCheck('ProAccount') && Settings('current_active_accounting') == "pro") {

            $proVoucherRepository = new ProVoucherRepository();

        }

        $purchase_payments = $this->purchaseOrderRepository->purchasePayments($type);

        $purchase = $purchase_payments->sum('amount') - $purchase_payments->sum('return_amount');

        $sale_payments = $this->saleRepository->salePayments($type);

        $sale = $sale_payments->sum('amount') - $sale_payments->sum('return_amount');

        $expenses = (moduleStatusCheck('ProAccount') && Settings('current_active_accounting') == "pro") ? $proVoucherRepository->expenses($type) : $this->voucherRepository->expenses($type);

        $sale_due = $this->saleRepository->saleDue($type);

        $purchase_due = $this->purchaseOrderRepository->purchaseDue($type);



        return [

            'purchase_amount' => single_price($purchase),

            'sale_amount' => single_price($sale),

            'expense' => single_price($expenses),

            'bank' => single_price($this->totalBank($type)),

            'cash' => single_price($this->totalCash($type)),

            'income' => single_price($this->totalIncome()),

            'purchase_due' => single_price($purchase_due->sum('amount') - $purchase_due->sum('return_amount')),

            'sale_due' => single_price($sale_due->sum('amount') - $sale_due->sum('return_amount')),

        ];

    }



    public function fileDownload($document)
    {

        try {

            $file = explode(',', $document);

            $fileName = implode('/', $file);

            return response()->download(public_path($fileName));

        } catch (\Exception $e) {

            Toastr::error("File Couldn't Find", 'Error!');

            return back();

        }

    }



    public function company()
    {

        $data = [

            'company' => 'company',

            'business_settings' => BusinessSetting::all(),

            'setting' => GeneralSetting::first(),

            'email_templates' => EmailTemplate::all()



        ];

        return view('setting::index')->with($data);

    }



    public function menuSearch(Request $request)
    {

        $permissions = Permission::where('name', 'like', '%' . $request->value . '%')->where('searchable', 1)->where('status', 1)->get();



        $output = '';

        if (count($permissions) > 0) {

            foreach ($permissions as $permission) {

                $output .= '<a href="' . route($permission->route) . '"> ' . $permission->name . ' </a>';

            }

        } else {

            $no_result = trans('dashboard.No Results Found');

            $output = "<a href='#'>$no_result</a>";

        }





        return $output;

    }



    public function notificationUpdate(Request $request)
    {

        $notification = Notification::find($request->id);

        $notification->read_at = Carbon::now();

        $notification->save();

        return response()->json(['success' => 'success'], 200);

    }



    public function notification_list()
    {



        $notifications = Notification::where('user_id', auth()->user()->id)->where('role', auth()->user()->role_id)->latest()->get();

        return view('backEnd.notifications.index', compact('notifications'));





    }



    public function notification_read_all()
    {

        Notification::where('user_id', auth()->user()->id)->where('role', auth()->user()->role_id)->whereNull('read_at')->update(['read_at' => Carbon::now()]);

        if (!request()->ajax()) {

            return back();

        }

    }



    public function change_password()
    {

        return view('backEnd.profiles.password');

    }



    public function post_change_password(Request $request)
    {

        $validation_rules = [

            'current_password' => ['required', 'string'],

            'password' => ['required', 'string', 'min:8', 'confirmed']

        ];

        $validator = Validator::make($request->all(), $validation_rules, validationMessage($validation_rules));

        $user = User::where(['email' => auth()->user()->email])->first();



        $validator->after(function ($validator) use ($user, $request) {

            if ($user and Hash::check($request->current_password, $user->password)) {

                return true;

            }

            $validator->errors()->add(

                'current_password',
                __('auth.failed')

            );

        });



        if ($validator->fails()) {

            return redirect()

                ->back()

                ->withErrors($validator)

                ->withInput();

        }

        $user->password = bcrypt($request->password);

        $user->save();

        Toastr::success(__('common.Password change successful'), __('common.success'));

        return redirect()->route('home');



    }



    public function post_notification_read_all(Request $request)
    {

        $notifications = $request->notifications;



        if (!$notifications) {

            return back();

        }



        Notification::where('user_id', auth()->user()->id)->where('role', auth()->user()->role_id)->whereNull('read_at')->whereIn('id', $notifications)->update(['read_at' => Carbon::now()]);

        Toastr::success(__('common.Selected notification marked as seen'), __('common.success'));

        return back();





    }

}

