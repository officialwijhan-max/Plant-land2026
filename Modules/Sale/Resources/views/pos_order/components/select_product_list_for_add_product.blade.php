<div class="col-lg-12" id="select_product">
    <div class="primary_input mb-15">
        <label class="primary_input_label" for="">{{__('common.Select Product')}}</label>
        <select class="primary_select mb-15" id="selected_product_id"
                name="selected_product_id[]" multiple>
            @foreach($productSkus as $key => $productSku)
                <option value="{{$productSku->id}}">{{$productSku->product->product_name}}
                    - {{ $productSku->sku }}</option>
            @endforeach
        </select>
        <span class="text-danger" id="selected_product_id_error"></span>

    </div>
</div>
