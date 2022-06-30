<?php

namespace App\Services;

use App\Exceptions\DB\EmptyTableException;
use App\Exceptions\DB\NoteNotFoundException;
use App\Exceptions\DB\TagNotFoundException;
use App\Repositories\Interfaces\TagRepositoryInterface;
use Illuminate\Support\Collection;
use stdClass;

class TagService{
    private TagRepositoryInterface $tagRepository;

    public function __construct(TagRepositoryInterface $tagRepository){
        $this->tagRepository = $tagRepository;
    }

    public function getAll():Collection|EmptyTableException{
        return $this->tagRepository->all();
    }

    public function create(string $name){
        $id =$this->tagRepository->add($name);

        return $this->find(["id" => $id]);

    }

    public function find(array $condtion): stdClass|TagNotFoundException{
        if(array_key_exists("id", $condtion)){
            return $this->tagRepository->findById($condtion['id']);
        }

        throw new TagNotFoundException("Tag not found by {$condtion}");
    }


}