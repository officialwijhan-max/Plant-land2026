<?php

namespace Modules\Setup\Repositories;

interface CountryRepositoryInterface
{
    public function withPaginate($row_count,$quick_search,$sort,$column);

    public function csvDownload();
}
