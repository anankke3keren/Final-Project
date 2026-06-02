/**
 * AuraPad Pro - SPA Interactivity & Autosave Engine
 */

document.addEventListener('DOMContentLoaded', () => {
    // ----------------------------------------------------
    // 1. STATE MANAGEMENT
    // ----------------------------------------------------
    let state = {
        notes: [],
        archivedNotes: [],
        trashedNotes: [],
        categories: [],
        stats: {},
        currentFolder: 'all',          // 'all', 'pinned', 'archived', 'trashed'
        currentCategoryId: null,       // filter by category ID
        activeNoteId: null,            // current note ID being edited
        searchQuery: '',
        autosaveTimer: null,
        previewMode: false,
        activeCategoryColor: 'blue'    // for new category form
    };

    // DOM Elements Cache
    const el = {
        btnNewNote: document.getElementById('btn-new-note'),
        btnEmptyNewNote: document.getElementById('btn-empty-new-note'),
        searchInput: document.getElementById('search-input'),
        notesListContainer: document.getElementById('notes-list-container'),
        currentFolderTitle: document.getElementById('current-folder-title'),
        currentFolderDesc: document.getElementById('current-folder-desc'),
        btnEmptyTrash: document.getElementById('btn-empty-trash'),
        
        // Editor Canvas
        activeNoteEditor: document.getElementById('active-note-editor'),
        emptyEditorOverlay: document.getElementById('empty-editor-overlay'),
        editorFrame: document.getElementById('editor-frame'),
        saveIndicatorDot: document.getElementById('save-indicator-dot'),
        saveIndicatorText: document.getElementById('save-indicator-text'),
        
        // Editor Fields
        noteTitle: document.getElementById('note-title'),
        noteContent: document.getElementById('note-content'),
        notePreview: document.getElementById('note-preview'),
        selectCategory: document.getElementById('select-category'),
        
        // Actions Buttons
        btnPinToggle: document.getElementById('btn-pin-toggle'),
        btnArchiveToggle: document.getElementById('btn-archive-toggle'),
        btnExportToggle: document.getElementById('btn-export-toggle'),
        exportMenu: document.getElementById('export-menu'),
        btnCopyClipboard: document.getElementById('btn-copy-clipboard'),
        btnDelete: document.getElementById('btn-delete'),
        
        // Color Picker
        btnColorPicker: document.getElementById('btn-color-picker'),
        colorMenu: document.getElementById('color-menu'),
        currentColorDot: document.getElementById('current-color-dot'),
        
        // Tabs
        tabEdit: document.getElementById('tab-edit'),
        tabPreview: document.getElementById('tab-preview'),
        
        // Stats
        wordCounter: document.getElementById('word-counter'),
        charCounter: document.getElementById('char-counter'),
        readingTimeCounter: document.getElementById('reading-time-counter'),
        
        statTotalNotes: document.getElementById('stat-total-notes'),
        statTotalWords: document.getElementById('stat-total-words'),
        statReadTime: document.getElementById('stat-read-time'),
        
        countAll: document.getElementById('count-all'),
        countPinned: document.getElementById('count-pinned'),
        countArchived: document.getElementById('count-archived'),
        countTrashed: document.getElementById('count-trashed'),
        
        // Categories
        btnAddCategory: document.getElementById('btn-add-category'),
        addCategoryForm: document.getElementById('add-category-form'),
        newCategoryName: document.getElementById('new-category-name'),
        btnCancelCategory: document.getElementById('btn-cancel-category'),
        btnSaveCategory: document.getElementById('btn-save-category'),
        categoryColorPicker: document.getElementById('category-color-picker'),
        categoriesContainer: document.getElementById('categories-container'),
        
        // Toasts
        toastContainer: document.getElementById('toast-container')
    };

    // ----------------------------------------------------
    // 2. INITIALIZATION
    // ----------------------------------------------------
    function init() {
        if (!window.AuraData) {
            console.error("AuraData failed to preload from Laravel.");
            return;
        }

        // Hydrate state from injected window object
        state.notes = window.AuraData.notes || [];
        state.archivedNotes = window.AuraData.archivedNotes || [];
        state.trashedNotes = window.AuraData.trashedNotes || [];
        state.categories = window.AuraData.categories || [];
        state.stats = window.AuraData.stats || {};

        setupEventListeners();
        setupThemeSelector();

        // Load the first note if available, else show empty screen
        const firstNote = getFirstAvailableNote();
        if (firstNote) {
            activateNote(firstNote.id);
        } else {
            showEmptyEditorOverlay();
        }

        renderNotesList();
        renderCategories();
        updateStatsWidget();
        updateFolderCounters();
    }

    function getFirstAvailableNote() {
        if (state.currentFolder === 'all' && state.notes.length > 0) {
            return state.notes[0];
        } else if (state.currentFolder === 'archived' && state.archivedNotes.length > 0) {
            return state.archivedNotes[0];
        } else if (state.currentFolder === 'trashed' && state.trashedNotes.length > 0) {
            return state.trashedNotes[0];
        }
        return null;
    }

    // ----------------------------------------------------
    // 3. EVENT LISTENERS SETUP
    // ----------------------------------------------------
    function setupEventListeners() {
        // CSRF Token Setup for Fetch
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        window.csrfHeaders = {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        };

        // Note Creation
        el.btnNewNote.addEventListener('click', () => createNewNote());
        if (el.btnEmptyNewNote) {
            el.btnEmptyNewNote.addEventListener('click', () => createNewNote());
        }

        // Live Editor Key events (Autosave & Stats)
        el.noteTitle.addEventListener('input', () => {
            if (state.activeNoteId) {
                updateNoteLocalState(state.activeNoteId, { title: el.noteTitle.value });
                triggerAutosave(state.activeNoteId);
                // Update live title in list card
                const cardTitle = document.querySelector(`[data-note-id="${state.activeNoteId}"] h4`);
                if (cardTitle) cardTitle.innerText = el.noteTitle.value || 'Catatan Tanpa Judul';
            }
        });

        el.noteContent.addEventListener('input', () => {
            if (state.activeNoteId) {
                updateNoteLocalState(state.activeNoteId, { content: el.noteContent.value });
                triggerAutosave(state.activeNoteId);
                calculateEditorStats();
                
                // Update live content snippet in list card
                const cardSnippet = document.querySelector(`[data-note-id="${state.activeNoteId}"] p`);
                if (cardSnippet) {
                    const text = el.noteContent.value.replace(/[#*`_~]/g, ''); // strip basic md characters
                    cardSnippet.innerText = text.substring(0, 100) || 'Belum ada konten.';
                }
            }
        });

        // Tab Key indent support in Textarea
        el.noteContent.addEventListener('keydown', (e) => {
            if (e.key === 'Tab') {
                e.preventDefault();
                const start = el.noteContent.selectionStart;
                const end = el.noteContent.selectionEnd;
                el.noteContent.value = el.noteContent.value.substring(0, start) + "    " + el.noteContent.value.substring(end);
                el.noteContent.selectionStart = el.noteContent.selectionEnd = start + 4;
                el.noteContent.dispatchEvent(new Event('input'));
            }
        });

        // Search Engine
        el.searchInput.addEventListener('input', (e) => {
            state.searchQuery = e.target.value.toLowerCase().trim();
            renderNotesList();
        });

        // Sidebar Folder Toggles
        document.querySelectorAll('.folder-btn').forEach(button => {
            button.addEventListener('click', () => {
                document.querySelectorAll('.folder-btn').forEach(btn => btn.classList.remove('active-folder', 'bg-[var(--bg-app)]'));
                button.classList.add('active-folder', 'bg-[var(--bg-app)]');
                
                state.currentFolder = button.getAttribute('data-folder');
                state.currentCategoryId = null; // Clear category filter when clicking folders
                
                // Clear category button active highlights
                document.querySelectorAll('.category-btn').forEach(btn => btn.closest('.category-item-wrapper').classList.remove('bg-[var(--bg-app)]'));

                updateListTitleAndDesc();
                renderNotesList();

                // Select first note in this folder
                const note = getFirstAvailableNote();
                if (note) {
                    activateNote(note.id);
                } else {
                    showEmptyEditorOverlay();
                }
            });
        });

        // Toggle Pinned
        el.btnPinToggle.addEventListener('click', () => togglePinActiveNote());

        // Toggle Archived
        el.btnArchiveToggle.addEventListener('click', () => toggleArchiveActiveNote());

        // Soft / Permanent Delete
        el.btnDelete.addEventListener('click', () => deleteActiveNote());

        // Empty Trash Trigger
        el.btnEmptyTrash.addEventListener('click', () => {
            if (confirm("Apakah Anda yakin ingin menghapus semua catatan di Tempat Sampah secara permanen? Tindakan ini tidak dapat dibatalkan.")) {
                emptyTrash();
            }
        });

        // Export Dropdown Trigger
        el.btnExportToggle.addEventListener('click', (e) => {
            e.stopPropagation();
            el.exportMenu.classList.toggle('hidden');
        });

        document.querySelectorAll('[data-export-format]').forEach(item => {
            item.addEventListener('click', () => {
                const format = item.getAttribute('data-export-format');
                exportActiveNote(format);
                el.exportMenu.classList.add('hidden');
            });
        });

        // Copy Note Content
        el.btnCopyClipboard.addEventListener('click', () => {
            if (state.activeNoteId) {
                const text = el.noteContent.value;
                navigator.clipboard.writeText(text).then(() => {
                    showToast("Catatan disalin ke papan klip!");
                });
            }
            el.exportMenu.classList.add('hidden');
        });

        // Color Palette Toggle
        el.btnColorPicker.addEventListener('click', (e) => {
            e.stopPropagation();
            el.colorMenu.classList.toggle('hidden');
        });

        document.querySelectorAll('[data-note-color]').forEach(btn => {
            btn.addEventListener('click', () => {
                const color = btn.getAttribute('data-note-color');
                changeActiveNoteColor(color);
                el.colorMenu.classList.add('hidden');
            });
        });

        // Click outside drops
        document.addEventListener('click', () => {
            el.exportMenu.classList.add('hidden');
            el.colorMenu.classList.add('hidden');
        });

        // Select Category Change
        el.selectCategory.addEventListener('change', () => {
            const catId = el.selectCategory.value ? parseInt(el.selectCategory.value) : null;
            changeActiveNoteCategory(catId);
        });

        // Editor Tabs (Edit / Preview Toggle)
        el.tabEdit.addEventListener('click', () => setEditorMode(false));
        el.tabPreview.addEventListener('click', () => setEditorMode(true));

        // Category Inline Adding
        el.btnAddCategory.addEventListener('click', () => {
            el.addCategoryForm.classList.toggle('hidden');
            if (!el.addCategoryForm.classList.contains('hidden')) {
                el.newCategoryName.focus();
            }
        });

        el.btnCancelCategory.addEventListener('click', () => {
            el.addCategoryForm.classList.add('hidden');
            el.newCategoryName.value = '';
        });

        el.btnSaveCategory.addEventListener('click', () => createNewCategory());

        // Category Color Dot Pickers
        el.categoryColorPicker.querySelectorAll('button').forEach(btn => {
            btn.addEventListener('click', () => {
                el.categoryColorPicker.querySelectorAll('button').forEach(b => b.classList.remove('scale-110', 'border-2', 'border-white', 'dark:border-slate-800'));
                btn.classList.add('scale-110', 'border-2', 'border-white', 'dark:border-slate-800');
                state.activeCategoryColor = btn.getAttribute('data-color');
            });
        });

        // Keyboard Shortcuts Handler
        document.addEventListener('keydown', (e) => {
            // Check if editing
            if (!state.activeNoteId) return;

            // Ctrl + S (Force save)
            if (e.ctrlKey && e.key === 's') {
                e.preventDefault();
                saveNoteToServer(state.activeNoteId);
            }
            // Ctrl + N (New Note)
            if (e.ctrlKey && e.key === 'n') {
                e.preventDefault();
                createNewNote();
            }
            // Ctrl + P (Pin Note)
            if (e.ctrlKey && e.key === 'p') {
                e.preventDefault();
                togglePinActiveNote();
            }
            // Ctrl + A (Archive Note)
            if (e.ctrlKey && e.key === 'a') {
                e.preventDefault();
                toggleArchiveActiveNote();
            }
            // Ctrl + E (Export MD)
            if (e.ctrlKey && e.key === 'e') {
                e.preventDefault();
                exportActiveNote('md');
            }
            // Ctrl + Alt + P (Toggle Preview)
            if (e.ctrlKey && e.altKey && e.key === 'p') {
                e.preventDefault();
                setEditorMode(!state.previewMode);
            }
        });
    }

    // ----------------------------------------------------
    // 4. NOTES LIST RENDERING
    // ----------------------------------------------------
    function renderNotesList() {
        el.notesListContainer.innerHTML = '';
        
        let filteredNotes = [];

        // 1. Filter by Folder
        if (state.currentFolder === 'all') {
            filteredNotes = [...state.notes];
        } else if (state.currentFolder === 'pinned') {
            filteredNotes = state.notes.filter(n => n.is_pinned);
        } else if (state.currentFolder === 'archived') {
            filteredNotes = [...state.archivedNotes];
        } else if (state.currentFolder === 'trashed') {
            filteredNotes = [...state.trashedNotes];
        }

        // 2. Filter by Category
        if (state.currentCategoryId !== null) {
            filteredNotes = filteredNotes.filter(n => n.category_id === state.currentCategoryId);
        }

        // 3. Filter by Search Query
        if (state.searchQuery) {
            filteredNotes = filteredNotes.filter(n => 
                (n.title && n.title.toLowerCase().includes(state.searchQuery)) || 
                (n.content && n.content.toLowerCase().includes(state.searchQuery))
            );
        }

        if (filteredNotes.length === 0) {
            renderEmptyListState();
            return;
        }

        filteredNotes.forEach(note => {
            const card = document.createElement('div');
            card.setAttribute('data-note-id', note.id);
            
            // Build pastel class name
            const colorClass = `note-color-${note.color || 'default'}`;
            const activeClass = state.activeNoteId === note.id ? 'active-note' : '';
            
            card.className = `note-list-card p-4 rounded-xl border flex flex-col gap-2.5 cursor-pointer transition-all ${colorClass} ${activeClass}`;
            
            // Format content snippet (strip html, slice, default placeholder)
            const plainText = note.content ? note.content.replace(/[#*`_~]/g, '') : '';
            const snippet = plainText ? (plainText.substring(0, 70) + (plainText.length > 70 ? '...' : '')) : 'Belum ada konten.';
            
            // Format updated time
            const dateStr = formatHumanFriendlyDate(new Date(note.updated_at));

            // Icons panel
            let iconsHtml = '';
            if (note.is_pinned) {
                iconsHtml += `<span class="text-amber-500" title="Disematkan"><svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 17v5M5 17h14v-1.76a2 2 0 0 0-.44-1.24l-2.78-3.5a2 2 0 0 1-.78-1.24V3.5a1.5 1.5 0 0 0-3 0v5.76a2 2 0 0 1-.78 1.24l-2.78 3.5a2 2 0 0 0-.44 1.24H5Z"/></svg></span>`;
            }
            if (note.is_archived) {
                iconsHtml += `<span class="text-emerald-500" title="Diarsipkan"><svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="20" height="5" x="2" y="3" rx="1"/><path d="M4 8v11a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8"/><path d="M10 12h4"/></svg></span>`;
            }

            // Category badge
            let categoryBadgeHtml = '';
            if (note.category) {
                categoryBadgeHtml = `
                    <span class="flex items-center gap-1 font-semibold text-[var(--text-main)] bg-[var(--bg-sidebar)]/80 px-2 py-0.5 rounded-full border border-[var(--border-color)]/60 max-w-[150px] truncate shrink-0">
                        <span>${note.category.icon || '📁'}</span>
                        <span class="truncate">${note.category.name}</span>
                    </span>
                `;
            }

            card.innerHTML = `
                <div class="flex items-start justify-between gap-2">
                    <h4 class="font-bold text-sm text-[var(--text-main)] line-clamp-1 flex-1">${note.title || 'Catatan Tanpa Judul'}</h4>
                    <div class="flex items-center gap-1 shrink-0">
                        ${iconsHtml}
                    </div>
                </div>
                <p class="text-xs text-[var(--text-muted)] line-clamp-2 leading-relaxed h-8">
                    ${snippet}
                </p>
                <div class="flex items-center justify-between text-[10px] text-[var(--text-muted)] border-t border-[var(--border-color)]/40 pt-2">
                    <span>${dateStr}</span>
                    ${categoryBadgeHtml}
                </div>
            `;

            // Card click active handler
            card.addEventListener('click', () => {
                activateNote(note.id);
            });

            el.notesListContainer.appendChild(card);
        });
    }

    function renderEmptyListState() {
        const div = document.createElement('div');
        div.className = 'h-full flex flex-col items-center justify-center text-center p-6 space-y-3';
        div.innerHTML = `
            <div class="w-14 h-14 rounded-full bg-[var(--border-color)]/30 flex items-center justify-center text-[var(--text-muted)]">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/></svg>
            </div>
            <div>
                <h4 class="font-bold text-sm text-[var(--text-main)]">Kosong</h4>
                <p class="text-xs text-[var(--text-muted)] mt-1">Tidak ada catatan yang cocok dengan penyaringan ini.</p>
            </div>
        `;
        el.notesListContainer.appendChild(div);
    }

    // ----------------------------------------------------
    // 5. ACTIVE NOTE MANAGEMENT
    // ----------------------------------------------------
    function activateNote(noteId) {
        // Save current note first if there's an active one with pending changes
        if (state.activeNoteId && state.activeNoteId !== noteId) {
            saveNoteToServer(state.activeNoteId);
        }

        state.activeNoteId = noteId;

        // Highlight card in list
        document.querySelectorAll('.note-list-card').forEach(card => {
            card.classList.remove('active-note');
            if (parseInt(card.getAttribute('data-note-id')) === noteId) {
                card.classList.add('active-note');
            }
        });

        // Find the active note details in state
        const note = findNoteInState(noteId);
        if (!note) {
            showEmptyEditorOverlay();
            return;
        }

        // Show editor panel, hide overlay
        el.activeNoteEditor.classList.remove('hidden');
        el.emptyEditorOverlay.classList.add('hidden');

        // Populate fields
        el.noteTitle.value = note.title || '';
        el.noteContent.value = note.content || '';
        el.selectCategory.value = note.category_id || '';
        
        // Handle Trashed state: disable inputs if trashed
        if (note.is_trashed) {
            el.noteTitle.disabled = true;
            el.noteContent.disabled = true;
            el.selectCategory.disabled = true;
            el.btnPinToggle.disabled = true;
            el.btnArchiveToggle.disabled = true;
            el.btnColorPicker.disabled = true;
            el.btnDelete.setAttribute('title', 'Hapus Permanen');
        } else {
            el.noteTitle.disabled = false;
            el.noteContent.disabled = false;
            el.selectCategory.disabled = false;
            el.btnPinToggle.disabled = false;
            el.btnArchiveToggle.disabled = false;
            el.btnColorPicker.disabled = false;
            el.btnDelete.setAttribute('title', 'Hapus Catatan');
        }

        // Pinned state color
        if (note.is_pinned) {
            el.btnPinToggle.classList.add('text-amber-500', 'bg-[var(--bg-app)]');
        } else {
            el.btnPinToggle.classList.remove('text-amber-500', 'bg-[var(--bg-app)]');
        }

        // Archived state color
        if (note.is_archived) {
            el.btnArchiveToggle.classList.add('text-emerald-500', 'bg-[var(--bg-app)]');
        } else {
            el.btnArchiveToggle.classList.remove('text-emerald-500', 'bg-[var(--bg-app)]');
        }

        // Update background layout color frame
        updateEditorFrameColor(note.color);

        // Word counts
        calculateEditorStats();

        // If in preview mode, render markdown
        if (state.previewMode) {
            renderMarkdownPreview();
        }

        // Reset autosave status indicator to Saved
        updateSaveIndicator('saved');
    }

    function findNoteInState(noteId) {
        let note = state.notes.find(n => n.id === noteId);
        if (!note) note = state.archivedNotes.find(n => n.id === noteId);
        if (!note) note = state.trashedNotes.find(n => n.id === noteId);
        return note;
    }

    function updateNoteLocalState(noteId, fields) {
        const updateArray = (arr) => {
            const index = arr.findIndex(n => n.id === noteId);
            if (index !== -1) {
                arr[index] = { ...arr[index], ...fields, updated_at: new Date().toISOString() };
                return true;
            }
            return false;
        };

        if (updateArray(state.notes)) return;
        if (updateArray(state.archivedNotes)) return;
        updateArray(state.trashedNotes);
    }

    // ----------------------------------------------------
    // 6. AUTOSAVE ENGINE (DEBOUNCED)
    // ----------------------------------------------------
    function triggerAutosave(noteId) {
        // If trashed, do not autosave edits
        const note = findNoteInState(noteId);
        if (note && note.is_trashed) return;

        updateSaveIndicator('saving');

        clearTimeout(state.autosaveTimer);
        state.autosaveTimer = setTimeout(() => {
            saveNoteToServer(noteId);
        }, 1000); // 1-second debounce
    }

    function saveNoteToServer(noteId) {
        const note = findNoteInState(noteId);
        if (!note || note.is_trashed) return;

        const payload = {
            title: note.title,
            content: note.content,
            color: note.color,
            is_pinned: note.is_pinned,
            is_archived: note.is_archived,
            category_id: note.category_id
        };

        fetch(`/notes/${noteId}`, {
            method: 'PUT',
            headers: window.csrfHeaders,
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                updateSaveIndicator('saved');
                
                // Sync exact state updated_at from server
                updateNoteLocalState(noteId, { updated_at: data.note.updated_at });
                
                // Update stats
                state.stats = data.stats;
                updateStatsWidget();
            } else {
                updateSaveIndicator('error');
            }
        })
        .catch(err => {
            console.error("Autosave failed:", err);
            updateSaveIndicator('error');
        });
    }

    function updateSaveIndicator(status) {
        if (status === 'saving') {
            el.saveIndicatorDot.className = 'w-2.5 h-2.5 rounded-full bg-amber-500 relative flex shrink-0';
            el.saveIndicatorDot.innerHTML = '<span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>';
            el.saveIndicatorText.innerText = 'Menyimpan...';
        } else if (status === 'saved') {
            el.saveIndicatorDot.className = 'w-2.5 h-2.5 rounded-full bg-emerald-500 relative flex shrink-0';
            el.saveIndicatorDot.innerHTML = '';
            el.saveIndicatorText.innerText = 'Tersimpan';
        } else if (status === 'error') {
            el.saveIndicatorDot.className = 'w-2.5 h-2.5 rounded-full bg-rose-500 relative flex shrink-0';
            el.saveIndicatorDot.innerHTML = '';
            el.saveIndicatorText.innerText = 'Gagal menyimpan';
        }
    }

    // ----------------------------------------------------
    // 7. NOTE CRUD ACTIONS
    // ----------------------------------------------------
    function createNewNote() {
        const payload = {};
        // If filtering by a specific category, auto-assign the category to the new note!
        if (state.currentCategoryId !== null) {
            payload.category_id = state.currentCategoryId;
        }

        fetch('/notes', {
            method: 'POST',
            headers: window.csrfHeaders,
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // If creating in Trash/Archive view, toggle back to All Notes folder
                if (state.currentFolder === 'trashed' || state.currentFolder === 'archived') {
                    state.currentFolder = 'all';
                    state.currentCategoryId = null;
                    document.querySelectorAll('.folder-btn').forEach(btn => btn.classList.remove('active-folder', 'bg-[var(--bg-app)]'));
                    const allBtn = document.querySelector('[data-folder="all"]');
                    if (allBtn) allBtn.classList.add('active-folder', 'bg-[var(--bg-app)]');
                    updateListTitleAndDesc();
                }

                // Add to state and activate
                state.notes.unshift(data.note);
                activateNote(data.note.id);
                renderNotesList();
                
                // Update stats
                state.stats.total_notes += 1;
                updateStatsWidget();
                updateFolderCounters();

                showToast("Catatan baru berhasil dibuat!");
                
                // Focus editor
                el.noteTitle.focus();
            }
        })
        .catch(err => {
            console.error("Create note failed:", err);
            showToast("Gagal membuat catatan baru.", "error");
        });
    }

    function deleteActiveNote() {
        if (!state.activeNoteId) return;

        const noteId = state.activeNoteId;
        const note = findNoteInState(noteId);

        fetch(`/notes/${noteId}`, {
            method: 'DELETE',
            headers: window.csrfHeaders
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // Remove from local arrays
                state.notes = state.notes.filter(n => n.id !== noteId);
                state.archivedNotes = state.archivedNotes.filter(n => n.id !== noteId);

                if (note.is_trashed) {
                    // Permanently deleted from trash array
                    state.trashedNotes = state.trashedNotes.filter(n => n.id !== noteId);
                    showToast("Catatan dihapus secara permanen.");
                } else {
                    // Soft deleted to trash array
                    note.is_trashed = true;
                    note.is_pinned = false;
                    state.trashedNotes.unshift(note);
                    showToast("Catatan dipindahkan ke Tempat Sampah.");
                }

                // Select next note
                state.activeNoteId = null;
                const nextNote = getFirstAvailableNote();
                if (nextNote) {
                    activateNote(nextNote.id);
                } else {
                    showEmptyEditorOverlay();
                }

                state.stats = data.stats;
                updateStatsWidget();
                updateFolderCounters();
                renderNotesList();
                renderCategories();
            }
        })
        .catch(err => {
            console.error("Delete note failed:", err);
            showToast("Gagal menghapus catatan.", "error");
        });
    }

    function togglePinActiveNote() {
        if (!state.activeNoteId) return;

        const noteId = state.activeNoteId;
        const note = findNoteInState(noteId);
        if (note.is_trashed) return;

        const newPinnedState = !note.is_pinned;
        updateNoteLocalState(noteId, { is_pinned: newPinnedState });

        // Update Pin Button layout state
        if (newPinnedState) {
            el.btnPinToggle.classList.add('text-amber-500', 'bg-[var(--bg-app)]');
            showToast("Catatan disematkan ke atas.");
        } else {
            el.btnPinToggle.classList.remove('text-amber-500', 'bg-[var(--bg-app)]');
            showToast("Sematan catatan dilepas.");
        }

        // Float to top: Re-sort active notes array locally
        state.notes.sort((a, b) => {
            if (a.is_pinned && !b.is_pinned) return -1;
            if (!a.is_pinned && b.is_pinned) return 1;
            return new Date(b.updated_at) - new Date(a.updated_at);
        });

        saveNoteToServer(noteId);
        renderNotesList();
        updateFolderCounters();
    }

    function toggleArchiveActiveNote() {
        if (!state.activeNoteId) return;

        const noteId = state.activeNoteId;
        const note = findNoteInState(noteId);
        if (note.is_trashed) return;

        const newArchiveState = !note.is_archived;
        
        // Remove from current list, move to another
        if (newArchiveState) {
            note.is_archived = true;
            note.is_pinned = false; // Unpin archived note
            state.notes = state.notes.filter(n => n.id !== noteId);
            state.archivedNotes.unshift(note);
            showToast("Catatan berhasil diarsipkan.");
        } else {
            note.is_archived = false;
            state.archivedNotes = state.archivedNotes.filter(n => n.id !== noteId);
            state.notes.unshift(note);
            showToast("Catatan dikeluarkan dari arsip.");
        }

        // Sync with server
        saveNoteToServer(noteId);
        
        // Select next note
        state.activeNoteId = null;
        const nextNote = getFirstAvailableNote();
        if (nextNote) {
            activateNote(nextNote.id);
        } else {
            showEmptyEditorOverlay();
        }

        // Re-sort notes list
        state.notes.sort((a, b) => {
            if (a.is_pinned && !b.is_pinned) return -1;
            if (!a.is_pinned && b.is_pinned) return 1;
            return new Date(b.updated_at) - new Date(a.updated_at);
        });

        renderNotesList();
        renderCategories();
        updateFolderCounters();
    }

    function emptyTrash() {
        fetch('/notes/trash/empty', {
            method: 'POST',
            headers: window.csrfHeaders
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                state.trashedNotes = [];
                state.stats = data.stats;
                
                showToast("Tempat sampah berhasil dikosongkan.");

                if (state.currentFolder === 'trashed') {
                    showEmptyEditorOverlay();
                    renderNotesList();
                }

                updateStatsWidget();
                updateFolderCounters();
            }
        })
        .catch(err => {
            console.error("Empty trash failed:", err);
            showToast("Gagal mengosongkan tempat sampah.", "error");
        });
    }

    function exportActiveNote(format) {
        if (!state.activeNoteId) return;
        
        // Use browser direct navigation to trigger stream file download headers
        window.location.href = `/notes/${state.activeNoteId}/export/${format}`;
    }

    function changeActiveNoteColor(color) {
        if (!state.activeNoteId) return;

        updateNoteLocalState(state.activeNoteId, { color: color });
        updateEditorFrameColor(color);
        saveNoteToServer(state.activeNoteId);
        renderNotesList();
    }

    function changeActiveNoteCategory(categoryId) {
        if (!state.activeNoteId) return;

        // Find selected category representation in state
        const cat = state.categories.find(c => c.id === categoryId) || null;
        updateNoteLocalState(state.activeNoteId, { category_id: categoryId, category: cat });
        
        saveNoteToServer(state.activeNoteId);
        renderNotesList();
        renderCategories();
    }

    // ----------------------------------------------------
    // 8. CATEGORY MANAGEMENT
    // ----------------------------------------------------
    function createNewCategory() {
        const name = el.newCategoryName.value.trim();
        if (!name) {
            showToast("Nama kategori tidak boleh kosong.", "error");
            return;
        }

        const payload = {
            name: name,
            color: state.activeCategoryColor,
            icon: '' // Will trigger random emoji in PHP
        };

        fetch('/categories', {
            method: 'POST',
            headers: window.csrfHeaders,
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                state.categories.push(data.category);
                
                // Add option to note category select dropdown
                const opt = document.createElement('option');
                opt.value = data.category.id;
                opt.innerText = `${data.category.icon} ${data.category.name}`;
                el.selectCategory.appendChild(opt);

                renderCategories();
                
                // Reset form
                el.addCategoryForm.classList.add('hidden');
                el.newCategoryName.value = '';
                showToast("Kategori baru berhasil ditambahkan!");
            } else {
                showToast("Kategori gagal ditambahkan.", "error");
            }
        })
        .catch(err => {
            console.error("Create category failed:", err);
            showToast("Nama kategori sudah terpakai.", "error");
        });
    }

    function renderCategories() {
        el.categoriesContainer.innerHTML = '';

        state.categories.forEach(cat => {
            // Count notes of this category locally
            const count = state.notes.filter(n => n.category_id === cat.id).length;

            const item = document.createElement('div');
            item.className = 'category-item-wrapper group flex items-center justify-between rounded-lg hover:bg-[var(--bg-app)]/50 pr-2';
            
            if (state.currentCategoryId === cat.id) {
                item.classList.add('bg-[var(--bg-app)]');
            }

            item.innerHTML = `
                <button data-category-id="${cat.id}" class="flex-1 flex items-center gap-2.5 px-3 py-2 text-sm font-medium text-[var(--text-main)] cursor-pointer text-left category-btn">
                    <span class="text-base">${cat.icon || '📁'}</span>
                    <span class="truncate">${cat.name}</span>
                    <span class="w-2 h-2 rounded-full note-dot-${cat.color}"></span>
                </button>
                <span class="text-xs px-2 py-0.5 rounded-full bg-[var(--border-color)] text-[var(--text-muted)] group-hover:hidden">${count}</span>
                <button data-action="delete-category" data-id="${cat.id}" class="hidden group-hover:flex p-1 text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/20 rounded cursor-pointer transition-colors" title="Hapus Kategori">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                </button>
            `;

            // Category filter click event
            item.querySelector('.category-btn').addEventListener('click', () => {
                // Clear active folder highlights
                document.querySelectorAll('.folder-btn').forEach(btn => btn.classList.remove('active-folder', 'bg-[var(--bg-app)]'));
                document.querySelectorAll('.category-item-wrapper').forEach(btn => btn.classList.remove('bg-[var(--bg-app)]'));
                
                item.classList.add('bg-[var(--bg-app)]');

                state.currentFolder = 'all'; // filter active notes
                state.currentCategoryId = cat.id;

                el.currentFolderTitle.innerText = `${cat.icon} ${cat.name}`;
                el.currentFolderDesc.innerText = `Menampilkan catatan dalam kategori ${cat.name}`;
                
                renderNotesList();

                // Select first note matching category
                const note = getFirstAvailableNote();
                if (note) {
                    activateNote(note.id);
                } else {
                    showEmptyEditorOverlay();
                }
            });

            // Delete category click
            item.querySelector('[data-action="delete-category"]').addEventListener('click', (e) => {
                e.stopPropagation();
                if (confirm(`Apakah Anda yakin ingin menghapus kategori "${cat.name}"? Catatan di dalamnya tidak akan terhapus, hanya kategorinya yang dikosongkan.`)) {
                    deleteCategory(cat.id);
                }
            });

            // Color helper stylesheet
            const style = document.createElement('style');
            style.innerHTML = `
                .note-dot-blue { background-color: #3b82f6; }
                .note-dot-amber { background-color: #f59e0b; }
                .note-dot-emerald { background-color: #10b981; }
                .note-dot-rose { background-color: #f43f5e; }
                .note-dot-purple { background-color: #a855f7; }
                .note-dot-indigo { background-color: #6366f1; }
                .note-dot-slate { background-color: #64748b; }
            `;
            document.head.appendChild(style);

            el.categoriesContainer.appendChild(item);
        });
    }

    function deleteCategory(catId) {
        fetch(`/categories/${catId}`, {
            method: 'DELETE',
            headers: window.csrfHeaders
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                state.categories = state.categories.filter(c => c.id !== catId);
                
                // Nullify categories of notes locally
                state.notes.forEach(n => {
                    if (n.category_id === catId) {
                        n.category_id = null;
                        n.category = null;
                    }
                });

                // Clear dropdown select option
                const opt = el.selectCategory.querySelector(`option[value="${catId}"]`);
                if (opt) opt.remove();

                if (state.currentCategoryId === catId) {
                    // reset filters
                    state.currentCategoryId = null;
                    state.currentFolder = 'all';
                    const allBtn = document.querySelector('[data-folder="all"]');
                    if (allBtn) allBtn.classList.add('active-folder', 'bg-[var(--bg-app)]');
                    updateListTitleAndDesc();
                }

                renderCategories();
                renderNotesList();
                
                // If active note was in deleted category, re-trigger activation to refresh selects
                if (state.activeNoteId) {
                    activateNote(state.activeNoteId);
                }

                showToast("Kategori berhasil dihapus.");
            }
        })
        .catch(err => {
            console.error("Delete category failed:", err);
            showToast("Gagal menghapus kategori.", "error");
        });
    }

    // ----------------------------------------------------
    // 9. THEMES & ESTHETIC POLISHING
    // ----------------------------------------------------
    function setupThemeSelector() {
        document.querySelectorAll('[data-theme-set]').forEach(btn => {
            btn.addEventListener('click', () => {
                const theme = btn.getAttribute('data-theme-set');
                
                document.documentElement.setAttribute('data-theme', theme);
                localStorage.setItem('aurapad-theme', theme);
                
                updateThemePickerActive(theme);
                showToast(`Tema beralih ke ${theme.toUpperCase()}`);
            });
        });

        // Initialize theme buttons highlights
        const activeTheme = localStorage.getItem('aurapad-theme') || 'light';
        updateThemePickerActive(activeTheme);
    }

    function updateThemePickerActive(themeName) {
        document.querySelectorAll('[data-theme-set]').forEach(btn => {
            btn.classList.remove('bg-[var(--bg-app)]', 'text-[var(--accent-color)]');
            if (btn.getAttribute('data-theme-set') === themeName) {
                btn.classList.add('bg-[var(--bg-app)]', 'text-[var(--accent-color)]');
            }
        });
    }

    function updateEditorFrameColor(colorName) {
        // Remove all note color styling classes
        el.editorFrame.className = 'flex-1 flex flex-col p-8 overflow-hidden transition-all duration-300';
        el.editorFrame.classList.add(`note-color-${colorName || 'default'}`);

        // Update dot in palette button
        el.currentColorDot.className = 'w-3.5 h-3.5 rounded-full border border-gray-300 flex inline-block shrink-0';
        if (colorName === 'default') {
            el.currentColorDot.classList.add('bg-[var(--bg-card)]');
        } else if (colorName === 'blue') {
            el.currentColorDot.classList.add('bg-blue-300');
        } else if (colorName === 'emerald') {
            el.currentColorDot.classList.add('bg-emerald-300');
        } else if (colorName === 'amber') {
            el.currentColorDot.classList.add('bg-amber-300');
        } else if (colorName === 'purple') {
            el.currentColorDot.classList.add('bg-purple-300');
        } else if (colorName === 'rose') {
            el.currentColorDot.classList.add('bg-rose-300');
        }
    }

    function setEditorMode(isPreview) {
        state.previewMode = isPreview;

        if (isPreview) {
            // Preview mode activated
            el.tabPreview.classList.replace('text-[var(--text-muted)]', 'text-[var(--accent-color)]');
            el.tabPreview.classList.add('bg-[var(--bg-app)]');
            el.tabEdit.classList.replace('text-[var(--accent-color)]', 'text-[var(--text-muted)]');
            el.tabEdit.classList.remove('bg-[var(--bg-app)]');

            el.noteContent.classList.add('hidden');
            el.notePreview.classList.remove('hidden');

            renderMarkdownPreview();
        } else {
            // Edit mode activated
            el.tabEdit.classList.replace('text-[var(--text-muted)]', 'text-[var(--accent-color)]');
            el.tabEdit.classList.add('bg-[var(--bg-app)]');
            el.tabPreview.classList.replace('text-[var(--accent-color)]', 'text-[var(--text-muted)]');
            el.tabPreview.classList.remove('bg-[var(--bg-app)]');

            el.notePreview.classList.add('hidden');
            el.noteContent.classList.remove('hidden');
            el.noteContent.focus();
        }
    }

    function renderMarkdownPreview() {
        if (!window.marked) {
            el.notePreview.innerHTML = "<p class='text-rose-500'>Error: Pembuat pratinjau markdown tidak termuat.</p>";
            return;
        }

        const rawContent = el.noteContent.value || '_Belum ada konten._';
        el.notePreview.innerHTML = marked.parse(rawContent);
    }

    // ----------------------------------------------------
    // 10. STATISTICS & UTILITIES
    // ----------------------------------------------------
    function calculateEditorStats() {
        const text = el.noteContent.value || '';
        const charCount = text.length;
        const wordCount = text.trim() ? text.trim().split(/\s+/).length : 0;
        const readingTime = Math.ceil(wordCount / 200) || 0;

        el.wordCounter.innerText = wordCount;
        el.charCounter.innerText = charCount;
        el.readingTimeCounter.innerText = readingTime;
    }

    function updateStatsWidget() {
        if (!state.stats) return;

        el.statTotalNotes.innerText = state.stats.total_notes || 0;
        el.statTotalWords.innerText = formatNumber(state.stats.total_words || 0);
        el.statReadTime.innerText = `${state.stats.total_reading_time || 0}m`;
    }

    function updateFolderCounters() {
        el.countAll.innerText = state.notes.length;
        el.countPinned.innerText = state.notes.filter(n => n.is_pinned).length;
        el.countArchived.innerText = state.archivedNotes.length;
        el.countTrashed.innerText = state.trashedNotes.length;
    }

    function updateListTitleAndDesc() {
        if (state.currentFolder === 'all') {
            el.currentFolderTitle.innerText = "Semua Catatan";
            el.currentFolderDesc.innerText = "Menampilkan catatan aktif Anda";
            el.btnEmptyTrash.classList.add('hidden');
        } else if (state.currentFolder === 'pinned') {
            el.currentFolderTitle.innerText = "Catatan Penting";
            el.currentFolderDesc.innerText = "Catatan yang Anda sematkan di atas";
            el.btnEmptyTrash.classList.add('hidden');
        } else if (state.currentFolder === 'archived') {
            el.currentFolderTitle.innerText = "Arsip Catatan";
            el.currentFolderDesc.innerText = "Catatan yang sudah dirapikan";
            el.btnEmptyTrash.classList.add('hidden');
        } else if (state.currentFolder === 'trashed') {
            el.currentFolderTitle.innerText = "Tempat Sampah";
            el.currentFolderDesc.innerText = "Catatan yang dihapus sementara";
            el.btnEmptyTrash.classList.remove('hidden');
        }
    }

    function showEmptyEditorOverlay() {
        el.activeNoteEditor.classList.add('hidden');
        el.emptyEditorOverlay.classList.remove('hidden');
    }

    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        
        let bgClass = 'bg-slate-900 border-slate-800 text-white';
        let icon = '✨';
        if (type === 'error') {
            bgClass = 'bg-rose-600 border-rose-500 text-white';
            icon = '❌';
        } else if (type === 'warning') {
            bgClass = 'bg-amber-600 border-amber-500 text-white';
            icon = '⚠️';
        }

        toast.className = `flex items-center gap-2.5 px-4 py-3 rounded-xl border text-xs font-semibold shadow-2xl transition-all duration-300 transform translate-y-4 opacity-0 ${bgClass}`;
        toast.innerHTML = `<span>${icon}</span> <span>${message}</span>`;
        
        el.toastContainer.appendChild(toast);
        
        // Anim slide-up in
        setTimeout(() => {
            toast.classList.remove('translate-y-4', 'opacity-0');
        }, 10);

        // Slide-out and remove
        setTimeout(() => {
            toast.classList.add('translate-y-4', 'opacity-0');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    // Helper formatting
    function formatHumanFriendlyDate(date) {
        const now = new Date();
        const diffMs = now - date;
        const diffMins = Math.floor(diffMs / 60000);
        const diffHrs = Math.floor(diffMs / 3600000);
        
        if (diffMins < 1) return 'Baru saja';
        if (diffMins < 60) return `${diffMins} menit lalu`;
        if (diffHrs < 24) {
            // Check if same calendar day
            if (now.getDate() === date.getDate()) {
                return `Hari ini, ${padZero(date.getHours())}:${padZero(date.getMinutes())}`;
            } else {
                return `Kemarin, ${padZero(date.getHours())}:${padZero(date.getMinutes())}`;
            }
        }
        
        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        return `${date.getDate()} ${months[date.getMonth()]} ${date.getFullYear()}`;
    }

    function padZero(num) {
        return num < 10 ? '0' + num : num;
    }

    function formatNumber(num) {
        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }

    // Fire up state engine!
    init();
});
