<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PostRequest;
use App\Http\Resources\PostCollection;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class PostController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $posts = Post::with('user')->paginate(10);
        return new PostCollection($posts);
    }

    public function show($id)
    {
        $post = Post::with('user')->findOrFail($id);
        $this->authorize('view', $post);
        return new PostResource($post);
    }

    // POST /api/posts
    public function store(PostRequest $request)
    {
        $post = Post::create([
            'title' => $request->input('title'),
            'content' => $request->input('content'),
            'user_id' => auth()->id(),
        ]);

        $post->load('user');

        return response()->json([
            'message' => 'Post created successfully.',
            'post' => new PostResource($post)
        ], 201);
    }

    // PUT /api/posts/{id}
    public function update(PostRequest $request, $id)
    {
        $post = Post::findOrFail($id);
        $this->authorize('update', $post);

        $post->update($request->only('title', 'content'));
        $post->load('user');

        return response()->json([
            'message' => 'Post updated successfully.',
            'post' => new PostResource($post)
        ]);
    }

    // DELETE /api/posts/{id}
    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        $this->authorize('delete', $post);

        $post->delete();

        return response()->json([
            'message' => 'Post deleted successfully.'
        ]);
    }

}
