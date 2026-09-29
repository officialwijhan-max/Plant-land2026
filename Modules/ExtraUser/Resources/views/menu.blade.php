@if(permissionCheck('extrauser.store'))
    <li>
        <a href="{{route('extrauser.create')}}"
           class="{{ spn_active_link(['extrauser.create']) }}"> {{__('extrauser::extrauser.Create Extra User')}} </a>
    </li>
@endif
@if(permissionCheck('agencies.index'))
    <li>
        <a href="{{route('agencies.index')}}"
           class="{{ spn_active_link(['agencies.index']) }}"> {{__('extrauser::extrauser.Agencies')}} </a>
    </li>
@endif
@if(permissionCheck('strategic-partner.index'))
    <li>
        <a href="{{route('strategic-partner.index')}}"
           class="{{ spn_active_link(['strategic-partner.index']) }}"> {{__('extrauser::extrauser.Strategic Partners')}} </a>
    </li>
@endif
@if(permissionCheck('benches.index'))
    <li>
        <a href="{{route('benches.index')}}"
           class="{{ spn_active_link(['benches.index']) }}"> {{__('extrauser::extrauser.Benches')}} </a>
    </li>
@endif
@if(permissionCheck('kiosks.index'))
    <li>
        <a href="{{route('kiosks.index')}}"
           class="{{ spn_active_link(['kiosks.index']) }}"> {{__('extrauser::extrauser.Kiosks')}} </a>
    </li>
@endif




