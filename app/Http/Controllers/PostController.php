<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Services\PostService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\PostRequest;
use App\Http\Requests\UpdatePostRequest;


class PostController extends Controller
{

    use AuthorizesRequests;

    protected $service;

    public function __construct(PostService $service)
    {
        $this->service = $service;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('posts.index');
    }

    public function list()
    {
        $posts = $this->service->listPosts();
        $currentUser = auth()->user();

        return response()->json([
            'posts' => $posts,
            'current_user' => $currentUser,
        ]);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //return view('posts.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PostRequest $request)
    {
        $data = $request->validated();           // Only validated title and content
        $data['user_id'] = auth()->id();        // Add the authenticated user

        $this->service->store($data);            // create the post

        return response()->json(['status' => 'success']);
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PostRequest $request, Post $post)
    {
        $this->authorize('update', $post);

        $this->service->update($post, $request->validated());
//        $post->update($request->validated());

        return response()->json(['status' => 'success']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);

        $this->service->delete($post);

        return response()->json(['status' => 'success']);
    }


}
