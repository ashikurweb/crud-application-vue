<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    
    public function index()
    {
        $posts = Post::latest()->paginate(8);

        return response()->json([
            'posts' => $posts,
            'showPagination' => $posts->total() > 8
        ]);
    }

   
    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $validateData = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $post = Post::create( $validateData ); 

        return response()->json([
            'post' => $post,
            'message' => 'Post created successfully'
        ], 201); 
    }

    
    public function show(Post $post)
    {
        return $post;
    }

    
    public function edit($id)
    {
        $post = Post::findOrFail($id);

        return response()->json($post);
    }

    
    public function update(Request $request, Post $post)
    {
        $validateData = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $post = $post->update( $validateData );

        return response()->json([$post = 201]);
    }

    
    public function destroy(Post $post)
    {
        $post->delete();

        return response()->noContent();
    }
}
