<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Image;
use App\Models\Like;
use App\Models\Post;
use App\Models\Repost;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PostController extends Controller
{
    public function fetchAllPosts(Request $request)
    {
        $page = $request->page;
        $limit = $request->limit ?? 20;
        $offset = ($page - 1) * $limit;

        $result = [];

        $posts = Post::skip($offset)->take($limit)->get();

        foreach ($posts as $post) {
            $id = $post->id;
            $speech = $post->speech;
            $creatorId = $post->creatorId;
            $creatorName = "kidus"; //todo
            $liked = Like::where('userId', 1)->exists();
            $likeCount = Like::where('postId', $id)->count();
            $commentCount = Comment::where('postId', $id)->count();
            $repostCount = Repost::where('originalPostId', $id)->count();
            $images = Image::where('postId', $post['id'])->get();
            $createdAt = $post->created_at;

            $p = [
                'id' => $id,
                'speech' => $speech,
                'creatorId' => $creatorId,
                'creatorName' => $creatorName,
                'liked' => $liked,
                'likeCount' => $likeCount,
                'commentCount' => $commentCount,
                'repostCount' => $repostCount,
                'images' => $images,
                'createAt' => $createdAt,
            ];

            array_push($result, $p);
        }

        $totalLength = Post::count();

        $response = [
            'status' => 'OK',
            'result' => $result,
            'total' => $totalLength,
        ];

        return Response($response, 200);

    }

    public function createPost(Request $request)
    {
        $fields = $request->validate([
            'speech' => "string|required",
        ]);

        $speech = $fields['speech'];

        $creatorId = 1; //todo

        $post = Post::create([
            'speech' => $speech,
            'creatorId' => $creatorId,
        ]);

        $files = $request->file('images');

        if ($files) {
            foreach ($files as $file) {
                $extension = $file->getClientOriginalExtension();
                $imageName = $file->getClientOriginalName();
                $imagePath = public_path() . '/uploads';

                $file->move($imagePath, $imageName);

                Image::create([
                    'postId' => $post->id,
                    'creatorId' => $creatorId,
                    'imageTitle' => $imageName,
                    'imageUrl' => $imagePath,
                ]);
            }}

        $response = [
            'status' => "Ok",
            'message' => "post created successfully",

        ];
        return Response($response, 201);
    }

    public function updateLikeStatus(Request $request)
    {
        $fields = $request->validate([
            'postId' => "string|required",
            'liked' => "bool|required",
        ]);

        $postId = $fields['postId'];
        $liked = $fields['liked'];

        $userId = 1;

        if ($liked) {
            $like = Like::create([
                'userId' => $userId,
                'postId' => $postId,
                'isPost' => true,
            ]);

            $message = "added to liked";

        } else {
            $userLikes = Like::where('userId', $userId)->get();

            foreach ($userLikes as $like) {
                if ($like->postId == $postId) {
                    $like->delete();
                }
            }

            $message = "removed from liked";
        }

        $response = [
            'status' => "OK",
            'message' => $message,
        ];
        return Response($response, 200);
    }
}
