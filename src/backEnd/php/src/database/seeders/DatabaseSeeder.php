<?php

namespace Database\Seeders;

use App\Services\NoteService;
use App\Services\TagService;
use DateTime;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(NoteService $noteService, TagService $tagService){
        $tagService->create("tag 1");
        $tagService->create("tag 2");
        $tagService->create("tag 3");

        $tags = $tagService->getAll();

//        dd($tags);

        $noteService->create("Title 1", "Note Description 1", new DateTime(), $tags->random(2)->toArray());
        $noteService->create("Title 2", "Note Description 2", new DateTime(), $tags->random(2)->toArray());
        $noteService->create("Title 3", "Note Description 3", new DateTime(), $tags->random(2)->toArray());

    }
}
