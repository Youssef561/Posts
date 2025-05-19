<?php
namespace App\Services;

use App\Models\Post;
use App\Repositories\PostRepository;

class PostService
{
    protected $postRepo;

    public function __construct(PostRepository $postRepo)
    {
        $this->postRepo = $postRepo;
    }

    public function listPosts()
    {
        return $this->postRepo->getAll();
    }

    public function store(array $data)
    {
        return $this->postRepo->create($data);
    }

    public function update(Post $post, array $data)
    {
        return $this->postRepo->update($post, $data);
    }

    public function delete(Post $post)
    {
        return $this->postRepo->delete($post);
    }

}
