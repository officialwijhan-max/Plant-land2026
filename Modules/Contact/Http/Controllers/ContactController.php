<?php

namespace Modules\Contact\Http\Controllers;

use App\User;
use Exception;
use LogActivity;
use Carbon\Carbon;
use App\Traits\Notification;
use Illuminate\Http\Request;
use Modules\Setup\Entities\City;
use Modules\Setup\Entities\State;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Setup\Entities\Country;
use Modules\Account\Entities\ChartAccount;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Modules\Setting\Model\GeneralSetting;
use Modules\ProAccount\Repositories\SubLeadgerRepository;
use Modules\ProAccount\Repositories\JournalRepository as ProJournalRepository;
use Modules\ProAccount\Repositories\LeadgerRepository;
use Modules\Purchase\Repositories\PurchaseOrderRepository;
use Modules\Account\Repositories\VoucherRepository;
use Modules\Contact\Http\Requests\ContactFormRequest;
use Modules\Sale\Repositories\SaleRepositoryInterface;
use Modules\Account\Repositories\JournalRepositoryInterface;
use Modules\Contact\Http\Requests\ContactProfileFormRequest;
use Modules\Contact\Repositories\ContactRepositoriesInterface;

class ContactController extends Controller
{
    use Notification;

    protected $contactRepository, $voucherRepository, $journalRepository;

    public function __construct(ContactRepositoriesInterface $contactRepository, VoucherRepository $voucherRepository, JournalRepositoryInterface $journalRepository)
    {
        $this->middleware(['auth']);
        $this->contactRepository = $contactRepository;
        $this->voucherRepository = $voucherRepository;
        $this->journalRepository = $journalRepository;
        $this->middleware(['prohibited.demo.mode'])->only('post_settings');
    }

    public function index(Request $request)
    {
        $contact_type = $request->contact_type;
        $countries = Country::all();
        return view('contact::contact.add_contact', compact('countries', 'contact_type'));
    }

    public function customer_select_list_option_for_quotation(Request $request)
    {
        $data = $this->contactRepository->listForSelectCustomerWithID($request);
        return response()->json($data);
    }

    public function customer_select_list_option(Request $request)
    {
        $data = $this->contactRepository->listForSelectCustomer($request);
        return response()->json($data);
    }

    public function supplier_select_list_option(Request $request)
    {
        $data = $this->contactRepository->listForSelectSupplier($request);
        return response()->json($data);
    }

    public function supplier(Request $request)
    {
        try{
            $row_count = ($request->has('row')) ? $request->row : 10 ;
            $sort = ($request->has('sort')) ? $request->sort : 'asc' ;
            $column = ($request->has('col')) ? $request->col : null ;
            $quick_search = ($request->has('quick_search')) ? $request->quick_search : null ;
            $name = ($request->has('name')) ? $request->name : null ;
            if ($request->has("import_as")) {
                set_time_limit(-1);
                $data['items'] = $this->contactRepository->withPaginate("all",$quick_search,$name,$sort,$column,"supplier",['country:id,name','state:id,name','city:id,name','purchases:id,supplier_id'],['id','contact_id','name','is_active','country_id','state_id','city_id','email','address','mobile','tax_number','pay_term','pay_term_condition']);
                $data['type'] = "supplier";
                if ($request->import_as == "print") {
                    return view('contact::contact.paginates.customer_print', $data);
                }
                if ($request->import_as == "csv") {
                    $data['type'] = "supplier";
                    $this->contactRepository->csvDownload('supplier', $data);
                    $filePath = public_path("uploads/csv/supplier_list.xlsx");
                    $headers = ['Content-Type: text/csv'];
                    $fileName = time().'-supplier_list.xlsx';

                    return response()->download($filePath, $fileName, $headers);
                }
            }
            $data['items'] = $this->contactRepository->withPaginate($row_count,$quick_search,$name,$sort,$column,"supplier",['country:id,name','state:id,name','city:id,name','purchases:id,supplier_id'],['id','contact_id','name','is_active','country_id','state_id','city_id','email','address','mobile','tax_number','pay_term','pay_term_condition']);
            if ($request->ajax()) {
                return view('contact::contact.paginates.supplier', $data);
            }
            return view('contact::contact.supplier', $data);

        }catch (\Exception $e){
            \LogActivity::errorLog($e->getMessage());
            Toastr::error(__('common.Something Went Wrong'));
            return back();
        }
    }

    public function customer(Request $request)
    {
        try{
            $row_count = ($request->has('row')) ? $request->row : 10 ;
            $sort = ($request->has('sort')) ? $request->sort : 'asc' ;
            $column = ($request->has('col')) ? $request->col : null ;
            $quick_search = ($request->has('quick_search')) ? $request->quick_search : null ;
            $name = ($request->has('name')) ? $request->name : null ;
            if ($request->has("import_as")) {
                set_time_limit(-1);
                $data['items'] = $this->contactRepository->withPaginate("all",$quick_search,$name,$sort,$column,"customer",['country:id,name','state:id,name','city:id,name','sales:id,customer_id'],['id','contact_id','name','is_active','country_id','state_id','city_id','email','address','mobile','tax_number','pay_term','pay_term_condition']);
                if ($request->import_as == "print") {
                    return view('contact::contact.paginates.customer_print', $data);
                }
                if ($request->import_as == "csv") {
                    $data['type'] = "customer";
                    $this->contactRepository->csvDownload("customer", $data);
                    $filePath = public_path("uploads/csv/customer_list.xlsx");
                    $headers = ['Content-Type: text/csv'];
                    $fileName = time().'-customer_list.xlsx';

                    return response()->download($filePath, $fileName, $headers);
                }
            }
            $data['items'] = $this->contactRepository->withPaginate($row_count,$quick_search,$name,$sort,$column,"customer",['country:id,name','state:id,name','city:id,name','sales:id,customer_id'],['id','contact_id','name','is_active','country_id','state_id','city_id','email','address','mobile','tax_number','pay_term','pay_term_condition']);
            if ($request->ajax()) {
                return view('contact::contact.paginates.customer', $data);
            }

            return view('contact::contact.customer', $data);

        }catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            Toastr::error(__('common.Something Went Wrong'));
            return back();
        }
    }

    public function create()
    {
        $countries = Country::all();
        return view('contact::create', compact('countries'));
    }

    public function store(ContactFormRequest $request)
    {
        DB::beginTransaction();
        try {
            $contact = $this->contactRepository->create($request->except("_token"));

            $users=User::whereIn('role_id',[1,2])->where('id','!=',auth()->user()->id)
            ->where('is_active','1')
            ->get(['id','role_id']);

            $created_by = Auth::user()->name;
            $company = app('general_setting')->company_name;
            $content = __('notification.You Have Been added as') . $contact->contact_type .  __('notification.by') . $created_by . __('notification.for') . $company . ' ';
            $number = $contact->mobile;
            $subject='Added';
            $message = __('notification.Congrats ! You Have Been added as') . $contact->contact_type . __('notification.by') . $created_by . ' for ' . $company . ' ';
             $this->sendNotification($contact, $contact->email,$subject, $content, $number, $message, $users);

            DB::commit();

            LogActivity::successLog('Contact Added Successfully');
            if ($request->ajax()) {
                $customers = $this->contactRepository->customer();
                $output = '<select class="primary_select mb-30 customer">';
                foreach ($customers as $key => $customer) {
                    $output .= '<option value="'.$customer->id.'">'.$customer->name.'</option>';

                }
                $output .= '</select>';
                return response()->json([
                    'success' => __('contact.Contact Added Successfully'),
                    'output' => $output,
                ]);
            }

            else{
                Toastr::success(__('contact.Contact Added Successfully'));
                return back();
            }

        } catch (Exception $e) {
            DB::rollBack();
            LogActivity::errorLog($e->getMessage());

            Toastr::error(__('common.Something Went Wrong'));
            return back();
        }
    }

    public function show(Request $request, $id)
    {
        try {
            $row_count = ($request->has('row')) ? $request->row : 10 ;
            $sort = ($request->has('sort')) ? $request->sort : 'asc' ;
            $column = ($request->has('col')) ? $request->col : null ;
            $quick_search = ($request->has('quick_search')) ? $request->quick_search : null ;
            $name = ($request->has('name')) ? $request->name : null ;
            $data['supplier'] = $this->contactRepository->find($id);
            $saleRepository = new PurchaseOrderRepository();
            if ($request->has("import_as")) {
                set_time_limit(-1);
                if ($request->purpose == "invoice") {
                    $data['invoices'] = $saleRepository->withPaginateSupplierInvoice('all',$quick_search,$sort,$column,$id,['user:id,name','payments:id,payable_type,payable_id,amount,return_amount'],['id','date','invoice_no','ref_no','is_paid','supplier_id','created_by','payable_amount','status','amount']);
                    if ($request->import_as == "print") {
                        return view('contact::contact.paginates.supplier_invoice_print', $data);
                    }
                    if ($request->import_as == "csv") {
                        $saleRepository->csvDownloadSupplierInvoice($data,'supplier');
                        $filePath = public_path("uploads/csv/supplier-invoice-list.xlsx");
                        $headers = ['Content-Type: text/csv'];
                        $fileName = time().'-supplier-invoice-list.xlsx';

                        return response()->download($filePath, $fileName, $headers);
                    }
                }
                if ($request->purpose == "return_invoice") {
                    $data['return_invoices'] = $saleRepository->withPaginateSupplierReturnInvoice('all',$quick_search,$sort,$column,$id,['items'],['id','date','invoice_no','return_status','total_return_amount']);
                    if ($request->import_as == "print") {
                        return view('contact::contact.paginates.supplier_return_invoice_print', $data);
                    }
                    if ($request->import_as == "csv") {
                        $saleRepository->csvDownloadSupplierReturnInvoice($data,'supplier');
                        $filePath = public_path("uploads/csv/supplier-return-invoice-list.xlsx");
                        $headers = ['Content-Type: text/csv'];
                        $fileName = time().'-supplier-return-invoice-list.xlsx';

                        return response()->download($filePath, $fileName, $headers);
                    }
                }
            }

            $data['invoices'] = $saleRepository->withPaginateSupplierInvoice($row_count,$quick_search,$sort,$column,$id,['user:id,name','payments:id,payable_type,payable_id,amount,return_amount'],['id','date','invoice_no','ref_no','is_paid','supplier_id','created_by','payable_amount','status','amount']);
            $data['return_invoices'] = $saleRepository->withPaginateSupplierReturnInvoice($row_count,$quick_search,$sort,$column,$id,['items'],['id','date','invoice_no','return_status','total_return_amount']);

            if ($request->has('type') && $request->type == "invoice_list_in_supplier_detail" && $request->ajax()) {
                return view('contact::contact.paginates.supplier_invoice_list', $data);
            }
            if ($request->has('type') && $request->type == "return_invoice_list_in_supplier_detail" && $request->ajax()) {
                return view('contact::contact.paginates.supplier_return_invoice_list', $data);
            }


            $data['account_categories'] = $this->voucherRepository->category();
            $data['accounts'] = $this->journalRepository->transactionalAccounts();
            return view('contact::contact.supplier_view', $data);
        } catch (Exception $e) {
            LogActivity::errorLog($e->getMessage());
            Toastr::error('common.Operation failed');
            return back();
        }
    }

    public function customer_details(Request $request, SaleRepositoryInterface $saleRepository, $id)
    {
        try {
            $row_count = ($request->has('row')) ? $request->row : 10 ;
            $sort = ($request->has('sort')) ? $request->sort : 'asc' ;
            $column = ($request->has('col')) ? $request->col : null ;
            $quick_search = ($request->has('quick_search')) ? $request->quick_search : null ;
            $name = ($request->has('name')) ? $request->name : null ;
            $data['customer'] = $this->contactRepository->find($id);
            if ($request->has("import_as")) {
                set_time_limit(-1);

                if ($request->purpose == "transaction") {
                    if ($request->import_as == "print") {
                        $customer = $data['customer'];
                        $data['chartAccount'] =  ChartAccount::where('contactable_type', 'Modules\Contact\Entities\ContactModel')->where('contactable_id', $customer->id)->first();
                        return view('contact::contact.paginates.transaction_print', $data);
                    }
                }
                if ($request->purpose == "invoice") {
                    $data['invoices'] = $saleRepository->withPaginateCustomerInvoice('all',$quick_search,$sort,$column,'customer',$id,['user:id,name','payments:id,payable_type,payable_id,amount,return_amount'],['id','date','invoice_no','ref_no','is_approved','customer_id','user_id','payable_amount','status','amount']);
                    if ($request->import_as == "print") {

                        \LogActivity::successLog('Invoice List Print for'.' - '.$data['customer']->name, route('customer.view', $id), 'Invoice List Print');
                        return view('contact::contact.paginates.invoice_print', $data);
                    }
                    if ($request->import_as == "csv") {

                        \LogActivity::successLog('Invoice List CSV download for'.' - '.$data['customer']->name, route('customer.view', $id), 'Invoice List CSV');
                        $saleRepository->csvDownloadCustomerInvoice($data,'customer');
                        $filePath = public_path("uploads/csv/customer_invoice_list.xlsx");
                        $headers = ['Content-Type: text/csv'];
                        $fileName = time().'-customer_invoice_list.xlsx';

                        return response()->download($filePath, $fileName, $headers);
                    }
                }
                if ($request->purpose == "return_invoice") {
                    $data['return_invoices'] = $saleRepository->withPaginateCustomerReturnInvoice('all',$quick_search,$sort,$column,'customer',$id,['items'],['id','date','invoice_no', 'ref_no', 'user_id','return_status']);
                    if ($request->import_as == "print") {
                        \LogActivity::successLog('Return Invoice List Print for'.' - '.$data['customer']->name, route('customer.view', $id), "Return Invoice List Print");
                        return view('contact::contact.paginates.return_invoice_print', $data);
                    }
                    if ($request->import_as == "csv") {
                        \LogActivity::successLog('Return Invoice List CSV Download for'.' - '.$data['customer']->name, route('customer.view', $id), "Return Invoice List CSV Download");
                        $saleRepository->csvDownloadCustomerReturnInvoice($data,'customer');
                        $filePath = public_path("uploads/csv/customer_return_invoice_list.xlsx");
                        $headers = ['Content-Type: text/csv'];
                        $fileName = time().'-customer_return_invoice_list.xlsx';

                        return response()->download($filePath, $fileName, $headers);
                    }
                }
            }

            $data['invoices'] = $saleRepository->withPaginateCustomerInvoice($row_count,$quick_search,$sort,$column,'customer',$id,['user:id,name','payments:id,payable_type,payable_id,amount,return_amount'],['id','date','invoice_no','ref_no','is_approved','customer_id','user_id','payable_amount','status','amount']);
            $data['return_invoices'] = $saleRepository->withPaginateCustomerReturnInvoice($row_count,$quick_search,$sort,$column,'customer',$id,['items'],['id','date','invoice_no', 'ref_no', 'user_id','return_status']);
            if (moduleStatusCheck('ProAccount') && Settings('current_active_accounting') == "pro") {
                $leadgerRepo = new LeadgerRepository();
                $data['accounts'] = $leadgerRepo->transactional_accounts([],['id','name','code','type']);
            }

            if ($request->has('type') && $request->type == "invoice_list_in_customer_detail" && $request->ajax()) {
                return view('contact::contact.paginates.invoice_list', $data);
            }
            if ($request->has('type') && $request->type == "return_invoice_list_in_customer_detail" && $request->ajax()) {
                return view('contact::contact.paginates.return_invoice_list', $data);
            }



            return view('contact::contact.customer_view', $data);
        } catch (Exception $e) {
            LogActivity::errorLog($e->getMessage());
            Toastr::error('common.Operation failed');
            return back();
        }
    }

    public function edit($id)
    {
        try {
            $contact = $this->contactRepository->find($id);
            return view('contact::contact.edit_contact', [
                "contact" => $contact,
            ]);
        } catch (Exception $e) {
            LogActivity::errorLog($e->getMessage());
            Toastr::error('common.Operation failed');
            return back();
        }
    }

    public function update(ContactFormRequest $request, $id)
    {

        try {
            $contact = $this->contactRepository->update($request->except("_token"), $id);
            $users=User::whereIn('role_id',[1,2])->where('id','!=',auth()->user()->id)
                        ->where('is_active','1')
                        ->get(['id','role_id']);

            $created_by = Auth::user()->name;
            $company = app('general_setting')->company_name;
            $content = __('notification.Your info has Been updated as') . $contact->contact_type .  __('notification.by') . $created_by . __('notification.for') . $company . ' ';
            $number = $contact->mobile;
            $message = __('notification.Your info Have Been updated as') . $contact->contact_type . __('notification.by') . $created_by . __('notification.for') . $company . ' ';
            $this->sendNotification($contact, $contact->email, $contact->contact_type . 'Added', $content, $number, $message,$users);

            LogActivity::successLog('Contact Updated Successfully');
            Toastr::success(__('contact.Contact Updated Successfully'));
            return back();
        } catch (Exception $e) {

            LogActivity::errorLog($e->getMessage());
            Toastr::error(__('common.Something Went Wrong'));
            return back();
        }
    }

    public function destroy($id)
    {
        try {
            $this->contactRepository->delete($id);
            LogActivity::successLog('Contact Deleted Successfully');
            return back();
        } catch (Exception $e) {
            LogActivity::errorLog($e->getMessage());
            Toastr::error(__('common.Something Went Wrong'));
            return back();
        }
    }

    public function statusChange(Request $request)
    {
        try {
            $this->contactRepository->statusChange($request->except("_token"));
            Toastr::success(__('contact.Status has been changed Successfully'));
            return 1;
        } catch (Exception $e) {
            LogActivity::errorLog($e->getMessage());
            Toastr::error('Operation failed');
            return 0;
        }
    }

    public function customerSaleProductList($id)
    {
        $saleHistory = $this->contactRepository->customerSaleHistory($id);
        return view('contact::contact.customer_product_list', compact('saleHistory'));
    }


    public function supplierPurchaseProductList($id)
    {
        $purchaseHistory = $this->contactRepository->supplierPurchaseHistory($id);

        return view('contact::contact.supplier_product_list', compact('purchaseHistory'));
    }

    public function addBalanceCustomer(Request $request)
    {
        $validate_rules = [
            "date" => "required",
            "voucher_type" => (moduleStatusCheck('ProAccount') && Settings('current_active_accounting') == "pro") ? "nullable" : "required",
            "narration" => "nullable",
            "cheque_no" => "nullable",
            "cheque_date" => "nullable",
            "bank_name" => "nullable",
            "bank_branch" => "nullable",
            "debit_account_id" => "required",
            "debit_account_amount.*" => "required",
            "debit_account_narration" => "nullable"

        ];

        $request->validate($validate_rules, validationMessage($validate_rules));
        try {
            if (moduleStatusCheck('ProAccount') && Settings('current_active_accounting') == "pro") {
                $subLeadgerRepo = new SubLeadgerRepository();
                $leadgerRepo = new LeadgerRepository();
                $sub_leadger = $subLeadgerRepo->find($request->debit_account_id[0]);
                $leadger = $leadgerRepo->find($request->credit_account_id);
                $credit_amounts[] = $request->debit_account_amount[0];
                $credit_account_id[] = $sub_leadger->leadger_id;
                $credit_partner_id[] = $sub_leadger->id;
                $credit_cash_flow_account_id[] = 0;
                $credit_narration[] = $request->debit_account_narration[0];

                $debit_amounts[] = $request->debit_account_amount[0];
                $debit_account_id[] = $request->credit_account_id;
                $debit_partner_id[] = 0;
                $debit_cash_flow_account_id[] = $request->cash_flow_account;
                $debit_narration[] = $request->debit_account_narration[0];

                $journalRecieveRepository = new ProJournalRepository();
                $voucher = $journalRecieveRepository->create([
                    'type' => $leadger->acc_type == "bank" ? "rec_bank" : "rec_cash",
                    'is_cash_flow_journal' => 0,
                    'amount'=> $request->debit_account_amount[0],
                    'date'=> Carbon::parse($request->date)->format('Y-m-d'),
                    'credit_account_id'=> $credit_account_id,
                    'credit_sub_account_id'=> $credit_partner_id,
                    'credit_cash_flow_account_id'=> $credit_cash_flow_account_id,
                    'credit_account_amount'=> $credit_amounts,
                    'credit_narration'=> $credit_narration,
                    'narration_voucher'=> $request->debit_account_narration[0],
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
                ]);
            } else {
                $debit_account_amount = 0;
                foreach ($request->debit_account_amount as $key => $amount) {
                    $debit_account_amount += $amount;
                }
                DB::beginTransaction();
                $this->voucherRepository->create([
                    'voucher_type' => $request->voucher_type == 1 ? 'CV' : 'BV',
                    'amount' => $debit_account_amount,
                    'date' => Carbon::parse($request->date)->format('Y-m-d'),
                    'payment_type' => 'voucher_recieve',
                    'credit_account_id' => $request->credit_account_id,
                    'credit_account_amount' => $debit_account_amount,
                    'credit_account_narration' => $request->debit_account_narration[0],
                    'debit_account_id' => $request->debit_account_id,
                    'debit_account_amount' => $request->debit_account_amount,
                    'debit_account_narration' => $request->debit_account_narration,

                    'narration' => $request->debit_account_narration[0],
                    'cheque_no' => $request->cheque_no,
                    'cheque_date' => Carbon::parse($request->cheque_date)->format('Y-m-d'),
                    'bank_name' => $request->bank_name,
                    'bank_branch' => $request->bank_branch,
                    'invoice_id' => ($request->invoice_id) ? $request->invoice_id : null,
                    'is_approve' => (app('business_settings')->where('type', 'add_balance_voucher_approval')->first()->status == 1) ? 1 : 0,
                ]);
            }
            DB::commit();
            LogActivity::successLog('Balance has been added Successfully !!!.');
            Toastr::success(__('contact.Balance has been added Successfully !!!'));
            return redirect()->back();
        } catch (Exception $e) {
            DB::rollBack();
            LogActivity::errorLog($e->getMessage() . ' - Error has been detected for Voucher Receive creation');
            Toastr::error(__('common.Something Went Wrong'));
            return back();
        }
    }

    public function minusBalance(Request $request)
    {
        try {
            DB::beginTransaction();
            if (moduleStatusCheck('ProAccount') && Settings('current_active_accounting') == "pro") {
                $subLeadgerRepo = new SubLeadgerRepository();
                $sub_leadger = $subLeadgerRepo->find($request->sub_account_id[0]);
                $debit_amounts[] = $request->sub_amount[0];
                $debit_account_id[] = $sub_leadger->leadger_id;
                $debit_partner_id[] = $sub_leadger->id;
                $debit_cash_flow_account_id[] = 0;
                $debit_narration[] = $request->narration;

                $credit_amounts[] = $request->sub_amount[0];
                $credit_account_id[] = $request->account_id;
                $credit_partner_id[] = 0;
                $credit_cash_flow_account_id[] = $request->cash_flow_account;
                $credit_narration[] = $request->narration;

                $journalRecieveRepository = new ProJournalRepository();
                $voucher = $journalRecieveRepository->create([
                    'type' => "misc",
                    'is_cash_flow_journal' => 0,
                    'amount'=> $request->sub_amount[0],
                    'date'=> Carbon::parse($request->date)->format('Y-m-d'),
                    'credit_account_id'=> $credit_account_id,
                    'credit_sub_account_id'=> $credit_partner_id,
                    'credit_cash_flow_account_id'=> $credit_cash_flow_account_id,
                    'credit_account_amount'=> $credit_amounts,
                    'credit_narration'=> $credit_narration,
                    'narration_voucher'=> $request->narration,
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
                ]);
            } else {
                $this->journalRepository->create([
                    'voucher_type' => 'JV',
                    'amount' => $request->sub_amount[0],
                    'date' => Carbon::parse($request->date)->format('Y-m-d'),
                    'account_type' => $request->account_type,
                    'payment_type' => 'journal_voucher',
                    'account_id' => $request->account_id,
                    'main_amount' => $request->sub_amount[0],
                    'narration' => $request->narration,

                    'sub_account_id' => $request->sub_account_id,
                    'sub_amount' => $request->sub_amount,
                    'sub_narration' => [$request->narration],
                    'is_approve' => 1,
                    // 'is_approve' => (app('business_settings')->where('type', 'substraction_balance_voucher_approval')->first()->status == 1) ? 1 : 0,
                ]);
            }
            DB::commit();
            LogActivity::successLog('Journal has been Added.');
            Toastr::success(__('account.Journal has been added Successfully'));
            return redirect()->back();
        } catch (Exception $e) {
            DB::rollBack();
            LogActivity::errorLog($e->getMessage() . ' - Error has been detected for Journal creation');
            Toastr::error(__('common.Something Went Wrong'));
            return back();
        }
    }

    public function addBalanceSupplier(Request $request)
    {
        $validate_rules = [
            "date" => "required",
            "voucher_type" => (moduleStatusCheck('ProAccount') && Settings('current_active_accounting') == "pro") ? "nullable" : "required",
            "narration" => "nullable",
            "cheque_no" => "nullable",
            "cheque_date" => "nullable",
            "bank_name" => "nullable",
            "bank_branch" => "nullable",
            "debit_account_id" => "required",
            "debit_account_amount.*" => "required",
            "debit_account_narration" => "nullable"

        ];
        $request->validate($validate_rules, validationMessage($validate_rules));
        DB::beginTransaction();
        try {
            if (moduleStatusCheck('ProAccount') && Settings('current_active_accounting') == "pro") {
                $leadgerRepo = new LeadgerRepository();
                $leadger = $leadgerRepo->find($request->credit_account_id);
                $subLeadgerRepo = new SubLeadgerRepository();
                $sub_leadger = $subLeadgerRepo->find($request->debit_account_id[0]);
                $debit_amounts[] = $request->debit_account_amount[0];
                $debit_account_id[] = $sub_leadger->leadger_id;
                $debit_partner_id[] = $sub_leadger->id;
                $debit_cash_flow_account_id[] = 0;
                $debit_narration[] = $request->debit_account_narration[0];

                $credit_amounts[] = $request->debit_account_amount[0];
                $credit_account_id[] = $request->credit_account_id;
                $credit_partner_id[] = 0;
                $credit_cash_flow_account_id[] = $request->cash_flow_account;
                $credit_narration[] = $request->debit_account_narration[0];

                $journalRecieveRepository = new ProJournalRepository();
                $voucher = $journalRecieveRepository->create([
                    'type' => $leadger->acc_type == "bank" ? "pay_bank" : "pay_cash",
                    'is_cash_flow_journal' => 0,
                    'amount'=> $request->debit_account_amount[0],
                    'date'=> Carbon::parse($request->date)->format('Y-m-d'),
                    'credit_account_id'=> $credit_account_id,
                    'credit_sub_account_id'=> $credit_partner_id,
                    'credit_cash_flow_account_id'=> $credit_cash_flow_account_id,
                    'credit_account_amount'=> $credit_amounts,
                    'credit_narration'=> $credit_narration,
                    'narration_voucher'=> $request->debit_account_narration[0],
                    'referable_type'=> null,
                    'referable_id'=> null,
                    'is_invoiced'=> null,
                    'is_manual_entry'=> 1,

                    'debit_account_id'=> $debit_account_id,
                    'debit_sub_account_id'=> $debit_partner_id,
                    'debit_cash_flow_account_id'=> $debit_cash_flow_account_id,
                    'debit_account_amount'=> $debit_amounts,
                    'debit_narration'=> $debit_narration,
                    'is_approve' => 1,
                ]);
            } else {
                $debit_account_amount = 0;
                foreach ($request->debit_account_amount as $key => $amount) {
                    $debit_account_amount += $amount;
                }
                $this->voucherRepository->create([
                    'voucher_type' => $request->voucher_type == 1 ? 'CV' : 'BV',
                    'amount' => $debit_account_amount,
                    'date' => Carbon::parse($request->date)->format('Y-m-d'),
                    'payment_type' => 'voucher_payment',
                    'credit_account_id' => $request->credit_account_id,
                    'credit_account_amount' => $debit_account_amount,
                    'credit_account_narration' => $request->debit_account_narration[0],
                    'debit_account_id' => $request->debit_account_id,
                    'debit_account_amount' => $request->debit_account_amount,
                    'debit_account_narration' => $request->debit_account_narration,

                    'narration' => $request->narration,
                    'cheque_no' => $request->cheque_no,
                    'cheque_date' => Carbon::parse($request->cheque_date)->format('Y-m-d'),
                    'bank_name' => $request->bank_name,
                    'bank_branch' => $request->bank_branch,
                    'invoice_id' => ($request->invoice_id) ? $request->invoice_id : null,
                    'is_approve' => (app('business_settings')->where('type', 'add_balance_voucher_approval')->first()->status == 1) ? 1 : 0,
                ]);
            }
            DB::commit();
            LogActivity::successLog('Balance has been added Successfully !!!.');
            Toastr::success(__('contact.Balance has been added Successfully !!!'));
            return redirect()->back();
        } catch (Exception $e) {
            DB::rollBack();
            LogActivity::errorLog($e->getMessage() . ' - Error has been detected for Voucher Receive creation');
            Toastr::error(__('common.Something Went Wrong'));
            return back();
        }
    }

    public function minusBalanceSupplier(Request $request)
    {
        $validate_rules = [
            "date" => "required",
            "account_id" => "required",

        ];
        $request->validate($validate_rules, validationMessage($validate_rules));
        DB::beginTransaction();
        try {
            if (moduleStatusCheck('ProAccount') && Settings('current_active_accounting') == "pro") {
                $subLeadgerRepo = new SubLeadgerRepository();
                $sub_leadger = $subLeadgerRepo->find($request->sub_account_id[0]);
                $credit_amounts[] = $request->sub_amount[0];
                $credit_account_id[] = $sub_leadger->leadger_id;
                $credit_partner_id[] = $sub_leadger->id;
                $credit_cash_flow_account_id[] = 0;
                $credit_narration[] = $request->narration[0];

                $debit_amounts[] = $request->sub_amount[0];
                $debit_account_id[] = $request->account_id;
                $debit_partner_id[] = 0;
                $debit_cash_flow_account_id[] = $request->cash_flow_account;
                $debit_narration[] = $request->narration[0];


                $journalRecieveRepository = new ProJournalRepository();
                $voucher = $journalRecieveRepository->create([
                    'type' => "misc",
                    'is_cash_flow_journal' => 0,
                    'amount'=> $request->sub_amount[0],
                    'date'=> Carbon::parse($request->date)->format('Y-m-d'),
                    'credit_account_id'=> $credit_account_id,
                    'credit_sub_account_id'=> $credit_partner_id,
                    'credit_cash_flow_account_id'=> $credit_cash_flow_account_id,
                    'credit_account_amount'=> $credit_amounts,
                    'credit_narration'=> $credit_narration,
                    'narration_voucher'=> $request->narration[0],
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
                ]);
            } else {
                $this->journalRepository->create([
                    'voucher_type' => 'JV',
                    'amount' => $request->sub_amount[0],
                    'date' => Carbon::parse($request->date)->format('Y-m-d'),
                    'account_type' => 'debit',
                    'payment_type' => 'journal_voucher',
                    'account_id' => $request->account_id,
                    'main_amount' => $request->sub_amount[0],
                    'narration' => $request->narration[0],

                    'sub_account_id' => $request->sub_account_id,
                    'sub_amount' => $request->sub_amount,
                    'sub_narration' => $request->narration,
                    'is_approve' => 1,
                ]);
            }
            DB::commit();
            LogActivity::successLog('Balance has been added Successfully !!!.');
            Toastr::success(__('contact.Balance has been added Successfully !!!'));
            return redirect()->back();
        } catch (Exception $e) {
            DB::rollBack();
            LogActivity::errorLog($e->getMessage() . ' - Error has been detected for Voucher Receive creation');
            Toastr::error(__('common.Something Went Wrong'));
            return back();
        }
    }

    public function settings()
    {
        return view('contact::contact.settings');
    }

    public function post_settings(Request $request)
    {
        try {
            $contact_login = $request->contact_login ? 1 : 0;
            $settings = GeneralSetting::find(1);
            $settings->contact_login = $contact_login;
            $settings->save();
            LogActivity::successLog('Contact Settings updated Successfully !!!.');
            Toastr::success(__('contact.Contact Settings updated Successfully !!!.'));
            return redirect()->back();
        } catch (Exception $e) {
            DB::rollBack();
            LogActivity::errorLog($e->getMessage() . ' - Error has been detected for Contact Settings update');
            Toastr::error(__('common.Something Went Wrong'));
            return back();
        }
    }

    public function my_details()
    {
        $id = auth()->user()->contact_id;
        $contact = $this->contactRepository->find($id);

        if ($contact->contact_type == 'Customer') {
            try {
                $recieve_from_accounts = $this->voucherRepository->recieveCategoryAccounts();
                $recieve_by_accounts = $this->voucherRepository->get_recieveByAccount_account();
                $accounts = $this->journalRepository->expenseAccounts();
                $customer = $this->contactRepository->find($id);
                return view('contact::contact.my_details.customer', [
                    "customer" => $customer,
                    "recieve_from_accounts" => $recieve_from_accounts,
                    "recieve_by_accounts" => $recieve_by_accounts,
                    "accounts" => $accounts
                ]);
            } catch (Exception $e) {
                LogActivity::errorLog($e->getMessage());
                Toastr::error('common.Operation failed');
                return back();
            }
        } else {
            try {
                $supplier = $this->contactRepository->find($id);
                return view('contact::contact.my_details.supplier', [
                    "supplier" => $supplier,
                    "account_categories" => $this->voucherRepository->category(),
                    "accounts" => $this->journalRepository->transactionalAccounts()
                ]);
            } catch (Exception $e) {

                LogActivity::errorLog($e->getMessage());
                Toastr::error('common.Operation failed');
                return back();
            }
        }
    }

    public function my_products()
    {
        $id = auth()->user()->contact_id;
        $contact = $this->contactRepository->find($id);
        if ($contact->contact_type == 'Customer') {
            $saleHistory = $this->contactRepository->customerSaleHistory($id);

            return view('contact::contact.my_details.customer_product', compact('saleHistory'));
        } else {
            $purchaseHistory = $this->contactRepository->supplierPurchaseHistory($id);

            return view('contact::contact.supplier_product_list', compact('purchaseHistory'));
        }
    }

    public function my_payment(SaleRepositoryInterface $saleRepo, $id)
    {
        try {

            $sale = $saleRepo->find($id);

            $data = [
                'sale' => $sale,
            ];

            return view('contact::contact.my_details.sale_payment')->with($data);
        } catch (Exception $e) {
            Toastr::error(trans('common.Something Went Wrong'));
            return back();
        }

    }

    public function post_my_payment(SaleRepositoryInterface $saleRepo, $id)
    {
        try {

            $sale = $saleRepo->find($id);
            $data = [
                'sale' => $sale,
            ];
            return view('contact::contact.my_details.sale_payment')->with($data);
        } catch (Exception $e) {
            Toastr::error(trans('common.Something Went Wrong'));
            return back();
        }

    }


    public function invoice()
    {
        $id = auth()->user()->contact_id;
        $contact = $this->contactRepository->find($id);
        if ($contact->contact_type == 'Customer') {
            try {
                $customer = $this->contactRepository->find($id);
                return view('contact::contact.my_details.customer_invoice', [
                    "customer" => $customer
                ]);
            } catch (Exception $e) {
                LogActivity::errorLog($e->getMessage());
                Toastr::error('common.Operation failed');
                return back();
            }
        } else {
            try {
                $supplier = $this->contactRepository->find($id);
                return view('contact::contact.my_details.supplier_invoice', [
                    "supplier" => $supplier
                ]);
            } catch (Exception $e) {

                LogActivity::errorLog($e->getMessage());
                Toastr::error('common.Operation failed');
                return back();
            }
        }
    }


    public function return()
    {
        $id = auth()->user()->contact_id;
        $contact = $this->contactRepository->find($id);
        if ($contact->contact_type == 'Customer') {
            try {
                $customer = $this->contactRepository->find($id);
                return view('contact::contact.my_details.customer_return', [
                    "customer" => $customer
                ]);
            } catch (Exception $e) {
                LogActivity::errorLog($e->getMessage());
                Toastr::error('common.Operation failed');
                return back();
            }
        } else {
            try {
                $supplier = $this->contactRepository->find($id);
                return view('contact::contact.my_details.supplier_return', [
                    "supplier" => $supplier
                ]);
            } catch (Exception $e) {
                LogActivity::errorLog($e->getMessage());
                Toastr::error('common.Operation failed');
                return back();
            }
        }
    }


    public function transaction()
    {
        $id = auth()->user()->contact_id;
        $contact = $this->contactRepository->find($id);
        if ($contact->contact_type == 'Customer') {
            try {
                $customer = $this->contactRepository->find($id);
                return view('contact::contact.my_details.customer_transaction', [
                    "customer" => $customer
                ]);
            } catch (Exception $e) {
                LogActivity::errorLog($e->getMessage());
                Toastr::error('common.Operation failed');
                return back();
            }
        } else {
            try {
                $supplier = $this->contactRepository->find($id);
                return view('contact::contact.my_details.supplier_transaction', [
                    "supplier" => $supplier
                ]);
            } catch (Exception $e) {

                LogActivity::errorLog($e->getMessage());
                Toastr::error('common.Operation failed');
                return back();
            }
        }
    }

    public function print_transaction_customer($id)
    {

        try{
            $user = $this->contactRepository->find($id);
            return view('contact::contact.print_view', [
                "user" => $user,
            ]);

        }catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            Toastr::error('common.Operation failed');
            return back();
        }
    }

    public function profile(){
        $user = $this->contactRepository->find(auth()->id());
        $countries = Country::all();
        $states = [];
        $cities = [];
        if ($user->country_id){
            $states = State::where('country_id', $user->country_id)->get();
        }

        if ($user->state_id){
            $cities = City::where('state_id', $user->state_id)->get();
        }
        return view('contact::contact.my_details.profile', compact('user', 'countries', 'states', 'cities', 'states', 'cities'));
    }

    public function post_profile(ContactProfileFormRequest $request){
        try {
            $contact = $this->contactRepository->updateProfile($request->except("_token"), auth()->user()->contact_id);

            LogActivity::successLog('Contact profile Updated Successfully');
            Toastr::success(__('contact.Contact Updated Successfully'));
            return back();
        } catch (Exception $e) {

            LogActivity::errorLog($e->getMessage());
            Toastr::error(__('common.Something Went Wrong'));
            return back();
        }
    }
    public function contact_csv_upload()
    {
        return view('contact::upload_via_csv.create');
    }

    public function contact_csv_upload_store(Request $request)
    {
        $validate_rules = [
            'file' => 'required|mimes:csv,xls,xlsx|max:2048'
        ];
        $request->validate($validate_rules, validationMessage($validate_rules));
        ini_set('max_execution_time', 0);
        DB::beginTransaction();
        try {
            $this->contactRepository->csv_contact_upload($request->except("_token"));
            DB::commit();
            Toastr::success(__('common.Successfully Uploaded !!!'));
            return back();
        } catch (\Exception $e) {
            DB::rollBack();
            if ($e->getCode() == 23000) {
                Toastr::error(__('common.Duplicate entry is exist in your file !!!'));
            }
            else {
                Toastr::error(__('common.Something went wrong. Upload again !!!'));
            }
            return back();
        }

    }

}
