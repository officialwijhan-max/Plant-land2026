<html>
    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
        <link rel="icon" href="{{url('/')}}/{{isset($fav)?$fav:''}}" type="image/png"/>
        <title>{{ $product_name }} - {{__('common.Print Label')}} </title>
        <meta name="_token" content="{!! csrf_token() !!}"/>
        @if ($page == 20)
            <style type="text/css">
                td {
                    border: 1px dotted lightgray;
                }
        
                
                @media print {
                    table { page-break-inside:auto }
                    tr    { page-break-inside:avoid; page-break-after:auto }
        
                    @page {
                    size: 8.5in 11in;
                    margin-top: 0.5in !important;
                    margin-bottom: 0.5in !important;
                    margin-left: 0.25in !important;
                    margin-right: 0.25in !important;
                    }
                }
            </style> 
        @else
            <style type="text/css">
                td {
                    border: 1px dotted lightgray;
                }
        
                
                @media print {
                    table { page-break-after:always }
        
                    @page {
                    size: 8.5in 11in;
                    margin-top: 0.5in !important;
                    margin-bottom: 0.5in !important;
                    margin-left: 0.25in !important;
                    margin-right: 0.25in !important;
                    }
                }
            </style>
        @endif
       
    </head>
    <body>
        @foreach ($skus as $key => $sku)
            @php
                $label = $labels[$key];
                if ($tax_option == "plain_tax") {
                    $tax_percent = $sku->tax;
                }
                
                if ($tax_option != "none")
                    $price = $sku->selling_price + (($sku->selling_price * $tax_percent)/100);
                else
                    $price = $sku->selling_price;

            @endphp
            
            @if ($page == 20)
                @php
                    if ($label % 2 == 0) {
                        $loop = $label/2;
                    } else {
                        $loop = ($label+1)/2;
                    }
                @endphp
                <table align="center" style="border-spacing: 0.1875in 0in; overflow: hidden !important;">
                    <tbody>
                        @for ($i = 1; $i <= $loop; $i++)
                            <tr>
                                @for ($j=1; $j<=2; $j++)
                                    @php
                                        $label -= 1;
                                    @endphp
                                    @if ($label >= 0)
                                        <td align="center" valign="center">
                                            <div style="overflow: hidden !important;display: flex; flex-wrap: wrap;align-content: center;width: 4in; height: 1in; justify-content: center;">
                                                <div>
                                                    @if ($barcode_label_business_name == 1)
                                                        <b style="display: block !important; font-size: {{ $barcode_label_business_name_font_size }}px; padding-top:0px;">{{app('general_setting')->company_name}}</b>
                                                    @endif
                                                    @if($barcode_label_product_name == 1)
                                                        <span style="display: block !important; font-size: {{ $barcode_label_product_name_font_size }}px"> {{$sku->product->product_name}} </span>
                                                    @endif
                                                    @if($barcode_label_variation == 1)
                                                        <span style="display: block !important; font-size: {{ $barcode_label_variation_font_size }}px">
                                                            {{variantNameFromSku($sku)}}
                                                        </span>
                                                    @endif
                                                    @if($barcode_label_product_price == 1)
                                                        <span style="font-size: {{ $barcode_label_product_price_font_size }}px;"> {{__('product.Price')}} : <b>{{single_price($price)}} </b>
                                                        </span>
                                                    @endif
                                                    <br>
                                                    <img src="data:image/png;base64, {{DNS1D::getBarcodePNG($sku->sku, $sku->barcode_type)}}" alt="barcode"style="max-width:{{ $max_width }}in !important;height:{{ $height }}in !important; display: block;"/>
                                                </div>
                                            </div>
                                        </td>
                                    @endif
                                @endfor
                            </tr>     
                        @endfor
                    </tbody>
                </table>
            @endif
            @if ($page == 30)
                @php
                    if ($label % 3 == 0) {
                        $loop = $label/3;
                    } else {
                        $loop = ($label/3) + 1;
                    }
                @endphp
                <table align="center" style="border-spacing: 0.125in 0in; overflow: hidden !important;">
                    <tbody>
                        @for ($i = 1; $i <= $loop; $i++)
                            <tr>
                                @for ($j=1; $j<=3; $j++)
                                    @php
                                        $label -= 1;
                                    @endphp
                                    @if ($label >= 0)
                                        <td align="center" valign="center">
                                            <div style="overflow: hidden !important;display: flex; flex-wrap: wrap;align-content: center;width: 2.625in; height: 1in; justify-content: center;">
                                            <div>
                                                @if ($barcode_label_business_name == 1)
                                                    <b style="display: block !important; font-size: {{ $barcode_label_business_name_font_size }}px; padding-top:0px;">{{app('general_setting')->company_name}}</b>
                                                @endif
                                                @if($barcode_label_product_name == 1)
                                                    <span style="display: block !important; font-size: {{ $barcode_label_product_name_font_size }}px"> {{$sku->product->product_name}} </span>
                                                @endif
                                                @if($barcode_label_variation == 1)
                                                    <span style="display: block !important; font-size: {{ $barcode_label_variation_font_size }}px">
                                                        {{variantNameFromSku($sku)}}
                                                    </span>
                                                @endif
                                                @if($barcode_label_product_price == 1)
                                                    <span style="font-size: {{ $barcode_label_product_price_font_size }}px;"> {{__('product.Price')}} : <b>{{single_price($price)}} </b>
                                                    </span>
                                                @endif
                                                <br>
                                                <img src="data:image/png;base64, {{DNS1D::getBarcodePNG($sku->sku, $sku->barcode_type)}}" alt="barcode"style="max-width:{{ $max_width }}in !important;height:{{ $height }}in !important; display: block;"/>
                                            </div>
                                            </div>
                                        </td>
                                    @endif
                                @endfor
                            </tr>     
                        @endfor
                    </tbody>
                </table>
            @endif
            @if ($page == 32)
                @php
                    if ($label % 4 == 0) {
                        $loop = $label/4;
                    } else {
                        $loop = ($label/4) + 1;
                    }
                @endphp
                <table align="center" style="border-spacing: 0in 0in; overflow: hidden !important;">
                    <tbody>
                        @for ($i = 1; $i <= $loop; $i++)
                            <tr>
                                @for ($j=1; $j<=4; $j++)
                                    @php
                                        $label -= 1;
                                    @endphp
                                    @if ($label >= 0)
                                        <td align="center" valign="center">
                                            <div style="overflow: hidden !important;display: flex; flex-wrap: wrap;align-content: center;width: 2in; height: 1.25in; justify-content: center;">
                                            <div>
                                                @if ($barcode_label_business_name == 1)
                                                    <b style="display: block !important; font-size: {{ $barcode_label_business_name_font_size }}px; padding-top:0px;">{{app('general_setting')->company_name}}</b>
                                                @endif
                                                @if($barcode_label_product_name == 1)
                                                    <span style="display: block !important; font-size: {{ $barcode_label_product_name_font_size }}px"> {{$sku->product->product_name}} </span>
                                                @endif
                                                @if($barcode_label_variation == 1)
                                                    <span style="display: block !important; font-size: {{ $barcode_label_variation_font_size }}px">
                                                        {{variantNameFromSku($sku)}}
                                                    </span>
                                                @endif
                                                @if($barcode_label_product_price == 1)
                                                    <span style="font-size: {{ $barcode_label_product_price_font_size }}px;"> {{__('product.Price')}} : <b>{{single_price($price)}} </b>
                                                    </span>
                                                @endif
                                                <br>
                                                <img src="data:image/png;base64, {{DNS1D::getBarcodePNG($sku->sku, $sku->barcode_type)}}" alt="barcode"style="max-width:{{ $max_width }}in !important;height:{{ $height }}in !important; display: block;"/>
                                            </div>
                                            </div>
                                        </td>
                                    @endif
                                @endfor
                            </tr>     
                        @endfor
                    </tbody>
                </table>
            @endif
            @if ($page == 40)
                @php
                    if ($label % 4 == 0) {
                        $loop = $label/4;
                    } else {
                        $loop = ($label/4) + 1;
                    }
                @endphp
                <table align="center" style="border-spacing: 0in 0in; overflow: hidden !important;">
                    <tbody>
                        @for ($i = 1; $i <= $loop; $i++)
                            <tr>
                                @for ($j=1; $j<=4; $j++)
                                    @php
                                        $label -= 1;
                                    @endphp
                                    @if ($label >= 0)
                                        <td align="center" valign="center">
                                            <div style="overflow: hidden !important;display: flex; flex-wrap: wrap;align-content: center;width: 2in; height: 1in; justify-content: center;">
                                            <div>
                                                @if ($barcode_label_business_name == 1)
                                                    <b style="display: block !important; font-size: {{ $barcode_label_business_name_font_size }}px; padding-top:0px;">{{app('general_setting')->company_name}}</b>
                                                @endif
                                                @if($barcode_label_product_name == 1)
                                                    <span style="display: block !important; font-size: {{ $barcode_label_product_name_font_size }}px"> {{$sku->product->product_name}} </span>
                                                @endif
                                                @if($barcode_label_variation == 1)
                                                    <span style="display: block !important; font-size: {{ $barcode_label_variation_font_size }}px">
                                                        {{variantNameFromSku($sku)}}
                                                    </span>
                                                @endif
                                                @if($barcode_label_product_price == 1)
                                                    <span style="font-size: {{ $barcode_label_product_price_font_size }}px;"> {{__('product.Price')}} : <b>{{single_price($price)}} </b>
                                                    </span>
                                                @endif
                                                <br>
                                                <img src="data:image/png;base64, {{DNS1D::getBarcodePNG($sku->sku, $sku->barcode_type)}}" alt="barcode"style="max-width:{{ $max_width }}in !important;height:{{ $height }}in !important; display: block;"/>
                                            </div>
                                            </div>
                                        </td>
                                    @endif
                                @endfor
                            </tr>     
                        @endfor
                    </tbody>
                </table>
            @endif
            @if ($page == 50)
                @php
                    if ($label % 5 == 0) {
                        $loop = $label/5;
                    } else {
                        $loop = ($label/5) + 1;
                    }
                @endphp
                <table align="center" style="border-spacing: 0in 0in; overflow: hidden !important;">
                    <tbody>
                        @for ($i = 1; $i <= $loop; $i++)
                            <tr>
                                @for ($j=1; $j<=5; $j++)
                                    @php
                                        $label -= 1;
                                    @endphp
                                    @if ($label >= 0)
                                        <td align="center" valign="center">
                                            <div style="overflow: hidden !important;display: flex; flex-wrap: wrap;align-content: center;width: 1.5in; height: 1in; justify-content: center;">
                                            <div>
                                                @if ($barcode_label_business_name == 1)
                                                    <b style="display: block !important; font-size: {{ $barcode_label_business_name_font_size }}px; padding-top:0px;">{{app('general_setting')->company_name}}</b>
                                                @endif
                                                @if($barcode_label_product_name == 1)
                                                    <span style="display: block !important; font-size: {{ $barcode_label_product_name_font_size }}px"> {{$sku->product->product_name}} </span>
                                                @endif
                                                @if($barcode_label_variation == 1)
                                                    <span style="display: block !important; font-size: {{ $barcode_label_variation_font_size }}px">
                                                        {{variantNameFromSku($sku)}}
                                                    </span>
                                                @endif
                                                @if($barcode_label_product_price == 1)
                                                    <span style="font-size: {{ $barcode_label_product_price_font_size }}px;"> {{__('product.Price')}} : <b>{{single_price($price)}} </b>
                                                    </span>
                                                @endif
                                                <br>
                                                <img src="data:image/png;base64, {{DNS1D::getBarcodePNG($sku->sku, $sku->barcode_type)}}" alt="barcode"style="max-width:{{ $max_width }}in !important;height:{{ $height }}in !important; display: block;"/>
                                            </div>
                                            </div>
                                        </td>
                                    @endif
                                @endfor
                            </tr>     
                        @endfor
                    </tbody>
                </table>
            @endif
            @if ($page == 0)
                @php
                    if ($label % 2 == 0) {
                        $loop = $label/2;
                    } else {
                        $loop = ($label/2) + 1;
                    }
                @endphp
                <table align="center" style="border-spacing: 0in 0in; overflow: hidden !important;">
                    <tbody>
                        @for ($i = 1; $i <= $label/2; $i++)
                            <tr>
                                @for ($j=1; $j<=2; $j++)
                                    <td align="center" valign="center">
                                        <div style="overflow: hidden !important;display: flex; flex-wrap: wrap;align-content: center;width: 1.25in; height: 1in; justify-content: center;">
                                        <div>
                                            @if ($barcode_label_business_name == 1)
                                                <b style="display: block !important; font-size: {{ $barcode_label_business_name_font_size }}px; padding-top:0px;">{{app('general_setting')->company_name}}</b>
                                            @endif
                                            @if($barcode_label_product_name == 1)
                                                <span style="display: block !important; font-size: {{ $barcode_label_product_name_font_size }}px"> {{$sku->product->product_name}} </span>
                                            @endif
                                            @if($barcode_label_variation == 1)
                                                <span style="display: block !important; font-size: {{ $barcode_label_variation_font_size }}px">
                                                    {{variantNameFromSku($sku)}}
                                                </span>
                                            @endif
                                            @if($barcode_label_product_price == 1)
                                                <span style="font-size: {{ $barcode_label_product_price_font_size }}px;"> {{__('product.Price')}} : <b>{{single_price($price)}} </b>
                                                </span>
                                            @endif
                                            <br>
                                            <img src="data:image/png;base64, {{DNS1D::getBarcodePNG($sku->sku, $sku->barcode_type)}}" alt="barcode"style="max-width:{{ $max_width }}in !important;height:{{ $height }}in !important; display: block;"/>
                                        </div>
                                        </div>
                                    </td>
                                @endfor
                            </tr>     
                        @endfor
                    </tbody>
                </table>
            @endif  
        @endforeach
        <script>
            window.print()
        </script>
    </body>
</html>