<?php

namespace Modules\Sale\Repositories;


interface PosOrderRepositoryInterface
{
    public function all();

    public function withPaginate($row_count,$quick_search,$name,$sort,$column,$is_draft,$is_approved);

    public function csvDownload($type, $is_draft, $is_approved, $data);

    public function allListQuery($search_keyword,$filter_date);

    public function allPending();

    public function draftall();

    public function create(array $data);

    public function find($id);

    public function approval($id);

    public function update(array $data,array $payments, $id);

}
