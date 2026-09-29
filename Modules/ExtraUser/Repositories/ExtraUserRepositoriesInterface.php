<?php

namespace Modules\ExtraUser\Repositories;

interface ExtraUserRepositoriesInterface
{

    public function agency();

    public function strategicPartner();

    public function benches();

    public function kiosks();

    public function create(array $data);
    public function find($id);

    public function update($id, array $data);

}
