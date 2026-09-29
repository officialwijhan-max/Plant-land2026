@props(["action" => "#", "method" => "GET", "reset_url" => null])
<div {{ $attributes->merge(['class' => "collapse mb-30"]) }}>
    <div class="white-box card-body">
        <form action="{{ $action }}" method="{{ $method }}" id="{{ $attributes->has('id') ? $attributes->get('id') : 'content_form' }}_form">

            {{ $slot }}

            <div class="row justify-content-center">
                <div class="primary_input">
                    <button type="submit" class="primary-btn radius_30px  fix-gr-bg" id="save_button_parent"><i class="ti-import"></i>{{ trans('common.Import Now') }}</button>
                </div>
            </div>
        </form>   
    </div>
</div>