@extends('backEnd.master')
@section('mainContent')
    <link rel="stylesheet" href="{{asset('Modules/ModuleManager/Resources/assets/sass/manage_module.css')}}">
    <link rel="stylesheet" href="{{ asset('public/vendor/spondonit/css/parsley.css') }}">

    <section class="admin-visitor-area">
        <div class="container-fluid p-0">
            <div class="row">
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-lg-9 col-xs-6 col-md-6 col-6 no-gutters ">
                            <div class="main-title sm_mb_20 sm2_mb_20 md_mb_20 mb-30 ">
                                <h3 class="mb-0"> {{__('common.Module Manager')}}</h3>
                            </div>
                        </div>

                    </div>

                    <div class="row">
                        <div class="col-lg-12">

                            <table class="display school-table school-table-style" cellspacing="0" width="100%">
                                <thead>
                                <tr>
                                    <th>{{__('setting.SL')}}</th>
                                    <th>{{__('common.Module')}}</th>
                                    <th>{{__('common.Description')}}</th>
                                    <th class="text-right"></th>
                                </tr>
                                </thead>

                                <tbody>
                                <input type="hidden" name="url" id="url" value="{{URL::to('/')}}">
                                @php
                                    $count=1;
                                @endphp
                                @foreach($is_module_available as $row)

                                    @php
                                        $is_module_available = base_path().'/'.'Modules/' . $row->name. '/Providers/' .$row->name. 'ServiceProvider.php';
                                        $configFile = 'Modules/' . $row->name. '/' .$row->name. '.json';
                                        $is_data =  \Modules\ModuleManager\Entities\InfixModuleManager::where('name', $row->name)->first();

                                    try {
                                        $config =file_get_contents(base_path().'/'.$configFile);
                                         }catch (\Exception $exception){
                                            $config =null;
                                         }
                                    @endphp
                                    <tr>
                                        <td>{{@$count++}}</td>
                                        <td>
                                            {{@$row->name}}
                                            @if(file_exists($is_module_available))
                                            <a href="#" class="module_switch" data-id="{{@$row->name}}"
                                               id="module_switch_label{{@$row->name}}"
                                               data-item="{{$row}}">
                                                {{ __('common.'.(moduleStatusCheck($row->name )  == false ? 'Activate': 'Deactivate')) }}


                                            </a>
                                            <div id="waiting_loader"
                                                 class="waiting_loader{{@$row->name}}">
                                                <img
                                                    src="{{asset('public/backEnd/img/demo_wait.gif')}}"
                                                    width="20" height="20"/><br>

                                            </div>
                                            @endif
                                        </td>
                                        <td>
                                            @if(isset($config))
                                                @php
                                                    $name = $row->name;
                                                    $config= json_decode($config);
                                                    if (isset($config->$name->notes[0])){
                                                    echo $config->$name->notes[0];
                                                    echo '<br>';
                                                    echo 'Version: '.$config->$name->versions[0];

                                                    }
                                                @endphp
                                            @else
                                                @php
                                                    if (isset($row->details)){
                                                        echo $row->details;
                                                    }
                                                @endphp
                                            @endif
                                        </td>

                                        <td class="text-right">
                                            @if (file_exists($is_module_available))
                                                <input type="hidden" name="name" value="{{@$row->name}}">
                                            @endif

                                        </td>


                                    </tr>


                                @endforeach
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
@push('scripts')
    <script src="{{asset('public/backEnd/js/module.js')}}"></script>
@endpush
