<div>
    @if(session()->has('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
            {{ session('success') }}
        </div>
    @endif

    @if(session()->has('error'))
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
            {{ session('error') }}
        </div>
    @endif

    <!-- Add New Category -->
    <div class="mb-6 p-4 bg-gray-50 rounded-lg">
        <h4 class="font-semibold mb-3">Add New Category</h4>
        <div class="flex gap-2">
            <input 
                type="text" 
                wire:model="newCategoryName" 
                placeholder="Category name (e.g., History, Science)" 
                class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                wire:keydown.enter="addCategory"
            >
            <button 
                wire:click="addCategory" 
                class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
            >
                Add Category
            </button>
        </div>
        @error('newCategoryName')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
        <p class="mt-2 text-sm text-gray-600">
            Categories: {{ $categories->count() }} / {{ $game->max_categories }}
        </p>
    </div>

    <!-- Categories List -->
    <div class="space-y-4">
        @forelse($categories as $category)
            <div class="border rounded-lg p-4 bg-white shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    @if($editingCategoryId === $category->id)
                        <div class="flex-1 flex gap-2">
                            <input 
                                type="text" 
                                wire:model="editingCategoryName" 
                                class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                wire:keydown.enter="updateCategory"
                            >
                            <button 
                                wire:click="updateCategory" 
                                class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded text-sm"
                            >
                                Save
                            </button>
                            <button 
                                wire:click="cancelEdit" 
                                class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded text-sm"
                            >
                                Cancel
                            </button>
                        </div>
                    @else
                        <h4 class="text-lg font-semibold">{{ $category->name }}</h4>
                        <div class="flex gap-2">
                            <button 
                                wire:click="moveUp({{ $category->id }})" 
                                class="text-gray-600 hover:text-gray-800"
                                title="Move Up"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                </svg>
                            </button>
                            <button 
                                wire:click="moveDown({{ $category->id }})" 
                                class="text-gray-600 hover:text-gray-800"
                                title="Move Down"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <button 
                                wire:click="startEdit({{ $category->id }})" 
                                class="text-blue-600 hover:text-blue-800"
                                title="Edit"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </button>
                            <button 
                                wire:click="deleteCategory({{ $category->id }})" 
                                onclick="return confirm('Are you sure you want to delete this category and all its questions?')"
                                class="text-red-600 hover:text-red-800"
                                title="Delete"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>
                    @endif
                </div>

                <!-- Questions for this category -->
                <div class="mt-4">
                    @livewire('question-manager', ['category' => $category, 'game' => $game], key('question-manager-'.$category->id))
                </div>
            </div>
        @empty
            <div class="text-center text-gray-500 py-8">
                <p>No categories yet. Add your first category above!</p>
            </div>
        @endforelse
    </div>
</div>