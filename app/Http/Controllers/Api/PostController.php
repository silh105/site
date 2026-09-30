<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\Post;
use App\Models\PostImage;

class PostController extends Controller {
    public function index(Request $request) {
        $user = $request->user();
        $query = Post::with(['user.profile', 'category', 'images'])->latest();

        if ($request->filled('category_id') && $request->category_id !== 'all') {
            $query->where('category_id', $request->category_id);
        } elseif ($request->boolean('my_feed') && $user) {
            $catIds = $user->categories()->pluck('categories.id')->toArray();
            if (!empty($catIds)) $query->whereIn('category_id', $catIds);
        }

        return response()->json($query->paginate(15));
    }

    public function store(Request $request) {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'text' => 'required|string|max:5000',
            'video_url' => 'nullable|url|max:255',
            'images.*' => 'nullable|image|max:3072'
        ]);

        return DB::transaction(function () use ($request) {
            $post = Post::create([
                'user_id' => $request->user()->id,
                'category_id' => $request->category_id,
                'text' => $request->text,
                'video_url' => $request->video_url,
            ]);

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $file) {
                    $path = $file->store('posts', 'public');
                    PostImage::create(['post_id' => $post->id, 'image_url' => '/storage/' . $path]);
                }
            }

            return response()->json($post->load(['user.profile', 'category', 'images']), 201);
        });
    }

    public function destroy(Request $request, Post $post) {
        if ($post->user_id !== $request->user()->id && !$request->user()->isAdmin()) {
            return response()->json(['message' => 'Запрещено'], 403);
        }
        foreach ($post->images as $img) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $img->image_url));
        }
        $post->delete();
        return response()->json(['success' => true]);
    }
}