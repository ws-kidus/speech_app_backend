<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $fields = $request->validate([
            "comment" => "string|required",
            "postId" => "string|required",
        ]);

        $comment = $fields['comment'];
        $postId = $fields['postId'];
        $creatorId = 0;
        $likeCount = 0;

        Comment::create([
            'comment' => $comment,
            'creatorId' => $creatorId,
            'postId' => $postId,
            'likeCount' => $likeCount,
        ]);

        $response = [
            "status" => "OK",
            "message" => "Commented successfully",
        ];
        return Response($response, 201);
    }

    public function updateLikeCount(Request $request)
    {

        $field = $request->validate(
            [
                'commentId' => "string|required",
                'likeCount' => "bool|required",
            ]
        );

        $comment = Comment::where(id, $fields['commentId']);

        $likeCount = $comment['likeCount'];

        $field['likeCount'] ? $likeCount += 1 : $likeCount -= 1;

        $comment->update([
            'comment' => $comment['comment'],
            'creatorId' => $comment['creatorId'],
            'postId' => $comment['postId'],
            'likeCount' => $likeCount,
        ]);
        $response = [
            'status' => "OK",
            'message' => "successfully updated",
        ];
        return Response($response, 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(comment $comment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(comment $comment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, comment $comment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(comment $comment)
    {
        //
    }
}
