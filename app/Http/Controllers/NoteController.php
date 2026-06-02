<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class NoteController extends Controller
{
    /**
     * Display a listing of the notes and categories.
     */
    public function index()
    {
        $categories = Category::withCount(['notes' => function ($query) {
            $query->where('is_trashed', false)->where('is_archived', false);
        }])->get();

        $notes = Note::with('category')
            ->where('is_trashed', false)
            ->where('is_archived', false)
            ->orderBy('is_pinned', 'desc')
            ->orderBy('updated_at', 'desc')
            ->get();

        $archivedNotes = Note::with('category')
            ->where('is_trashed', false)
            ->where('is_archived', true)
            ->orderBy('is_pinned', 'desc')
            ->orderBy('updated_at', 'desc')
            ->get();

        $trashedNotes = Note::with('category')
            ->where('is_trashed', true)
            ->orderBy('updated_at', 'desc')
            ->get();

        // Calculate statistics
        $stats = [
            'total_notes' => Note::where('is_trashed', false)->count(),
            'total_words' => Note::where('is_trashed', false)->get()->sum('word_count'),
            'total_chars' => Note::where('is_trashed', false)->get()->sum('char_count'),
            'total_reading_time' => Note::where('is_trashed', false)->get()->sum('reading_time'),
            'pinned_count' => Note::where('is_trashed', false)->where('is_pinned', true)->count(),
            'archived_count' => Note::where('is_trashed', false)->where('is_archived', true)->count(),
            'trashed_count' => Note::where('is_trashed', true)->count(),
        ];

        return view('welcome', compact('categories', 'notes', 'archivedNotes', 'trashedNotes', 'stats'));
    }

    /**
     * Store a newly created note in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $note = Note::create([
            'title' => 'Catatan Baru',
            'content' => '',
            'color' => 'default',
            'is_pinned' => false,
            'is_archived' => false,
            'is_trashed' => false,
            'category_id' => $request->input('category_id'),
        ]);

        // Load category relation if any
        $note->load('category');

        return response()->json([
            'success' => true,
            'message' => 'Catatan berhasil dibuat.',
            'note' => $note
        ], 201);
    }

    /**
     * Update the specified note in storage.
     */
    public function update(Request $request, Note $note): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'color' => 'nullable|string|in:default,blue,emerald,amber,purple,rose',
            'is_pinned' => 'nullable|boolean',
            'is_archived' => 'nullable|boolean',
            'is_trashed' => 'nullable|boolean',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        // Handle string representation of booleans
        foreach (['is_pinned', 'is_archived', 'is_trashed'] as $field) {
            if ($request->has($field)) {
                $validated[$field] = filter_var($request->input($field), FILTER_VALIDATE_BOOLEAN);
            }
        }

        // If category_id is explicitly sent as empty/null, clear it
        if ($request->has('category_id') && empty($request->input('category_id'))) {
            $validated['category_id'] = null;
        }

        $note->update($validated);
        $note->load('category');

        // Re-calculate statistics for reactive UI update
        $stats = [
            'total_notes' => Note::where('is_trashed', false)->count(),
            'total_words' => Note::where('is_trashed', false)->get()->sum('word_count'),
            'total_chars' => Note::where('is_trashed', false)->get()->sum('char_count'),
            'total_reading_time' => Note::where('is_trashed', false)->get()->sum('reading_time'),
            'pinned_count' => Note::where('is_trashed', false)->where('is_pinned', true)->count(),
            'archived_count' => Note::where('is_trashed', false)->where('is_archived', true)->count(),
            'trashed_count' => Note::where('is_trashed', true)->count(),
        ];

        return response()->json([
            'success' => true,
            'message' => 'Catatan berhasil disimpan.',
            'note' => $note,
            'stats' => $stats
        ]);
    }

    /**
     * Remove the specified note from storage (soft or permanent delete).
     */
    public function destroy(Note $note): JsonResponse
    {
        if ($note->is_trashed) {
            // Permanently delete
            $note->delete();
            $message = 'Catatan dihapus secara permanen.';
        } else {
            // Soft delete to trash, unpin it
            $note->update([
                'is_trashed' => true,
                'is_pinned' => false
            ]);
            $message = 'Catatan dipindahkan ke Tempat Sampah.';
        }

        // Re-calculate stats
        $stats = [
            'total_notes' => Note::where('is_trashed', false)->count(),
            'total_words' => Note::where('is_trashed', false)->get()->sum('word_count'),
            'total_chars' => Note::where('is_trashed', false)->get()->sum('char_count'),
            'total_reading_time' => Note::where('is_trashed', false)->get()->sum('reading_time'),
            'pinned_count' => Note::where('is_trashed', false)->where('is_pinned', true)->count(),
            'archived_count' => Note::where('is_trashed', false)->where('is_archived', true)->count(),
            'trashed_count' => Note::where('is_trashed', true)->count(),
        ];

        return response()->json([
            'success' => true,
            'message' => $message,
            'stats' => $stats
        ]);
    }

    /**
     * Restore the specified note from trash.
     */
    public function restore(Note $note): JsonResponse
    {
        $note->update(['is_trashed' => false]);
        $note->load('category');

        $stats = [
            'total_notes' => Note::where('is_trashed', false)->count(),
            'total_words' => Note::where('is_trashed', false)->get()->sum('word_count'),
            'total_chars' => Note::where('is_trashed', false)->get()->sum('char_count'),
            'total_reading_time' => Note::where('is_trashed', false)->get()->sum('reading_time'),
            'pinned_count' => Note::where('is_trashed', false)->where('is_pinned', true)->count(),
            'archived_count' => Note::where('is_trashed', false)->where('is_archived', true)->count(),
            'trashed_count' => Note::where('is_trashed', true)->count(),
        ];

        return response()->json([
            'success' => true,
            'message' => 'Catatan berhasil dipulihkan.',
            'note' => $note,
            'stats' => $stats
        ]);
    }

    /**
     * Empty all notes in trash.
     */
    public function emptyTrash(): JsonResponse
    {
        Note::where('is_trashed', true)->delete();

        $stats = [
            'total_notes' => Note::where('is_trashed', false)->count(),
            'total_words' => Note::where('is_trashed', false)->get()->sum('word_count'),
            'total_chars' => Note::where('is_trashed', false)->get()->sum('char_count'),
            'total_reading_time' => Note::where('is_trashed', false)->get()->sum('reading_time'),
            'pinned_count' => Note::where('is_trashed', false)->where('is_pinned', true)->count(),
            'archived_count' => Note::where('is_trashed', false)->where('is_archived', true)->count(),
            'trashed_count' => 0,
        ];

        return response()->json([
            'success' => true,
            'message' => 'Tempat sampah berhasil dikosongkan.',
            'stats' => $stats
        ]);
    }

    /**
     * Export note content as file download.
     */
    public function export(Note $note, string $format): StreamedResponse
    {
        $title = $note->title ?: 'Catatan Tanpa Judul';
        $content = $note->content ?: '';

        // Clean filename from illegal characters
        $safeTitle = preg_replace('/[^A-Za-z0-9_\-]/', '_', $title);
        $extension = $format === 'md' ? 'md' : 'txt';
        $filename = "{$safeTitle}.{$extension}";

        $headers = [
            'Content-Type' => 'text/plain; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($title, $content, $format) {
            $file = fopen('php://output', 'w');
            
            if ($format === 'md') {
                // Return markdown as is, maybe prefix with title
                fwrite($file, "# {$title}\n\n{$content}");
            } else {
                // For plaintext, strip typical markdown headers/symbols if preferred, 
                // but usually raw notes content is fine as plaintext too.
                fwrite($file, "JUDUL: {$title}\n");
                fwrite($file, "DIBUAT: {$note->created_at}\n");
                fwrite($file, "----------------------------------------\n\n");
                fwrite($file, $content);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
