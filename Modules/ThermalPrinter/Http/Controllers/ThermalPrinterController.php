<?php

namespace Modules\ThermalPrinter\Http\Controllers;

use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;
use Modules\ThermalPrinter\Entities\PrinterSetting;
use Modules\ThermalPrinter\Entities\ThermalPrinter;
use Modules\ThermalPrinter\Repositories\ThermalPrinterRepositoryInterface;

class ThermalPrinterController extends Controller
{
    /**
     * @var ThermalPrinterRepositoryInterface
     */
    private $repository;

    public function __construct(ThermalPrinterRepositoryInterface $repository){

        $this->repository = $repository;
    }
    public function settings()
    {
        $preRequisite = $this->repository->getPreRequisite();

        return view('thermalprinter::settings', $preRequisite);
    }

    /**
     * @param Request $request
     */
    public function saveSettings(Request $request)
    {
        $this->repository->create($request->except(['_token']));
        Toastr::success('Printer Settings Saved');
        return redirect()->back();
    }

    /**
     * @param $order_id
     */
    public function printInvoice($order_id)
    {
        try {
            $print = new ThermalPrinter();
            $print->printInvoice($order_id);

            return redirect()->back()->with(['success' => 'Printing Command Sent']);
        } catch (\Exception $e) {

            return redirect()->back()->with(['message' => 'Printing Failed. Connection could not be established.']);
        }
    }

    /**
     * @param $order_id
     */
    public function getOrderDataForPrinting(Request $request)
    {
        //admin data used for special cases
        $adminSettings = PrinterSetting::where('user_id', '1')->first();
        $adminData = json_decode($adminSettings->data);

        $printerSetting = PrinterSetting::where('user_id', Auth::user()->id)->first();

        if ($printerSetting) {
            //if data exists take auth user data...
            $data = json_decode($printerSetting->data);
        } else {
            //else take admin data...
            $data = json_decode($adminSettings->data);
        }

        if ($data->print_width == 3) {
            $char_per_line = 48;
        } else {
            $char_per_line = 30;
        }

        $order = Order::where('id', $request->order_id)->with('restaurant', 'user', 'orderitems', 'orderitems.order_item_addons')->firstOrFail();

        //if print type is null then check the automatic print setting...
        if ($request->print_type == null) {
            if ($data->automatic_printing == 'ONLYINVOICE') {
                $printType = 'invoice';
            } else {
                $printType = null; //for printing both Invoice and KOT
            }
        }

        $finalData = [
            'adminData' => $adminData,
            'printerData' => $data,
            'order' => $order->toArray(),
            'char_per_line' => $char_per_line,
            'timezone' => config('app.timezone'),
            'print_type' => $request->print_type ? $request->print_type : $printType,
        ];

        return response()->json($finalData);

    }

}
