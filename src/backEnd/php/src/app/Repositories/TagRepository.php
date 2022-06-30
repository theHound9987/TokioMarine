<?php

namespace App\Repositories;

use App\Exceptions\DB\EmptyTableException;
use App\Exceptions\DB\TagNotFoundException;
use App\Repositories\Interfaces\TagRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use stdClass;

class TagRepository implements TagRepositoryInterface {

    public function all(): Collection|EmptyTableException{
        $results = DB::table('tags')->get();

        if($results->isEmpty()){
            throw new EmptyTableException("no data in tags table");
        }

        return $results;
    }

    public function add(string $name){
        $results = DB::table('tags')->insertGetId(["name" => $name]);

        return $results;
    }

    public function findById(int $id): stdClass{
        $results = DB::table("tags")->where("id",$id)->get();

        if($results->isEmpty()){
            throw new TagNotFoundException("Tag not found by id");
        }

        return $results->first();
    }
}