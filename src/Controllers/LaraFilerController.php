<?php

namespace LaraFiler\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use LaraFiler\Facades\LaraFiler;
use LaraFiler\Response\LaraFilerResponse;
use LaraFiler\Models\LarafmDocument;
use Validator;

class LaraFilerController extends Controller
{

    public function show(Request $request, $slug)
    {
        $size = $request->get('size', null);
        if ($request->get('download')) {
            return LaraFiler::download($slug, $size);
        }
        return LaraFiler::inline($slug, $size);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|max:' . LaraFiler::getMaxUserCapacity(),
        ]);
        if ($validator->fails()) {
            return response()->json($validator->getMessageBag()->get('file') ?? "An error occured", 400);
        }

        $uploader = LaraFiler::upload($request->file('file'));
        $doc = $uploader->eloquent();
        if (!$doc || gettype($doc) === 'string') {
            return response()->json($doc ?: 'Error', 400);
        }
        return response()->json([
            'success' => true,
            'url' => route('larafm.file', $doc->slug),
            'mimetype' => $doc->mimetype,
        ], 201);
    }

    public function remove($slug)
    {
        $document = LarafmDocument::where('created_by', Auth::id())->where('slug', $slug)->first();
        if (!$document) {
            return new LaraFilerResponse('Document not found', 404);
        }
        return LaraFiler::delete($document);
    }
}