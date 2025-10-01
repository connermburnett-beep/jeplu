<div class="min-h-screen bg-gradient-to-br from-blue-900 via-blue-800 to-blue-900 p-8" 
     x-data="{ 
         showCorrect: false, 
         showWrong: false,
         playSound(type) {
             if (type === 'correct') {
                 this.showCorrect = true;
                 setTimeout(() => this.showCorrect = false, 2000);
             } else if (type === 'wrong') {
                 this.showWrong = true;
                 setTimeout(() => this.showWrong = false, 2000);
             }
         }
     }"
     @score-updated.window="playSound($event.detail.isCorrect ? 'correct' : 'wrong')">
    
    <!-- Correct Answer Flash -->
    <div x-show="showCorrect" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-green-500 bg-opacity-30 pointer-events-none z-50 flex items-center justify-center"
         style="display: none;">
        <div class="text-9xl font-bold text-white animate-bounce">✓</div>
    </div>
    
    <!-- Wrong Answer Flash -->
    <div x-show="showWrong" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-red-500 bg-opacity-30 pointer-events-none z-50 flex items-center justify-center"
         style="display: none;">
        <div class="text-9xl font-bold text-white animate-bounce">✗</div>
    </div>
    
    @if(!$currentQuestion)
        <!-- Game Board View -->
        <div class="max-w-7xl mx-auto">
            <!-- Header with Game Title -->
            <div class="text-center mb-8">
                <h1 class="text-6xl font-bold text-white mb-4" style="font-family: 'Fredoka One', cursive;">
                    {{ $game->name }}
                </h1>
            </div>
            
            <!-- Game Board -->
            <div class="bg-blue-950 rounded-2xl shadow-2xl p-8 mb-8">
                <div class="overflow-x-auto">
                    <div class="inline-block min-w-full">
                        <div class="grid gap-3" style="grid-template-columns: repeat({{ $categories->count() }}, minmax(180px, 1fr));">
                            <!-- Category Headers -->
                            @foreach($categories as $category)
                                <div class="bg-blue-900 text-white p-6 rounded-xl text-center font-bold text-xl border-4 border-blue-700">
                                    {{ $category->name }}
                                </div>
                            @endforeach
                            
                            <!-- Questions -->
                            @php
                                $maxQuestions = $categories->max(fn($cat) => $cat->questions->count());
                            @endphp
                            
                            @for($i = 0; $i < $maxQuestions; $i++)
                                @foreach($categories as $category)
                                    @php
                                        $question = $category->questions->get($i);
                                    @endphp
                                    
                                    @if($question)
                                        <div class="aspect-square rounded-xl font-bold text-4xl flex items-center justify-center border-4 transition-all
                                            @if($question->is_answered)
                                                bg-gray-700 text-gray-500 border-gray-600
                                            @else
                                                bg-blue-600 text-yellow-400 border-blue-500
                                            @endif">
                                            @if(!$question->is_answered)
                                                ${{ $question->points }}
                                            @endif
                                        </div>
                                    @else
                                        <div class="aspect-square bg-blue-950 rounded-xl"></div>
                                    @endif
                                @endforeach
                            @endfor
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Scoreboard -->
            <div class="bg-white rounded-2xl shadow-2xl p-8">
                <h2 class="text-4xl font-bold text-gray-900 mb-6 text-center">Scoreboard</h2>
                
                @if($teams->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($teams as $index => $team)
                            <div class="relative p-6 rounded-xl border-4 transform transition-all hover:scale-105"
                                 style="background: linear-gradient(135deg, {{ $team->color }}20 0%, {{ $team->color }}40 100%); border-color: {{ $team->color }};">
                                
                                @if($index === 0)
                                    <div class="absolute -top-4 -right-4 bg-yellow-400 text-yellow-900 w-12 h-12 rounded-full flex items-center justify-center text-2xl font-bold shadow-lg">
                                        👑
                                    </div>
                                @endif
                                
                                <div class="flex items-center gap-4 mb-3">
                                    <div class="w-8 h-8 rounded-full shadow-lg" style="background-color: {{ $team->color }};"></div>
                                    <h3 class="text-2xl font-bold text-gray-900">{{ $team->name }}</h3>
                                </div>
                                
                                <div class="text-5xl font-bold text-center" style="color: {{ $team->color }};">
                                    ${{ $team->score }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12 text-gray-500">
                        <p class="text-2xl">Waiting for teams to join...</p>
                    </div>
                @endif
            </div>
        </div>
    @else
        <!-- Question View -->
        <div class="max-w-6xl mx-auto">
            <!-- Category Badge -->
            <div class="text-center mb-6">
                <span class="inline-block px-8 py-3 bg-blue-600 text-white text-2xl font-bold rounded-full shadow-lg">
                    {{ $currentQuestion->category->name }}
                </span>
            </div>
            
            <!-- Question Card -->
            <div class="bg-white rounded-2xl shadow-2xl p-12 mb-8 min-h-[400px] flex flex-col justify-center"
                 x-data="{ revealed: false }"
                 x-init="setTimeout(() => revealed = true, 100)"
                 x-show="revealed"
                 x-transition:enter="transition ease-out duration-500"
                 x-transition:enter-start="opacity-0 transform scale-95"
                 x-transition:enter-end="opacity-100 transform scale-100">
                
                <div class="text-center">
                    <!-- Point Value -->
                    <div class="mb-8">
                        <span class="text-7xl font-bold text-yellow-500">${{ $currentQuestion->points }}</span>
                    </div>
                    
                    <!-- Question Text -->
                    <div class="mb-8">
                        <p class="text-4xl font-bold text-gray-900 leading-relaxed">
                            {{ $currentQuestion->question }}
                        </p>
                    </div>
                    
                    <!-- Answer (if revealed) -->
                    @if($showAnswer)
                        <div class="mt-8 p-8 bg-green-100 border-4 border-green-400 rounded-2xl"
                             x-data="{ show: false }"
                             x-init="setTimeout(() => show = true, 100)"
                             x-show="show"
                             x-transition:enter="transition ease-out duration-500"
                             x-transition:enter-start="opacity-0 transform scale-95"
                             x-transition:enter-end="opacity-100 transform scale-100">
                            <p class="text-2xl text-green-700 font-semibold mb-3">Answer:</p>
                            <p class="text-5xl font-bold text-green-900">{{ $currentQuestion->answer }}</p>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Selected Team Indicator -->
            @if($selectedTeam)
                <div class="bg-white rounded-2xl shadow-2xl p-8 mb-8"
                     x-data="{ show: false }"
                     x-init="setTimeout(() => show = true, 100)"
                     x-show="show"
                     x-transition:enter="transition ease-out duration-500"
                     x-transition:enter-start="opacity-0 transform -translate-y-4"
                     x-transition:enter-end="opacity-100 transform translate-y-0">
                    <div class="text-center">
                        <p class="text-2xl text-gray-600 mb-4">Now Answering:</p>
                        <div class="flex items-center justify-center gap-6">
                            <div class="w-16 h-16 rounded-full shadow-lg animate-pulse" 
                                 style="background-color: {{ $selectedTeam->color }};"></div>
                            <h3 class="text-5xl font-bold" style="color: {{ $selectedTeam->color }};">
                                {{ $selectedTeam->name }}
                            </h3>
                        </div>
                    </div>
                </div>
            @endif
            
            <!-- Buzzers Status -->
            @if($buzzersEnabled && !$selectedTeam)
                <div class="bg-yellow-400 rounded-2xl shadow-2xl p-8 text-center animate-pulse">
                    <p class="text-4xl font-bold text-yellow-900">🔔 Buzzers Active! 🔔</p>
                </div>
            @endif
            
            <!-- Mini Scoreboard -->
            <div class="bg-white rounded-2xl shadow-2xl p-6">
                <div class="flex items-center justify-around">
                    @foreach($teams->take(5) as $team)
                        <div class="text-center">
                            <div class="w-12 h-12 rounded-full mx-auto mb-2 shadow-lg" 
                                 style="background-color: {{ $team->color }};"></div>
                            <p class="font-bold text-gray-900 mb-1">{{ $team->name }}</p>
                            <p class="text-2xl font-bold" style="color: {{ $team->color }};">
                                ${{ $team->score }}
                            </p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
    
    <!-- Session Status Indicator -->
    <div class="fixed top-4 right-4 z-50">
        <div class="px-6 py-3 rounded-full shadow-lg font-bold text-lg
            @if($session->status === 'waiting') bg-gray-200 text-gray-800
            @elseif($session->status === 'active') bg-green-500 text-white
            @elseif($session->status === 'paused') bg-yellow-500 text-white
            @else bg-red-500 text-white
            @endif">
            {{ ucfirst($session->status) }}
        </div>
    </div>
    
    @if($session->status === 'completed')
        <!-- Game Over Screen -->
        <div class="fixed inset-0 bg-black bg-opacity-90 flex items-center justify-center z-50">
            <div class="text-center text-white">
                <h1 class="text-8xl font-bold mb-8 animate-bounce">🎉 Game Over! 🎉</h1>
                
                @if($teams->count() > 0)
                    <div class="bg-white rounded-2xl p-12 text-gray-900 max-w-2xl mx-auto">
                        <h2 class="text-4xl font-bold mb-8">Final Scores</h2>
                        
                        <div class="space-y-4">
                            @foreach($teams as $index => $team)
                                <div class="flex items-center justify-between p-6 rounded-xl border-4"
                                     style="background: linear-gradient(135deg, {{ $team->color }}20 0%, {{ $team->color }}40 100%); border-color: {{ $team->color }};">
                                    <div class="flex items-center gap-4">
                                        <span class="text-3xl font-bold text-gray-600">#{{ $index + 1 }}</span>
                                        <div class="w-8 h-8 rounded-full" style="background-color: {{ $team->color }};"></div>
                                        <span class="text-2xl font-bold">{{ $team->name }}</span>
                                    </div>
                                    <span class="text-4xl font-bold" style="color: {{ $team->color }};">
                                        ${{ $team->score }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                        
                        @if($teams->first())
                            <div class="mt-8 text-6xl">
                                🏆 {{ $teams->first()->name }} Wins! 🏆
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
    // Auto-refresh teams and scores
    setInterval(() => {
        @this.call('loadGameData');
    }, 5000);
</script>
@endpush