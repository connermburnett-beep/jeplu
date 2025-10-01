<div class="min-h-screen flex flex-col transition-all duration-500"
     x-data="{ 
         backgroundColor: '{{ $team->color }}',
         isSelected: @entangle('isSelected'),
         hasBuzzed: @entangle('hasBuzzed'),
         showCorrect: false,
         showWrong: false
     }"
     :style="'background-color: ' + (isSelected ? backgroundColor : '#1e3a8a')"
     @score-updated.window="
         if ($event.detail.isCorrect) {
             showCorrect = true;
             setTimeout(() => showCorrect = false, 2000);
         } else {
             showWrong = true;
             setTimeout(() => showWrong = false, 2000);
         }
     ">
    
    <!-- Correct Answer Flash -->
    <div x-show="showCorrect" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-green-500 bg-opacity-50 pointer-events-none z-50 flex items-center justify-center"
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
         class="fixed inset-0 bg-red-500 bg-opacity-50 pointer-events-none z-50 flex items-center justify-center"
         style="display: none;">
        <div class="text-9xl font-bold text-white animate-bounce">✗</div>
    </div>
    
    <div class="flex-1 flex flex-col items-center justify-center p-8">
        <!-- Team Info Header -->
        <div class="text-center mb-8">
            <div class="w-24 h-24 rounded-full mx-auto mb-4 shadow-2xl" 
                 style="background-color: {{ $team->color }};"></div>
            <h1 class="text-5xl font-bold text-white mb-2">{{ $team->name }}</h1>
            <p class="text-3xl font-bold text-white opacity-90">${{ $team->score }}</p>
        </div>
        
        @if($session->status === 'completed')
            <!-- Game Over -->
            <div class="text-center">
                <div class="text-8xl mb-6">🎉</div>
                <h2 class="text-6xl font-bold text-white mb-4">Game Over!</h2>
                <p class="text-3xl text-white opacity-90">Final Score: ${{ $team->score }}</p>
                
                @if($teams->count() > 0 && $teams->first()->id === $team->id)
                    <div class="mt-8 text-7xl animate-bounce">
                        🏆 Winner! 🏆
                    </div>
                @endif
            </div>
        @elseif(!$currentQuestion)
            <!-- Waiting for Question -->
            <div class="text-center">
                <div class="text-6xl mb-6 animate-pulse">⏳</div>
                <h2 class="text-4xl font-bold text-white mb-4">Waiting for next question...</h2>
                <p class="text-xl text-white opacity-75">Get ready to buzz in!</p>
            </div>
        @else
            <!-- Question Active -->
            <div class="w-full max-w-4xl">
                <!-- Question Display -->
                <div class="bg-white bg-opacity-95 rounded-3xl shadow-2xl p-8 mb-8">
                    <div class="text-center mb-6">
                        <span class="inline-block px-6 py-2 bg-blue-600 text-white text-xl font-bold rounded-full">
                            {{ $currentQuestion->category }}
                        </span>
                    </div>
                    
                    <div class="text-center mb-6">
                        <span class="text-5xl font-bold text-yellow-500">${{ $currentQuestion->points }}</span>
                    </div>
                    
                    <p class="text-3xl font-bold text-gray-900 text-center leading-relaxed">
                        {{ $currentQuestion->question }}
                    </p>
                    
                    @if($showAnswer)
                        <div class="mt-6 p-6 bg-green-100 border-4 border-green-400 rounded-2xl">
                            <p class="text-xl text-green-700 font-semibold mb-2 text-center">Answer:</p>
                            <p class="text-4xl font-bold text-green-900 text-center">{{ $currentQuestion->answer }}</p>
                        </div>
                    @endif
                </div>
                
                <!-- Buzzer Button -->
                <div class="flex justify-center">
                    @if($isSelected)
                        <div class="text-center">
                            <div class="text-6xl mb-4 animate-pulse">👆</div>
                            <h3 class="text-5xl font-bold text-white mb-4">You're Up!</h3>
                            <p class="text-2xl text-white opacity-90">Time to answer!</p>
                        </div>
                    @elseif($hasBuzzed)
                        <div class="text-center">
                            <div class="text-6xl mb-4">✋</div>
                            <h3 class="text-4xl font-bold text-white mb-4">Buzzed In!</h3>
                            <p class="text-xl text-white opacity-90">Waiting for teacher...</p>
                        </div>
                    @elseif($buzzersEnabled)
                        <button 
                            wire:click="buzz"
                            class="w-80 h-80 rounded-full font-bold text-4xl shadow-2xl transform transition-all hover:scale-110 active:scale-95 animate-pulse"
                            style="background: linear-gradient(135deg, {{ $team->color }} 0%, {{ $team->color }}dd 100%); color: white;">
                            <div class="text-6xl mb-4">🔔</div>
                            <div>BUZZ IN!</div>
                        </button>
                    @else
                        <div class="text-center">
                            <div class="w-80 h-80 rounded-full bg-gray-600 bg-opacity-50 flex items-center justify-center">
                                <div>
                                    <div class="text-6xl mb-4 opacity-50">🔕</div>
                                    <p class="text-2xl text-white opacity-75">Buzzers Locked</p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>
    
    <!-- Mini Scoreboard -->
    <div class="bg-white bg-opacity-10 backdrop-blur-sm p-6">
        <div class="max-w-6xl mx-auto">
            <div class="flex items-center justify-around flex-wrap gap-4">
                @foreach($teams->take(6) as $t)
                    <div class="text-center {{ $t->id === $team->id ? 'scale-110' : '' }} transition-transform">
                        <div class="w-12 h-12 rounded-full mx-auto mb-2 shadow-lg {{ $t->id === $team->id ? 'ring-4 ring-white' : '' }}" 
                             style="background-color: {{ $t->color }};"></div>
                        <p class="font-bold text-white text-sm mb-1">{{ $t->name }}</p>
                        <p class="text-xl font-bold text-white">${{ $t->score }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    
    <!-- Status Indicator -->
    <div class="fixed top-4 right-4 z-50">
        <div class="px-4 py-2 rounded-full shadow-lg font-bold
            @if($session->status === 'waiting') bg-gray-200 text-gray-800
            @elseif($session->status === 'active') bg-green-500 text-white
            @elseif($session->status === 'paused') bg-yellow-500 text-white
            @else bg-red-500 text-white
            @endif">
            {{ ucfirst($session->status) }}
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Auto-refresh team data
    setInterval(() => {
        @this.call('loadTeams');
    }, 5000);
    
    // Keyboard shortcut for buzzing (spacebar)
    document.addEventListener('keydown', (e) => {
        if (e.code === 'Space' && @this.buzzersEnabled && !@this.hasBuzzed) {
            e.preventDefault();
            @this.call('buzz');
        }
    });
</script>
@endpush