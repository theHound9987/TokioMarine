<?php

namespace App\Http\Controllers;

use App\Services\TagService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PostTagController extends Controller{
    private TagService $tagService;

    public function __construct(TagService $tagService){
        $this->tagService = $tagService;
    }

    public function __invoke(Request $request){

        $validator = Validator::make($request->input(),[
            "name" => ['required',],
        ]);


        if($validator->passes()) {
            return response()->json($this->tagService->create($request->post('name')));
        }else{
            return response()->json($request->all());
        }


    }


}