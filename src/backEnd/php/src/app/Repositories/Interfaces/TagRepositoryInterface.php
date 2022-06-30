<?php

namespace App\Repositories\Interfaces;

use App\Exceptions\DB\EmptyTableException;
use Illuminate\Support\Collection;
use stdClass;

interface TagRepositoryInterface{

    public function all():Collection|EmptyTableException;

    public function add(string $name);

    public function findById(int $id):stdClass;
}