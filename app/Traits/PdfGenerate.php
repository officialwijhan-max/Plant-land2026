<?php

namespace App\Traits;

use Modules\Setting\Entities\PdfFont;
use PDF;
use Mpdf\Mpdf;


trait PdfGenerate
{
    public function getInvoice($view, $data)
    {
        $pdf_font= PdfFont::where('is_active',1)->first();
        $pdf = PDF::loadView($view,compact('data','pdf_font'))->setPaper('a4', 'portrait');
        return $pdf->download("invoice-{$data->invoice_no}.pdf");

    }
    public function getInvoicePdf($view, $data)
    {
        $invoice_no = $data->invoice_no ?? time();

        $html = view($view, compact('data'))->render();

        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'orientation' => 'P',
            'default_font' => 'dejavusans',
        ]);
        $html = mb_convert_encoding($html, 'UTF-8', 'UTF-8');
        $mpdf->WriteHTML($html);
        $mpdf->Output("invoice-{$data->invoice_no}.pdf", 'D');
    }






    public function getPayroll($view, $payrollDetails)
    {
        $pdf_font= PdfFont::where('is_active',1)->first();
        $pdf = PDF::loadView($view,compact('payrollDetails', 'pdf_font'))->setPaper('a4', 'portrait');
        return $pdf->download("Payroll-{$payrollDetails->staff->employee_id}.pdf");
    }

    public function getPdf($title,$view, $data)
    {
        // $data['pdf_font']= PdfFont::where('is_active',1)->first();
        $pdf = PDF::loadView($view,$data);
        return $pdf->download($title);
    }

}
