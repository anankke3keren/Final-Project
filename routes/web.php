<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\AuthController;

// ============================================
// LANDING PAGE (Public - accessible by guests)
// ============================================
Route::get('/landing', function () {
    if (auth()->check()) {
        return redirect()->route('home');
    }
    return view('landing');
})->name('landing');

// ============================================
// AUTH ROUTES (Guest only)
// ============================================
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ============================================
// PROTECTED APP ROUTES (Auth required)
// ============================================
Route::middleware('auth')->group(function () {

    // Web interface & Note views
    Route::get('/', [NoteController::class, 'index'])->name('home');

    // Notes endpoints (AJAX & Exports)
    Route::post('/notes', [NoteController::class, 'store'])->name('notes.store');
    Route::put('/notes/{note}', [NoteController::class, 'update'])->name('notes.update');
    Route::delete('/notes/{note}', [NoteController::class, 'destroy'])->name('notes.destroy');
    Route::post('/notes/{note}/restore', [NoteController::class, 'restore'])->name('notes.restore');
    Route::post('/notes/trash/empty', [NoteController::class, 'emptyTrash'])->name('notes.empty-trash');
    Route::get('/notes/{note}/export/{format}', [NoteController::class, 'export'])->name('notes.export');

    // Categories endpoints
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    // Profile endpoints
    Route::post('/profile/picture', [AuthController::class, 'updateProfilePicture'])->name('profile.picture.update');
});
