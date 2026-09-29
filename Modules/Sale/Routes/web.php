<?php

use Illuminate\Support\Facades\Route;

Route::prefix('sale')->middleware('auth')->group(function() {

    Route::post('/get_sale_details_specific', 'SalesController@orderDetailSpecific')->name('get_sale_details_specific');
    Route::group(['prefix' => 'pos'], function () {
        Route::get('pos-order', 'POSController@index')->name('pos-order.index')->middleware('permission');
        Route::resource('pos-order', 'POSController');

        Route::get('pos-order-products', 'POSController@products')->name('pos-order.products')->middleware('permission');
        Route::get('get-draft-list', 'POSController@draftList')->name('get_draft_list');
        Route::post('get-draft-list-product-info', 'POSController@draftListProductInfo')->name('get_draft_list_product_info');

        Route::post('load-more-product', 'POSController@loadProduct')->name('pos-order.load.Product');
        Route::post('load-more-product-grid', 'POSController@loadProductGrid')->name('pos-order.load.ProductGrid');
        Route::post('find-product', 'POSController@loadProduct')->name('pos-order.find.Product');
        Route::post('pos-find-products', 'POSController@storeProduct')->name('pos.find.products');
        Route::post('pos-multiple-payment', 'POSController@multiple_payment_modal')->name('pos_multiple_payment');
        Route::post('pos-cash-payment', 'POSController@cash_payment_modal')->name('pos_cash_payment');

        Route::get('clear/products', 'POSController@clearProducts')->name('clear.products');
        Route::get('/pos-sales-approve/{id}', 'POSController@statusChange')->name('sale_pos.approve')->middleware('permission');
        Route::post('/approve-all-pos-sales', 'POSController@approve_all_sales')->name('sale_pos.all_approve_instant');

        Route::post('get-change-pos-view', 'POSController@get_change_pos_view')->name('get_change_pos_view');
        Route::post('/product-modal-for-select', 'POSController@product_modal_for_select')->name('pos.product_modal_for_select');
    });


    Route::get('/', 'SaleController@index');

    Route::middleware('permission')->group(function() {
       if(moduleStatusCheck('ExtraUser')){
            Route::get('/affiliate', 'SaleController@affiliate')->name('affiliate.index');
            Route::get('/commissioner', 'SaleController@commissioner')->name('commissioner.index');
        }

        Route::resource('sale','SaleController');
        Route::post('/invoice-details', 'SaleController@invoiceDetails')->name('invoice.details');
        Route::post('/quotation-to-store', 'SaleController@quotation_to_store')->name('sale.quotation_to_store');
        Route::post('/store-shipping', 'SaleController@storeShipping')->name('store.shipping');
        Route::get('/sale-delete-modal/{id}', 'SaleController@destroy')->name('sale.delete');

        Route::post('/get-due-invoice-list', 'SalesController@get_due_invoice_list')->name('sales.get_due_invoice_list');
    });

    Route::post('/sale_order_details', 'SaleController@orderDetails')->name('get_sale_details');
    Route::get('sale-return-create', 'SaleController@make_return_list')->name('sale.sale_return_list');
    Route::get('sale-return-details/{id}', 'SaleController@return_details')->name('sale.return_detail_show');
    Route::get('sale-print-view/{id}', 'SaleController@print_view')->name('sale.print_view');
    Route::get('sale-challan-print-view/{id}', 'SaleController@challan_print_view')->name('sale.challan_print_view');

    Route::get('/sale-pdf/{id}', 'SaleController@getPdf')->name('sale.pdf');
    Route::get('/sale-challan-pdf/{id}', 'SaleController@getChallanPdf')->name('sale.challan_pdf');

    Route::get('/sale-configurations', 'SaleController@saleConfiguration')->name('sale.configurations');
    //Purchase Return List
    Route::post('/product_add','SaleController@storeProduct')->name('sale.product.add');
    Route::get('/sale-return-list', 'SaleController@returnList')->name('sale.return.index')->middleware('permission');
    Route::get('/sale-return/{id}', 'SaleController@saleReturn')->name('sale.return');
    Route::get('/sale-return-pdf/{id}', 'SaleController@saleReturnPdf')->name('sale.return.pdf');
    Route::get('/sale-return-print/{id}', 'SaleController@saleReturnPrint')->name('sale.return.print');
    Route::post('/sale-return-update/{id}', 'SaleController@returnItem')->name('sale.return.update');
    Route::get('/sale-return-excelExport', 'SaleController@fileExport')->name('sale.excel');
    Route::post('/customer-details', 'SaleController@customerDetails')->name('customer.details');
    Route::get('/sale-payments/{id}', 'SaleController@payments')->name('sale.payment');
    Route::get('/sale-return-payments/{id}', 'SaleController@returnPayments')->name('sale.return.payment');
    Route::post('/sale-payments-details', 'SaleController@payments_details_sale')->name('sale.get_sale_payment_details');
    Route::get('/sale-clone/{id}', 'SaleController@cloneSale')->name('sale.clone');
	Route::post('/sale-order-preview', 'SaleController@getPreview')->name('sale.order.preview');
    Route::post('/sale-store-payments/{id}', 'SaleController@savePayment')->name('sale.store.payment');
    Route::post('/sale-return-store-payments/{id}', 'SaleController@saleReturnSavePayment')->name('sale_return.store.payment');
    Route::get('/sale-send-mail/{id}', 'SaleController@send_mail_quotation')->name('sale.send_mail');
    Route::get('/sale-quotation-convert/{id}','SaleController@convertToQuotation')->name('sale.convertTosale');
    Route::get('/sale-due-list','SaleController@dueList')->name('sale.due.list');
    Route::post('/item-session-delete','SaleController@itemSessionDelete')->name('item.session.delete');

    //Conditional Sale
    Route::get('/conditional-sales-approve/{id}', 'SaleController@statusChange')->name('conditional.sale.approve');
    Route::get('/return-sales-approve/{id}', 'SaleController@returnApprove')->name('return.sale.approve');
    Route::post('/order-sales-receive', 'SaleController@saleOrder')->name('sale.order.receive');
    Route::post('/sale-shipping_info', 'SaleController@shippingInfo')->name('sale.shipping_info');
    Route::post('/sale-item_delete', 'SaleController@itemDestroy')->name('item.delete');
    Route::get('/due/invoice-list', 'SaleController@invoiceList')->name('due.invoice.list');

    Route::post('/product-modal-for-select', 'SaleController@product_modal_for_select')->name('sale.product_modal_for_select');
});
    Route::get('/conditional-sales', 'SaleController@conditionalSale')->name('conditional.sale.index')->middleware('permission');
    Route::resource('conditional-sale','SaleController');
