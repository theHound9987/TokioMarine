<?php

namespace App\Repositories;

use App\Exceptions\DB\EmptyTableException;
use App\Exceptions\DB\NoteNotFoundException;
use App\Repositories\Interfaces\NoteRepositoryInterface;
use DateTime;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use stdClass;

class NoteRepository implements NoteRepositoryInterface{

    public function all(): Collection|EmptyTableException
    {
        $results = DB::table("notes")->get();

        if($results->isEmpty()){
            throw new EmptyTableException("no data in notes table");
        }

        return $results;
    }

    public function allWithRelationships(): Collection|EmptyTableException
    {
        $results = DB::table("notes")->join()->get();

        if($results->isEmpty()){
            throw new EmptyTableException("no data in notes table");
        }

        return $results;
    }

    public function getTagsLink(int $id): Collection|EmptyTableException{
        $results = DB::table("notes")
            ->leftJoin('notes_tags', 'notes.id', '=', 'notes_tags.note_id')
            ->where("note_id", $id)->get();

        if($results->isEmpty()){
            throw new EmptyTableException("no data in notes table");
        }

        return $results;
    }

    public function add(string $title, string $description, DateTime $dateTime){
        $id = DB::table('notes')->insertGetId(["title" => $title, "description" => $description, "create_at" => $dateTime]);
        return $id;
    }

    public function addTag(int $id, int $tagId){
        DB::table('notes_tags')->insert(['note_id' => $id, 'tag_id' => $tagId]);
    }

    public function findById(int $id): stdClass|NoteNotFoundException
    {
        $results = DB::table("notes")->where("id",$id)->get();

        if($results->isEmpty()){
            throw new NoteNotFoundException("note not found by id");
        }

        return $results->first();
    }
}