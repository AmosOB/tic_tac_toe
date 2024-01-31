<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Post;
use App\Models\User;
use validator;
use Intervention\Image\Facades\Image;


class PostsController extends Controller
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
        // return view('posts.create');
    }

    /**
     * Store a newly created resource in storage.
     */

    public function uploadPost(Request $request)

    {
        $data = $request->validate([
            'caption' => 'nullable',
            'image'  => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
        ]);

        // Custom validation rule to ensure at least one field is provided
        $request->validate([
            'caption' => 'required_without:image',
            'image' => 'required_without:caption',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('uploads', 'public');
            $image = Image::make(public_path("storage/{$imagePath}"))
                ->fit(1200, 1200);

            // Save the resized image
            $image->save();
        }

        auth()->user()->posts()->create([
            'caption' => $data['caption'],
            'image' => $imagePath,
        ]);

        return redirect()->back()->with('status', 'Posted Successfully!');
    }



    public function show()

    {
        $users = User::with(['posts' => function ($query) {
            $query->orderBy('created_at', 'desc');
        }])->orderBy('id', 'desc')->get();

        return view('home', ['users' => $users]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Find the post by its ID
        $post = Post::findOrFail($id);

        // Check if the currently authenticated user owns the post
        if ($post->user_id === auth()->user()->id) {
            // Delete the associated image if it exists
            if ($post->image) {
                Storage::disk('public')->delete($post->image);
            }

            // Delete the post from the database
            $post->delete();

            return redirect()->back()->with('status', 'Post deleted');
        } else {
            return redirect()->back()->withErrors('You are not authorized to delete this post.');
        }
    }

}
