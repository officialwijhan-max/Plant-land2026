@props(['head' => null, 'filter' => null, 'import' => null, 'table' => null, 'models' => null, 'left_side_btn' => null, 'table_btns'=>null])

<div {{ $attributes->merge(['class' => 'col-lg-12 mb_30']) }}>
    <div class="crm_table_top mb_20">
        <div class="crm_table_btns ">
            <div class="primary_input">
                <select class="primary_select select_div row_filter_option min_width_100" name="filter_no" data-url="{{ request()->url() }}">
                    <option value="10" {{ request("row") == 10 ? 'selected' : '' }}>10</option>
                    <option value="20" {{ request("row") == 20 ? 'selected' : '' }}>25</option>
                    <option value="50" {{ request("row") == 50 ? 'selected' : '' }}>50</option>
                    <option value="100" {{ request("row") == 100 ? 'selected' : '' }}>100</option>
                    <option value="all" {{ request("row") == 'all' ? 'selected' : '' }}>All</option>
                </select>
            </div>
            @if ($left_side_btn)
                {{ $left_side_btn }}
            @endif
        </div>
        @if ($table_btns)
        <div class="table_search">
            <div class="serach_field-area3">
                <div class="search_inner">
                    <div class="input-effect mt-10">
                        <input class="primary-input form-control quick_search" type="text" placeholder="SEARCH" name="quick_search" value="" autocomplete="false">
                        <span class="focus-border"></span>
                    </div>
                    <button type="submit"> <i class="ti-search"></i> </button>
                </div>
            </div>
        </div>
        @endif
        
        @if ($table_btns)
            <div class="pdf_btns">
                {{ $table_btns }}
            </div>
        @endif
    </div>


    @if ($filter)
        <div {{ $filter->attributes->merge(['class' => 'collapse mb-30']) }}>
            <div class="white-box card-body">
                <form action="" method="get"
                    id="{{ $filter->attributes->has('id') ? $filter->attributes->get('id') : 'content' }}_form">

                    {{ $filter }}

                    <div class="row justify-content-center">
                        <div class="primary_input">
                            <button type="submit" class="primary-btn radius_30px  fix-gr-bg" id="save_button_parent"><i
                                    class="ti-search"></i>{{ __('common.Search') }}</button>
                        </div>

                        <div class="primary_input ml-2">
                            <a href="javascript:void(0);" class="primary-btn radius_30px  fix-gr-bg" id="save_button_parent"><i
                                    class="fa fa-refresh"></i>{{ __('common.Reset') }}</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif

    @if ($import)
        <div {{ $import->attributes->merge(['class' => 'collapse mb-30']) }}>
            <div class="white-box card-body">
                <form action="" method="get"
                    id="{{ $import->attributes->has('id') ? $import->attributes->get('id') : 'content' }}_form">

                    {{ $import }}

                    <div class="row justify-content-center">
                        <div class="primary_input">
                            <button type="submit" class="primary-btn radius_30px  fix-gr-bg" id="save_button_parent"><i
                                    class="ti-search"></i>{{ trans('common.Import Now') }}</button>
                        </div>

                        <div class="primary_input ml-2">
                            <a href="javascript:void(0);" class="primary-btn radius_30px  fix-gr-bg" id="save_button_parent"><i
                                    class="fa fa-refresh"></i>{{ __('common.Reset') }}</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif

    @if ($table)
        <div class="table-responsive">
            <table {{ $table->attributes->merge(['class' => 'table crm_default_table mb_30']) }}>
                {{ $table }}
            </table>
        </div>

        <x-pagination :models="$models"></x-pagination>
    @endif
</div>
