<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>AuraPad - Catatan Digital & Editor Markdown Premium</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Marked.js CDN for real-time markdown parsing -->
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>

    <!-- Pre-load theme and data to avoid flashing -->
    <script>
        const theme = localStorage.getItem('aurapad-theme') || 'light';
        document.documentElement.setAttribute('data-theme', theme);

        window.AuraData = {
            notes: @json($notes),
            archivedNotes: @json($archivedNotes),
            trashedNotes: @json($trashedNotes),
            categories: @json($categories),
            stats: @json($stats)
        };
    </script>
</head>
<body class="h-screen w-screen overflow-hidden flex select-none">

    <!-- MAIN APP WRAPPER -->
    <div class="flex h-full w-full overflow-hidden">
        
        <!-- SIDEBAR (LEFT) -->
        <aside class="w-80 h-full flex flex-col border-r border-[var(--border-color)] bg-[var(--bg-sidebar)] shrink-0 z-20">
            <!-- App Logo / Header -->
            <div class="p-6 border-b border-[var(--border-color)] flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-500 via-purple-500 to-pink-500 flex items-center justify-center shadow-lg shadow-indigo-500/20 text-white font-bold text-xl">
                        N
                    </div>
                    <div>
                        <h1 class="text-lg font-bold tracking-tight bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 bg-clip-text text-transparent">NOTEPAD</h1>
                        <p class="text-xs text-[var(--text-muted)]">Catatan Digital Estetik</p>
                    </div>
                </div>
            </div>

            <!-- New Note Trigger Button -->
            <div class="px-6 pt-5 pb-2">
                <button id="btn-new-note" class="w-full py-3 px-4 bg-[var(--accent-color)] hover:bg-[var(--accent-hover)] text-white font-medium rounded-xl flex items-center justify-center gap-2 shadow-lg shadow-indigo-500/10 cursor-pointer active:scale-98 transition-all glow-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                    Catatan Baru
                </button>
            </div>

            <!-- Search Bar -->
            <div class="px-6 py-2">
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-[var(--text-muted)]">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                    </span>
                    <input type="text" id="search-input" placeholder="Cari catatan..." class="w-full py-2 pl-9 pr-4 bg-[var(--bg-app)] border border-[var(--border-color)] text-[var(--text-main)] placeholder-[var(--text-muted)] rounded-lg text-sm outline-none focus:border-[var(--border-focus)] transition-all">
                </div>
            </div>

            <!-- Scrollable Sidebar Content -->
            <div class="flex-1 overflow-y-auto px-4 py-3 space-y-6">
                <!-- Navigation Folders -->
                <div class="space-y-1">
                    <h3 class="text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider px-2 mb-2">Folder</h3>
                    
                    <button data-folder="all" class="w-full flex items-center justify-between px-3 py-2 rounded-lg text-sm font-medium text-[var(--text-main)] bg-[var(--bg-app)] cursor-pointer hover:bg-[var(--bg-app)] transition-all folder-btn active-folder">
                        <span class="flex items-center gap-2.5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-indigo-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1-2.5-2.5Z"/><path d="M6 6h10"/><path d="M6 10h10"/></svg>
                            Semua Catatan
                        </span>
                        <span id="count-all" class="text-xs px-2 py-0.5 rounded-full bg-[var(--border-color)] text-[var(--text-muted)]">{{ $stats['total_notes'] - $stats['archived_count'] }}</span>
                    </button>

                    <button data-folder="pinned" class="w-full flex items-center justify-between px-3 py-2 rounded-lg text-sm font-medium text-[var(--text-main)] cursor-pointer hover:bg-[var(--bg-app)]/50 transition-all folder-btn">
                        <span class="flex items-center gap-2.5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-amber-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" x2="12" y1="17" y2="22"/><path d="M5 17h14v-1.76a2 2 0 0 0-.44-1.24l-2.78-3.5A2 2 0 0 1 15 9.26V3.5a1.5 1.5 0 0 0-3 0v5.76a2 2 0 0 1-.78 1.24l-2.78 3.5a2 2 0 0 0-.44 1.24Z"/></svg>
                            Disematkan
                        </span>
                        <span id="count-pinned" class="text-xs px-2 py-0.5 rounded-full bg-[var(--border-color)] text-[var(--text-muted)]">{{ $stats['pinned_count'] }}</span>
                    </button>

                    <button data-folder="archived" class="w-full flex items-center justify-between px-3 py-2 rounded-lg text-sm font-medium text-[var(--text-main)] cursor-pointer hover:bg-[var(--bg-app)]/50 transition-all folder-btn">
                        <span class="flex items-center gap-2.5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-emerald-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="5" x="2" y="3" rx="1"/><path d="M4 8v11a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8"/><path d="M10 12h4"/></svg>
                            Diarsipkan
                        </span>
                        <span id="count-archived" class="text-xs px-2 py-0.5 rounded-full bg-[var(--border-color)] text-[var(--text-muted)]">{{ $stats['archived_count'] }}</span>
                    </button>

                    <button data-folder="trashed" class="w-full flex items-center justify-between px-3 py-2 rounded-lg text-sm font-medium text-[var(--text-main)] cursor-pointer hover:bg-[var(--bg-app)]/50 transition-all folder-btn">
                        <span class="flex items-center gap-2.5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-rose-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
                            Tempat Sampah
                        </span>
                        <span id="count-trashed" class="text-xs px-2 py-0.5 rounded-full bg-[var(--border-color)] text-[var(--text-muted)]">{{ $stats['trashed_count'] }}</span>
                    </button>
                </div>

                <!-- Categories List -->
                <div class="space-y-2">
                    <div class="flex items-center justify-between px-2 mb-1">
                        <h3 class="text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider">Kategori</h3>
                        <button id="btn-add-category" class="text-[var(--text-muted)] hover:text-[var(--accent-color)] cursor-pointer transition-colors p-0.5 rounded hover:bg-[var(--bg-app)]" title="Tambah Kategori">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                        </button>
                    </div>

                    <!-- Add Category Form (Hidden by default) -->
                    <div id="add-category-form" class="hidden p-3 bg-[var(--bg-app)] rounded-xl border border-[var(--border-color)] space-y-3">
                        <input type="text" id="new-category-name" placeholder="Nama kategori..." class="w-full px-2.5 py-1.5 bg-[var(--bg-card)] border border-[var(--border-color)] text-[var(--text-main)] rounded-lg text-xs outline-none focus:border-[var(--border-focus)]">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-semibold text-[var(--text-muted)] uppercase">Warna:</span>
                            <div class="flex gap-1.5" id="category-color-picker">
                                <button data-color="blue" class="w-4 h-4 rounded-full bg-blue-500 cursor-pointer border-2 border-white dark:border-slate-800 scale-110" title="Biru"></button>
                                <button data-color="amber" class="w-4 h-4 rounded-full bg-amber-500 cursor-pointer border border-transparent" title="Kuning"></button>
                                <button data-color="emerald" class="w-4 h-4 rounded-full bg-emerald-500 cursor-pointer border border-transparent" title="Hijau"></button>
                                <button data-color="rose" class="w-4 h-4 rounded-full bg-rose-500 cursor-pointer border border-transparent" title="Merah"></button>
                                <button data-color="purple" class="w-4 h-4 rounded-full bg-purple-500 cursor-pointer border border-transparent" title="Ungu"></button>
                                <button data-color="indigo" class="w-4 h-4 rounded-full bg-indigo-500 cursor-pointer border border-transparent" title="Nila"></button>
                            </div>
                        </div>
                        <div class="flex gap-2 justify-end">
                            <button id="btn-cancel-category" class="px-2.5 py-1 text-[11px] font-medium text-[var(--text-muted)] hover:bg-[var(--border-color)]/50 rounded-md cursor-pointer transition-colors">Batal</button>
                            <button id="btn-save-category" class="px-2.5 py-1 text-[11px] font-medium bg-[var(--accent-color)] hover:bg-[var(--accent-hover)] text-white rounded-md cursor-pointer transition-colors">Simpan</button>
                        </div>
                    </div>

                    <div class="space-y-0.5" id="categories-container">
                        @foreach ($categories as $cat)
                        <div class="category-item-wrapper group flex items-center justify-between rounded-lg hover:bg-[var(--bg-app)]/50 pr-2">
                            <button data-category-id="{{ $cat->id }}" class="flex-1 flex items-center gap-2.5 px-3 py-2 text-sm font-medium text-[var(--text-main)] cursor-pointer text-left category-btn">
                                <span class="text-base">{{ $cat->icon }}</span>
                                <span class="truncate">{{ $cat->name }}</span>
                                <span class="w-2 h-2 rounded-full 
                                    @if($cat->color === 'blue') bg-blue-500 
                                    @elseif($cat->color === 'amber') bg-amber-500 
                                    @elseif($cat->color === 'emerald') bg-emerald-500 
                                    @elseif($cat->color === 'rose') bg-rose-500 
                                    @elseif($cat->color === 'purple') bg-purple-500 
                                    @elseif($cat->color === 'indigo') bg-indigo-500 
                                    @else bg-slate-500 @endif">
                                </span>
                            </button>
                            <span class="text-xs px-2 py-0.5 rounded-full bg-[var(--border-color)] text-[var(--text-muted)] group-hover:hidden">{{ $cat->notes_count }}</span>
                            <button data-action="delete-category" data-id="{{ $cat->id }}" class="hidden group-hover:flex p-1 text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/20 rounded cursor-pointer transition-colors" title="Hapus Kategori">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                            </button>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Bottom Settings (Theme Selector & Info) -->
            <div class="p-6 border-t border-[var(--border-color)] space-y-5 bg-[var(--bg-sidebar)]">

                <!-- User Info & Logout -->
                <div class="flex items-center justify-between gap-2">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-indigo-500 via-purple-500 to-pink-500 flex items-center justify-center text-white font-bold text-xs shrink-0">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-semibold text-[var(--text-main)] truncate">{{ auth()->user()->name }}</p>
                            <p class="text-[10px] text-[var(--text-muted)] truncate">{{ auth()->user()->email }}</p>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="shrink-0">
                        @csrf
                        <button type="submit" title="Keluar" class="p-1.5 text-[var(--text-muted)] hover:text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/20 rounded-lg cursor-pointer transition-all">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" x2="9" y1="12" y2="12"/></svg>
                        </button>
                    </form>
                </div>

                <!-- Theme Switcher Row -->
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider">Aura Tema</span>
                    <div class="flex items-center gap-1 bg-[var(--bg-app)] border border-[var(--border-color)] p-1 rounded-xl">
                        <button data-theme-set="light" class="p-2 rounded-lg text-[var(--text-muted)] hover:text-indigo-500 cursor-pointer transition-colors" title="Light Mode">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="4"/><path d="M12 2v2"/><path d="M12 20v2"/><path d="m4.93 4.93 1.41 1.41"/><path d="m17.66 17.66 1.41 1.41"/><path d="M2 12h2"/><path d="M20 12h2"/><path d="m6.34 17.66-1.41 1.41"/><path d="m19.07 4.93-1.41 1.41"/></svg>
                        </button>
                        <button data-theme-set="dark" class="p-2 rounded-lg text-[var(--text-muted)] hover:text-purple-500 cursor-pointer transition-colors" title="Deep Dark">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/></svg>
                        </button>
                        <button data-theme-set="sepia" class="p-2 rounded-lg text-[var(--text-muted)] hover:text-amber-600 cursor-pointer transition-colors" title="Warm Sepia">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 21h20"/><path d="M12 17H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-8"/><path d="M12 3v14"/></svg>
                        </button>
                        <button data-theme-set="cyberpunk" class="p-2 rounded-lg text-[var(--text-muted)] hover:text-pink-500 cursor-pointer transition-colors" title="Cyberpunk Neon">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m19 11-4-7-4 7h8Z"/><path d="M12 21v-2"/><path d="M5 21v-4a7 7 0 0 1 14 0v4"/><path d="M3 21h18"/></svg>
                        </button>
                    </div>
                </div>

                <!-- Simple Stats Widget -->
                <div class="bg-[var(--bg-app)] rounded-xl p-3 border border-[var(--border-color)] flex items-center justify-around text-center text-xs">
                    <div>
                        <p class="font-bold text-[var(--text-main)]" id="stat-total-notes">{{ $stats['total_notes'] }}</p>
                        <p class="text-[10px] text-[var(--text-muted)] uppercase font-semibold">Catatan</p>
                    </div>
                    <div class="h-6 w-px bg-[var(--border-color)]"></div>
                    <div>
                        <p class="font-bold text-[var(--text-main)]" id="stat-total-words">{{ number_format($stats['total_words']) }}</p>
                        <p class="text-[10px] text-[var(--text-muted)] uppercase font-semibold">Kata</p>
                    </div>
                    <div class="h-6 w-px bg-[var(--border-color)]"></div>
                    <div>
                        <p class="font-bold text-[var(--text-main)]" id="stat-read-time">{{ $stats['total_reading_time'] }}m</p>
                        <p class="text-[10px] text-[var(--text-muted)] uppercase font-semibold">Membaca</p>
                    </div>
                </div>
            </div>
        </aside>

        <!-- NOTES LIST SIDEBAR (MIDDLE) -->
        <section class="w-[350px] h-full flex flex-col border-r border-[var(--border-color)] bg-[var(--bg-app)]/50 shrink-0 z-10">
            <!-- Header section of Notes list -->
            <div class="p-6 border-b border-[var(--border-color)] flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-[var(--text-main)]" id="current-folder-title">Semua Catatan</h2>
                    <p class="text-xs text-[var(--text-muted)]" id="current-folder-desc">Menampilkan catatan aktif Anda</p>
                </div>
                <!-- Action: Empty Trash button (shows only when in Trash folder) -->
                <button id="btn-empty-trash" class="hidden px-2.5 py-1.5 bg-rose-500 hover:bg-rose-600 text-white rounded-lg text-xs font-semibold flex items-center gap-1 shadow-sm shadow-rose-500/10 cursor-pointer transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                    Kosongkan
                </button>
            </div>

            <!-- Notes Cards Scroll Container -->
            <div class="flex-1 overflow-y-auto p-4 space-y-3" id="notes-list-container">
                <!-- Notes will be loaded here dynamically via JavaScript -->
                @forelse ($notes as $note)
                    <div data-note-id="{{ $note->id }}" class="note-list-card p-4 rounded-xl border flex flex-col gap-2.5 cursor-pointer transition-all note-color-{{ $note->color }} @if($loop->first) active-note @endif">
                        <div class="flex items-start justify-between gap-2">
                            <h4 class="font-bold text-sm text-[var(--text-main)] line-clamp-1 flex-1">{{ $note->title ?: 'Catatan Tanpa Judul' }}</h4>
                            <div class="flex items-center gap-1 shrink-0">
                                @if ($note->is_pinned)
                                <span class="text-amber-500" title="Disematkan">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 17v5M5 17h14v-1.76a2 2 0 0 0-.44-1.24l-2.78-3.5a2 2 0 0 1-.78-1.24V3.5a1.5 1.5 0 0 0-3 0v5.76a2 2 0 0 1-.78 1.24l-2.78 3.5a2 2 0 0 0-.44 1.24H5Z"/></svg>
                                </span>
                                @endif
                                @if ($note->is_archived)
                                <span class="text-emerald-500" title="Diarsipkan">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="20" height="5" x="2" y="3" rx="1"/><path d="M4 8v11a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8"/><path d="M10 12h4"/></svg>
                                </span>
                                @endif
                            </div>
                        </div>
                        <p class="text-xs text-[var(--text-muted)] line-clamp-2 leading-relaxed h-8">
                            {{ $note->content ? strip_tags(Str::limit($note->content, 120)) : 'Belum ada konten.' }}
                        </p>
                        <div class="flex items-center justify-between text-[10px] text-[var(--text-muted)] border-t border-[var(--border-color)]/40 pt-2">
                            <span>{{ $note->updated_at->diffForHumans() }}</span>
                            @if ($note->category)
                            <span class="flex items-center gap-1 font-semibold text-[var(--text-main)] bg-[var(--bg-sidebar)]/80 px-2 py-0.5 rounded-full border border-[var(--border-color)]/60">
                                <span>{{ $note->category->icon }}</span>
                                <span>{{ $note->category->name }}</span>
                            </span>
                            @endif
                        </div>
                    </div>
                @empty
                    <!-- Empty State of active notes -->
                    <div class="h-full flex flex-col items-center justify-center text-center p-6 space-y-3" id="empty-state-list">
                        <div class="w-16 h-16 rounded-full bg-[var(--border-color)]/30 flex items-center justify-center text-indigo-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/></svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-sm text-[var(--text-main)]">Kosong</h4>
                            <p class="text-xs text-[var(--text-muted)] mt-1">Belum ada catatan aktif. Klik "+" untuk membuat baru.</p>
                        </div>
                    </div>
                @endforelse
            </div>
        </section>

        <!-- MAIN EDITOR CANVAS (RIGHT) -->
        <main class="flex-1 h-full flex flex-col bg-[var(--bg-editor)] relative z-0">
            <!-- ACTIVE NOTE CONTENT WRAPPER -->
            <div id="active-note-editor" class="h-full flex flex-col">
                
                <!-- TOP HEADER STATUS BAR -->
                <div class="px-8 py-4 border-b border-[var(--border-color)] flex items-center justify-between shrink-0">
                    <!-- Autosave Indicator -->
                    <div class="flex items-center gap-2">
                        <span id="save-indicator-dot" class="w-2.5 h-2.5 rounded-full bg-emerald-500 relative flex shrink-0">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        </span>
                        <span id="save-indicator-text" class="text-xs text-[var(--text-muted)] font-medium">Tersimpan</span>
                    </div>

                    <!-- Actions Panel -->
                    <div class="flex items-center gap-2">
                        <!-- Toggle Pin -->
                        <button id="btn-pin-toggle" class="p-2 text-[var(--text-muted)] hover:text-amber-500 hover:bg-[var(--bg-app)] rounded-xl cursor-pointer transition-all" title="Sematkan Catatan">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" x2="12" y1="17" y2="22"/><path d="M5 17h14v-1.76a2 2 0 0 0-.44-1.24l-2.78-3.5A2 2 0 0 1 15 9.26V3.5a1.5 1.5 0 0 0-3 0v5.76a2 2 0 0 1-.78 1.24l-2.78 3.5a2 2 0 0 0-.44 1.24H5Z"/></svg>
                        </button>

                        <!-- Toggle Archive -->
                        <button id="btn-archive-toggle" class="p-2 text-[var(--text-muted)] hover:text-emerald-500 hover:bg-[var(--bg-app)] rounded-xl cursor-pointer transition-all" title="Arsipkan Catatan">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="5" x="2" y="3" rx="1"/><path d="M4 8v11a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8"/><path d="M10 12h4"/></svg>
                        </button>

                        <!-- Export / Download Dropdown -->
                        <div class="relative inline-block text-left" id="export-dropdown-wrapper">
                            <button id="btn-export-toggle" class="p-2 text-[var(--text-muted)] hover:text-indigo-500 hover:bg-[var(--bg-app)] rounded-xl cursor-pointer transition-all flex items-center gap-1" title="Ekspor Catatan">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/></svg>
                            </button>
                            <!-- Dropdown Menu -->
                            <div id="export-menu" class="hidden absolute right-0 mt-2 w-48 rounded-xl bg-[var(--bg-sidebar)] border border-[var(--border-color)] shadow-xl z-30 py-1.5">
                                <button data-export-format="md" class="w-full text-left px-4 py-2 text-xs font-medium text-[var(--text-main)] hover:bg-[var(--bg-app)] cursor-pointer flex items-center gap-2">
                                    <span>📝</span> Unduh sebagai Markdown (.md)
                                </button>
                                <button data-export-format="txt" class="w-full text-left px-4 py-2 text-xs font-medium text-[var(--text-main)] hover:bg-[var(--bg-app)] cursor-pointer flex items-center gap-2">
                                    <span>📄</span> Unduh sebagai Plaintext (.txt)
                                </button>
                                <div class="h-px bg-[var(--border-color)] my-1.5"></div>
                                <button id="btn-copy-clipboard" class="w-full text-left px-4 py-2 text-xs font-medium text-[var(--text-main)] hover:bg-[var(--bg-app)] cursor-pointer flex items-center gap-2">
                                    <span>📋</span> Salin ke Papan Klip
                                </button>
                            </div>
                        </div>

                        <!-- Spacer -->
                        <div class="h-5 w-px bg-[var(--border-color)] mx-1"></div>

                        <!-- Trash Button (Soft Delete) -->
                        <button id="btn-delete" class="p-2 text-rose-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/20 rounded-xl cursor-pointer transition-all" title="Hapus Catatan">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                        </button>
                    </div>
                </div>

                <!-- NOTE COLOR FRAME WRAPPER (Adapts dynamically to the active note color) -->
                <div id="editor-frame" class="flex-1 flex flex-col p-8 overflow-hidden transition-all duration-300 note-color-default">
                    
                    <!-- EDITOR METADATA & CONTROL ROW -->
                    <div class="flex flex-wrap items-center justify-between gap-4 pb-4 border-b border-[var(--border-color)]/30 shrink-0">
                        <div class="flex items-center gap-3 flex-wrap">
                            <!-- Category Assignment Dropdown -->
                            <div class="relative">
                                <select id="select-category" class="px-3 py-1.5 bg-[var(--bg-sidebar)]/80 text-xs font-semibold text-[var(--text-main)] border border-[var(--border-color)]/80 rounded-full outline-none focus:border-[var(--border-focus)] cursor-pointer appearance-none pr-8 pl-4">
                                    <option value="">📁 Tanpa Kategori</option>
                                    @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->icon }} {{ $cat->name }}</option>
                                    @endforeach
                                </select>
                                <span class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-[var(--text-muted)]">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                                </span>
                            </div>

                            <!-- Color Palette Selector -->
                            <div class="relative" id="color-picker-wrapper">
                                <button id="btn-color-picker" class="px-3 py-1.5 bg-[var(--bg-sidebar)]/80 text-xs font-semibold text-[var(--text-main)] border border-[var(--border-color)]/80 rounded-full outline-none hover:bg-[var(--bg-app)] flex items-center gap-1.5 cursor-pointer">
                                    <span class="w-3.5 h-3.5 rounded-full border border-gray-300 flex inline-block shrink-0 bg-[var(--bg-card)]" id="current-color-dot"></span>
                                    <span>Warna</span>
                                </button>
                                <!-- Floating Color Menu -->
                                <div id="color-menu" class="hidden absolute left-0 mt-2 p-2 bg-[var(--bg-sidebar)] border border-[var(--border-color)] shadow-xl rounded-xl flex gap-1.5 z-30">
                                    <button data-note-color="default" class="w-6 h-6 rounded-full bg-[var(--bg-card)] border border-gray-300 cursor-pointer" title="Bawaan"></button>
                                    <button data-note-color="blue" class="w-6 h-6 rounded-full bg-blue-100 dark:bg-blue-900 border border-blue-300 cursor-pointer" title="Biru Pastel"></button>
                                    <button data-note-color="emerald" class="w-6 h-6 rounded-full bg-emerald-100 dark:bg-emerald-900 border border-emerald-300 cursor-pointer" title="Mint Hijau"></button>
                                    <button data-note-color="amber" class="w-6 h-6 rounded-full bg-amber-100 dark:bg-amber-900 border border-amber-300 cursor-pointer" title="Kuning Madu"></button>
                                    <button data-note-color="purple" class="w-6 h-6 rounded-full bg-purple-100 dark:bg-purple-900 border border-purple-300 cursor-pointer" title="Lilac Ungu"></button>
                                    <button data-note-color="rose" class="w-6 h-6 rounded-full bg-rose-100 dark:bg-rose-900 border border-rose-300 cursor-pointer" title="Rose Merah"></button>
                                </div>
                            </div>
                        </div>

                        <!-- Edit / Preview Switcher -->
                        <div class="flex items-center bg-[var(--bg-sidebar)]/80 p-0.5 border border-[var(--border-color)]/80 rounded-full select-none">
                            <button id="tab-edit" class="px-4 py-1.5 rounded-full text-xs font-semibold text-[var(--accent-color)] bg-[var(--bg-app)] hover:text-[var(--accent-color)] cursor-pointer transition-all flex items-center gap-1.5">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                                Edit Teks
                            </button>
                            <button id="tab-preview" class="px-4 py-1.5 rounded-full text-xs font-semibold text-[var(--text-muted)] hover:text-[var(--text-main)] cursor-pointer transition-all flex items-center gap-1.5">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0z"/><circle cx="12" cy="12" r="3"/></svg>
                                Pratinjau Markdown
                            </button>
                        </div>
                    </div>

                    <!-- TEXTAREA EDITOR CANVAS -->
                    <div class="flex-1 flex flex-col overflow-hidden py-6 relative" id="editor-body-wrapper">
                        <!-- Note Title Field -->
                        <input type="text" id="note-title" placeholder="Catatan Tanpa Judul..." class="w-full text-2xl font-bold bg-transparent text-[var(--text-main)] outline-none border-none placeholder-[var(--text-muted)]/50 pb-4 pr-4">

                        <!-- Content Editing Mode (Active by default) -->
                        <textarea id="note-content" placeholder="Mulai menulis pikiran Anda di sini..." class="w-full flex-1 bg-transparent text-[var(--text-main)] outline-none border-none placeholder-[var(--text-muted)]/50 resize-none font-sans text-[15px] leading-relaxed overflow-y-auto pr-2" tabindex="1"></textarea>

                        <!-- Content Live Markdown Preview (Hidden by default) -->
                        <div id="note-preview" class="hidden w-full flex-1 overflow-y-auto pr-2 markdown-body text-[var(--text-main)]">
                            <!-- Rendered markdown goes here -->
                        </div>
                    </div>

                    <!-- FOOTER WRITER STATS -->
                    <div class="pt-4 border-t border-[var(--border-color)]/30 flex items-center justify-between text-xs text-[var(--text-muted)] shrink-0 font-medium">
                        <div class="flex items-center gap-4">
                            <span><b id="word-counter">0</b> Kata</span>
                            <span><b id="char-counter">0</b> Karakter</span>
                        </div>
                        <div>
                            <span>Estimasi Membaca: <b id="reading-time-counter">0</b> Menit</span>
                        </div>
                    </div>

                </div>
            </div>

            <!-- EMPTY EDITOR STATE (Show when no note or active list is blank) -->
            <div id="empty-editor-overlay" class="hidden absolute inset-0 bg-[var(--bg-editor)] flex flex-col items-center justify-center text-center p-8 space-y-4">
                <div class="w-24 h-24 rounded-2xl bg-gradient-to-tr from-indigo-500/10 via-purple-500/10 to-pink-500/10 border border-[var(--border-color)] flex items-center justify-center text-[var(--accent-color)] shadow-xl">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-[var(--text-main)]">Pilih Catatan</h3>
                    <p class="text-sm text-[var(--text-muted)] mt-1 max-w-sm">Silakan pilih catatan di bilah samping atau buat catatan baru untuk mulai menyusun ide kreatif Anda.</p>
                </div>
                <button id="btn-empty-new-note" class="px-5 py-2.5 bg-[var(--accent-color)] hover:bg-[var(--accent-hover)] text-white font-medium text-sm rounded-xl cursor-pointer transition-all flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                    Buat Catatan Baru
                </button>
            </div>
        </main>

    </div>

    <!-- Toast Notifications System -->
    <div id="toast-container" class="fixed bottom-6 right-6 z-50 flex flex-col gap-2"></div>

</body>
</html>
