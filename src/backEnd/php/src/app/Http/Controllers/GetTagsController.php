<?php

namespace App\Http\Controllers;

use App\Services\TagService;
use Illuminate\Http\Request;

class GetTagsController extends Controller{
    private TagService $tagService;

    public function __construct(TagService $tagService){
        $this->tagService = $tagService;
    }

    public function __invoke(Request $request){
        return response()->json(["data" =>$this->tagService->getAll()]);
    }


}