<?php

namespace App\Http\Controllers;

use App\Services\NoteService;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PostNoteController extends Controller {
    private NoteService $noteService;

    public function __construct(NoteService $noteService){
        $this->noteService = $noteService;
    }

    public function __invoke(Request $request){


        $validator = Validator::make($request->input(),[
            "title" => ['required',],
            "description" => ['required',],
        ]);

        if($validator->passes()) {
            return response()->json(["data" =>
                $this->noteService->create($request->post("title"), $request->post("description"), new DateTime(), $request->post('tags'))]);
        }else{
            return response()->json($request->all());
        }
    }


}