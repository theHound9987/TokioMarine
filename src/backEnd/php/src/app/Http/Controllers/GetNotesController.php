<?php

namespace App\Http\Controllers;


use App\Services\NoteService;
use Illuminate\Http\Request;

class GetNotesController extends Controller{


    private NoteService $noteService;

    public function __construct(NoteService $noteService){
        $this->noteService = $noteService;
    }

    public function __invoke(Request $request){
        return response()->json(["data" =>$this->noteService->getAll()]);
    }


}