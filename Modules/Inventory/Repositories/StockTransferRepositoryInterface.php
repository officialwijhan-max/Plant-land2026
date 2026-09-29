<?php

namespace Modules\Inventory\Repositories;


interface StockTransferRepositoryInterface
{
    public function all();

    public function allStockProduct();

    public function allProductListByShowroomListQuery($search_keyword, $row_count, $column, $relational_data, $selected_data);

    public function create(array $data);

    public function find($id);

    public function statusChange($id);

    public function update(array $data, $id);

    public function delete($id);

    public function sendToHouse($id);

    public function stockReceive($id);

    public function stockList();

    public function stock($id);

    public function suggestList($relational_data,$selected_data);
}
