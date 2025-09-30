<div class="bg-gradient-to-b from-blue-900 to-blue-800 p-6 rounded-lg">
    @if($game->categories->isEmpty())
        <div class="text-center text-white py-8">
            <p class="text-lg">No categories yet. Add categories below to build your game board.</p>
        </div>
    @else
        <div class="grid gap-2" style="grid-template-columns: repeat({{ $game->categories->count() }}, minmax(0, 1fr));">
            @foreach($game->categories as $category)
                <div class="bg-blue-700 rounded">
                    <!-- Category Header -->
                    <div class="bg-blue-600 text-white font-bold text-center py-3 px-2 rounded-t border-b-2 border-yellow-400">
                        <h3 class="text-sm md:text-base uppercase tracking-wide">{{ $category->name }}</h3>
                    </div>
                    
                    <!-- Questions -->
                    <div class="space-y-2 p-2">
                        @forelse($category->questions as $question)
                            <div class="bg-blue-800 text-yellow-400 font-bold text-center py-4 rounded cursor-pointer hover:bg-blue-700 transition-colors border-2 border-yellow-400">
                                <span class="text-2xl">${{ $question->points }}</span>
                            </div>
                        @empty
                            <div class="bg-gray-700 text-gray-400 text-center py-4 rounded">
                                <span class="text-sm">No questions</span>
                            </div>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>