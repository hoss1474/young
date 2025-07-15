<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;

class PostApiController extends Controller
{
    // لیست همه پست‌ها
    public function index()
    {
        return Post::all();
    }

    // نمایش یک پست خاص
    public function show(Post $post)
    {
        return $post;
    }

    // ایجاد پست جدید
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'main_image' => 'required|image',
            'short_description' => 'required|string|max:500',
            'content' => 'required|string',
            'gallery_images' => 'nullable|array|max:5',
            'gallery_images.*' => 'image',
            'author' => 'required|string|max:255',
        ]);

        // آپلود عکس اصلی
        if ($request->hasFile('main_image')) {
            $data['main_image'] = $request->file('main_image')->store('posts');
        }

        // آپلود تصاویر گالری
        if ($request->hasFile('gallery_images')) {
            $galleryPaths = [];
            foreach ($request->file('gallery_images') as $image) {
                $galleryPaths[] = $image->store('posts/gallery');
            }
            $data['gallery_images'] = $galleryPaths;
        }

        $post = Post::create($data);
        return response()->json($post, 201);
    }

    // به‌روزرسانی پست
    public function update(Request $request, Post $post)
    {
        $data = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'main_image' => 'sometimes|image',
            'short_description' => 'sometimes|required|string|max:500',
            'content' => 'sometimes|required|string',
            'gallery_images' => 'nullable|array|max:5',
            'gallery_images.*' => 'image',
            'author' => 'sometimes|required|string|max:255',
        ]);

        if ($request->hasFile('main_image')) {
            $data['main_image'] = $request->file('main_image')->store('posts');
        }

        if ($request->hasFile('gallery_images')) {
            $galleryPaths = [];
            foreach ($request->file('gallery_images') as $image) {
                $galleryPaths[] = $image->store('posts/gallery');
            }
            $data['gallery_images'] = $galleryPaths;
        }

        $post->update($data);
        return response()->json($post);
    }

    // حذف پست
    public function destroy(Post $post)
    {
        $post->delete();
        return response()->json(null, 204);
    }
}
