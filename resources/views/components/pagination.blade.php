@props([ 'models' => null ])
@if($models && $models->count())
<div class="crm_default_table_bottom ">
    <p>  Showing {{($models->currentpage()-1)*$models->perpage()+1}}
        to {{ ($models->currentpage()*$models->perpage()) < $models->total() ? ($models->currentpage()* $models->perpage()) : $models->total()}}
        out of {{ $models->total()}} entries </p>
    <div class="crm_default_table_pagination">
        {{ $models->links() }}
    </div>
</div>
@endif