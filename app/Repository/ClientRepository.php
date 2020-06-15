<?php

namespace App\Repository;

class ClientRepository extends BaseRepository {

    public function store(array $data)
    {
        $data['name'] = bcrypt($data['name']);

        return parent::store($data);
    }
}
