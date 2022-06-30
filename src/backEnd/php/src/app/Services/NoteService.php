<?php

namespace App\Services;

use App\Exceptions\DB\NoteNotFoundException;
use App\Repositories\Interfaces\NoteRepositoryInterface;
use App\Repositories\Interfaces\TagRepositoryInterface;
use DateTime;
use Exception;
use Illuminate\Support\Collection;
use stdClass;

class NoteService{


    private NoteRepositoryInterface $noteRepository;
    private TagRepositoryInterface $tagRepository;

    public function __construct(NoteRepositoryInterface $noteRepository, TagRepositoryInterface $tagRepository){
        $this->noteRepository = $noteRepository;
        $this->tagRepository = $tagRepository;
    }

    public function getAll(bool $withRelationships = TRUE):Collection|Exception{

        if($withRelationships){
            $tags = $this->tagRepository->all();


            return $this->noteRepository->all()->transform(function ($item, $key) use ($tags){
                try {
                    $rawTags = $this->noteRepository->getTagsLink($item->id);
                } catch (Exception $e) {
                    $item->tags =[];
                    return $item;
                }

                $item->tags = [];

                foreach ($rawTags as $rawTag){
                    $item->tags[] = $tags->firstWhere('id',$rawTag->tag_id);
                }

                return $item;
            });
        }
        return $this->noteRepository->all();
    }

    public function create(string $title, string $description, DateTime $dateTime, array $tags){
        $id = $this->noteRepository->add($title, $description, $dateTime);
        $DBTags = $this->tagRepository->all();

        foreach ($tags as $tag){
            if(property_exists($tag, 'id') && property_exists($tag, 'name')) {
                $this->noteRepository->addTag($id, $tag->id);
            }elseif(is_int((int)$tag)) {
                $this->noteRepository->addTag($id, $tag);
            }else{
                $DBTag = $DBTags->firstWhere('name', $tag);
                $this->noteRepository->addTag($id, $DBTag->id);
            }

        }

        return $this->find(["id" => $id]);

    }

    public function find(array $condtion): stdClass|NoteNotFoundException{
        $DBTags = $this->tagRepository->all();
        if(array_key_exists("id", $condtion)){
            $note = $this->noteRepository->findById($condtion['id']);
            $rawTags = $this->noteRepository->getTagsLink($note->id);
            $note->tags = [];

//            dd($DBTags);
            foreach ($rawTags as $rawTag){
//                dd($DBTags->firstWhere('id',$rawTag->tag_id), $rawTag);
                $note->tags[] = $DBTags->firstWhere('id',$rawTag->tag_id)->name;
            }

            return $note;
        }

        throw new NoteNotFoundException("Note not found by conditions {$condtion}");
    }


}