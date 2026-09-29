@props(["action" => "#", "method" => "GET", "reset_url" => null])
<div {{ $attributes->merge(['class' => "collapse mb-30"]) }}>
    <div class="white-box card-body">
        <form action="{{ $action }}" method="{{ $method }}" id="{{ $attributes->has('id') ? $attributes->get('id') : 'content_form' }}_form">

            {{ $slot }}

            <div class="row justify-content-center">
                <div class="primary_input">
                    <button type="submit" class="primary-btn radius_30px  fix-gr-bg" id="save_button_parent"><i class="ti-search"></i>{{ trans('common.Search') }}</button>
                </div>
@if($reset_url)
                <div class="primary_input ml-2">
                    <a href="{{ $reset_url }}" class="primary-btn radius_30px  fix-gr-bg" id="save_button_parent"><i
                            class="fa fa-refresh"></i>{{ trans('common.Reset') }}</a>
                </div>
                @endif
            </div>
        </form>   
    </div>
</div>