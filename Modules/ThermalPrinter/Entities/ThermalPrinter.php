<?php

namespace Modules\ThermalPrinter\Entities;


use Exception;
use Illuminate\Support\Facades\Auth;
use Mike42\Escpos\Printer;
use Modules\Sale\Entities\Sale;

class ThermalPrinter extends Exception
{
    /**
     * @param $order_id
     */
    public function printInvoice($id)
    {
        $printerSetting = PrinterSetting::where('user_id', Auth::user()->id)->first();

        if (!$printerSetting) {
            $printerSetting = PrinterSetting::where('user_id', '1')->first();
        }

        $data = json_decode($printerSetting->data);

        if ($data->print_width == 3) {
            $char_per_line = 48;
        } else {
            $char_per_line = 30;
        }

        try {
            $main = new Escpos();
            $main->load($data->connector_type, $data->connector_descriptor);
        } catch (Exception $e) {
            throw new Exception('Printing Failed. Connection could not be established.');
        }

        $sale = Sale::with('items', 'items.product', 'shipping')->findOrFail($id);


        //init store header
        $main->printer->setJustification(Printer::JUSTIFY_CENTER);


        $main->printer->feed();
        $main->printer->selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
        $main->printer->text('INVOICE');
        $main->printer->selectPrintMode();
        $main->printer->feed();


        $main->printer->text($this->drawLine($char_per_line));

        $main->printer->feed();

        if (app('general_setting')->company_name) {
            $main->printer->setEmphasis(true);
            $main->printer->setUnderline(1);
            $main->printer->text(app('general_setting')->company_name);
            $main->printer->setUnderline(0);
            $main->printer->setEmphasis(false);
            $main->printer->feed();
        }


        $main->printer->text(app('general_setting')->address . ', ' . app('general_setting')->phone . ', ' . app('general_setting')->email);
        $main->printer->feed();


        $main->printer->text($sale->invoice_no);
        $main->printer->feed();

        $main->printer->text($sale->date);
        $main->printer->feed();


        /* Customer Details */

        $main->printer->setEmphasis(true);
        $main->printer->setUnderline(1);
        $main->printer->text('Customer Details');
        $main->printer->setUnderline(0);
        $main->printer->setEmphasis(false);
        $main->printer->feed();

        if ($sale->customer->name) {
            $main->printer->text($sale->customer->name);
            $main->printer->feed();
        }
        if ($sale->customer->mobile) {
            $main->printer->text($sale->customer->mobile);
            $main->printer->feed();
        }

        if ($sale->customer->email) {
            $main->printer->text($sale->customer->email);
            $main->printer->feed();
        }
        $main->printer->setEmphasis(true);
        $main->printer->feed();


        $main->printer->setJustification();

        $main->printer->setJustification(Printer::JUSTIFY_LEFT);

        //bill item header
        $main->printer->text($this->drawLine($char_per_line));

        $string = $this->columnify($this->columnify($this->columnify('ITEMS', 'QTY', 40, 12, 0, 0, $char_per_line), 'PRICE', 55, 20, 0, 0, $char_per_line), ' ' . 'TOTAL', 75, 25, 0, 0, $char_per_line);
        $main->printer->setEmphasis(true);
        $main->printer->text(rtrim($string));
        $main->printer->feed();
        $main->printer->setEmphasis(false);
        $main->printer->text($this->drawLine($char_per_line));



        foreach ($sale->items as $key => $item){

            //get addons and add to orderitem->addon_name
            $product = $item->productable->product ? $item->productable->product->product_name : $item->productable->name;


                $string = rtrim($this->columnify($this->columnify($this->columnify($product, $item->quantity, 40, 12, 0, 0, $char_per_line), floatval($item->price), 55, 20, 0, 0, $char_per_line), floatval($item->sub_total), 75, 25, 0, 0, $char_per_line));


            $main->printer->text($string);
            $main->printer->feed(1);

        }

        $main->printer->feed();
        $main->printer->text($this->drawLine($char_per_line));

        $main->printer->setJustification(Printer::JUSTIFY_LEFT);



        //Tax
        if ($order->tax != null) {
            $tax = $this->columnify($data->tax_label . ' ', $order->tax . '%', 75, 25, 0, 0, $char_per_line);
            $main->printer->text(rtrim($tax));
            $main->printer->feed();
        }

        //Order Total

        $main->printer->setJustification(Printer::JUSTIFY_CENTER);
        $main->printer->text($this->drawLine($char_per_line));
        $main->printer->setJustification();

        $orderTotal = $this->columnify($data->total_label . ' ', floatval($order->total), 75, 25, 0, 0, $char_per_line);
        $main->printer->setEmphasis(true);
        $main->printer->text(rtrim($orderTotal));
        $main->printer->setEmphasis(false);
        $main->printer->feed();

        $main->printer->setJustification();

        $main->printer->setJustification(Printer::JUSTIFY_CENTER);
        $main->printer->text($this->drawLine($char_per_line));
        $main->printer->setJustification();

        //admin footer
        if (!empty($adminData->footer_title)) {
            $main->printer->setJustification(Printer::JUSTIFY_CENTER);
            $main->printer->feed();
            $main->printer->setUnderline(1);
            $main->printer->text($adminData->footer_title);
            $main->printer->setUnderline(0);
            $main->printer->feed();
            $main->printer->setJustification();
        }

        if (!empty($adminData->footer_sub_title)) {
            //break lines in new array
            $subFooters = preg_split("/\r\n|\n|\r/", $adminData->footer_sub_title);

            $main->printer->setJustification(Printer::JUSTIFY_LEFT);
            $main->printer->feed();
            foreach ($subFooters as $subFooter) {
                $main->printer->text($subFooter);
                $main->printer->feed();
            }
            $main->printer->setJustification();
        }

        $main->printer->feed();

        //store footer
        if (!empty($data->store_footer_title)) {
            $main->printer->setJustification(Printer::JUSTIFY_CENTER);
            $main->printer->feed();
            $main->printer->setUnderline(1);
            $main->printer->text($data->store_footer_title);
            $main->printer->setUnderline(0);
            $main->printer->feed();
            $main->printer->setJustification();
        }

        if (!empty($data->store_footer_subtitle)) {
            //break lines in new array
            $subFooters = preg_split("/\r\n|\n|\r/", $data->store_footer_subtitle);

            $main->printer->setJustification(Printer::JUSTIFY_LEFT);
            $main->printer->feed();
            foreach ($subFooters as $subFooter) {
                $main->printer->text($subFooter);
                $main->printer->feed();
            }
            $main->printer->setJustification();
        }

        $main->printer->feed();

        //cut and close connection for printing
        $main->printer->cut();
        $main->printer->close();

    }

    /**
     * @param $char_per_line
     * @return mixed
     */
    public function drawLine($char_per_line)
    {
        $new = '';
        for ($i = 1; $i < $char_per_line; $i++) {
            $new .= '-';
        }
        return $new . "\n";
    }

    /**
     * @param $addons
     * @return mixed
     */
    public function calculateAddonTotal($addons)
    {
        $total = 0;
        foreach ($addons as $addon) {
            $total += $addon->addon_price;
        }
        return $total;
    }

    /**
     * @param $leftCol
     * @param $rightCol
     * @param $leftWidthPercent
     * @param $rightWidthPercent
     * @param $space
     * @param $remove_for_space
     * @param $char_per_line
     */
    public function columnify($leftCol, $rightCol, $leftWidthPercent, $rightWidthPercent, $space = 2, $remove_for_space = 0, $char_per_line)
    {
        $char_per_line = $char_per_line - $remove_for_space;

        $leftWidth = $char_per_line * $leftWidthPercent / 100;
        $rightWidth = $char_per_line * $rightWidthPercent / 100;

        $leftWrapped = wordwrap($leftCol, $leftWidth, "\n", true);
        $rightWrapped = wordwrap($rightCol, $rightWidth, "\n", true);

        $leftLines = explode("\n", $leftWrapped);
        $rightLines = explode("\n", $rightWrapped);
        $allLines = array();
        for ($i = 0; $i < max(count($leftLines), count($rightLines)); $i++) {
            $leftPart = str_pad($leftLines[$i] ?? '', $leftWidth, ' ');
            $rightPart = str_pad($rightLines[$i] ?? '', $rightWidth, ' ');
            $allLines[] = $leftPart . str_repeat(' ', $space) . $rightPart;
        }
        return implode( "\n", $allLines) . "\n";
    }

}
