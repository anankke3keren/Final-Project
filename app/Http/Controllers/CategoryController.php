<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    /**
     * Store a newly created category in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:categories,name',
            'color' => 'required|string|in:blue,amber,emerald,rose,purple,indigo,slate',
            'icon' => 'nullable|string|max:10', // supports single emoji
        ]);

        // Default icons if empty
        if (empty($validated['icon'])) {
            $emojis = ['📁', '📝', '📌', '🏷️', '📓', '🚀', '🎯', '💡', '🎒'];
            $validated['icon'] = $emojis[array_rand($emojis)];
        }

        $category = Category::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil ditambahkan.',
            'category' => $category
        ], 201);
    }

    /**
     * Remove the specified category from storage.
     */
    public function destroy(Category $category): JsonResponse
    {
        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil dihapus.'
        ]);
    }
}
