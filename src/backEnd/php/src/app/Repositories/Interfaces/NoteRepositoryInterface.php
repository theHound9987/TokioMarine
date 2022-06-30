<?php

namespace App\Repositories\Interfaces;

use App\Exceptions\DB\EmptyTableException;
use App\Exceptions\DB\NoteNotFoundException;
use DateTime;
use Illuminate\Support\Collection;
use stdClass;

interface NoteRepositoryInterface{

    public function all():Collection|EmptyTableException;

    public function allWithRelationships():Collection|EmptyTableException;

    public function getTagsLink(int $id):Collection|EmptyTableException;

    public function add(string $title, string $description, DateTime $dateTime);

    public function addTag(int $id, int $tagId);

    public function findById(int $id):stdClass|NoteNotFoundException;
}