<div class="min-h-screen bg-gradient-to-br from-blue-900 via-blue-800 to-blue-900 p-6">
    <!-- Header -->
    <div class="max-w-7xl mx-auto mb-6">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ $game->name }}</h1>
                    <p class="text-gray-600 mt-1">Session Code: <span class="font-mono font-bold text-blue-600">{{ $session->session_code }}</span></p>
                </div>
                
                <div class="flex items-center gap-4">
                    @if($session->status === 'waiting')
                        <button wire:click="startSession" class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-semibold transition">
                            Start Game
                        </button>
                    @elseif($session->status === 'active')
                        <button wire:click="pauseSession" class="px-6 py-3 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 font-semibold transition">
                            Pause Game
                        </button>
                    @elseif($session->status === 'paused')
                        <button wire:click="resumeSession" class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-semibold transition">
                            Resume Game
                        </button>
                    @endif
                    
                    @if($session->status !== 'completed')
                        <button wire:click="endSession" wire:confirm="Are you sure you want to end this game session?" class="px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 font-semibold transition">
                            End Game
                        </button>
                    @endif
                    
                    <a href="{{ route('play.presentation', $session) }}" target="_blank" class="px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 font-semibold transition">
                        Open Presentation View
                    </a>
                </div>
            </div>
            
            <!-- Status Badge -->
            <div class="mt-4">
                <span class="px-4 py-2 rounded-full text-sm font-semibold
                    @if($session->status === 'waiting') bg-gray-200 text-gray-800
                    @elseif($session->status === 'active') bg-green-200 text-green-800
                    @elseif($session->status === 'paused') bg-yellow-200 text-yellow-800
                    @else bg-red-200 text-red-800
                    @endif">
                    {{ ucfirst($session->status) }}
                </span>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Game Board (Left Side) -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">Game Board</h2>
                
                <!-- Categories and Questions Grid -->
                <div class="overflow-x-auto">
                    <div class="inline-block min-w-full">
                        <div class="grid gap-2" style="grid-template-columns: repeat({{ $categories->count() }}, minmax(150px, 1fr));">
                            <!-- Category Headers -->
                            @foreach($categories as $category)
                                <div class="bg-blue-900 text-white p-4 rounded-lg text-center font-bold">
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
                                        <button 
                                            wire:click="revealQuestion({{ $question->id }})"
                                            @if($question->is_answered) disabled @endif
                                            class="aspect-square rounded-lg font-bold text-2xl transition-all
                                                @if($question->is_answered)
                                                    bg-gray-300 text-gray-500 cursor-not-allowed
                                                @elseif($currentQuestion && $currentQuestion->id === $question->id)
                                                    bg-yellow-400 text-blue-900 ring-4 ring-yellow-500
                                                @else
                                                    bg-blue-600 text-yellow-400 hover:bg-blue-700 hover:scale-105
                                                @endif">
                                            @if(!$question->is_answered)
                                                ${{ $question->points }}
                                            @endif
                                        </button>
                                    @else
                                        <div class="aspect-square bg-gray-100 rounded-lg"></div>
                                    @endif
                                @endforeach
                            @endfor
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Current Question Control -->
            @if($currentQuestion)
                <div class="mt-6 bg-white rounded-lg shadow-lg p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Current Question</h3>
                    
                    <div class="bg-blue-50 border-2 border-blue-200 rounded-lg p-6 mb-4">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex-1">
                                <p class="text-sm text-blue-600 font-semibold mb-2">{{ $currentQuestion->category->name }}</p>
                                <p class="text-lg font-semibold text-gray-900">{{ $currentQuestion->question }}</p>
                            </div>
                            <span class="text-2xl font-bold text-yellow-600">${{ $currentQuestion->points }}</span>
                        </div>
                        
                        @if($showAnswer)
                            <div class="mt-4 p-4 bg-green-100 border-2 border-green-300 rounded-lg">
                                <p class="text-sm text-green-700 font-semibold mb-1">Answer:</p>
                                <p class="text-lg font-bold text-green-900">{{ $currentQuestion->answer }}</p>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Buzzer Controls -->
                    <div class="flex gap-4 mb-4">
                        @if(!$buzzersEnabled)
                            <button wire:click="enableBuzzers" class="flex-1 px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-semibold transition">
                                Enable Buzzers
                            </button>
                        @else
                            <button wire:click="disableBuzzers" class="flex-1 px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 font-semibold transition">
                                Disable Buzzers
                            </button>
                        @endif
                        
                        @if(!$showAnswer)
                            <button wire:click="revealAnswer" class="flex-1 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold transition">
                                Reveal Answer
                            </button>
                        @endif
                        
                        <button wire:click="closeQuestion" class="px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 font-semibold transition">
                            Close Question
                        </button>
                    </div>
                    
                    <!-- Buzzed Teams -->
                    @if(count($buzzedTeams) > 0)
                        <div class="mt-4">
                            <h4 class="text-lg font-bold text-gray-900 mb-3">Buzzed Teams (in order):</h4>
                            <div class="space-y-2">
                                @foreach($buzzedTeams as $buzzedTeam)
                                    <div class="flex items-center justify-between p-4 rounded-lg border-2" 
                                         style="background-color: {{ $buzzedTeam['color'] }}20; border-color: {{ $buzzedTeam['color'] }};">
                                        <div class="flex items-center gap-3">
                                            <div class="w-4 h-4 rounded-full" style="background-color: {{ $buzzedTeam['color'] }};"></div>
                                            <span class="font-semibold text-gray-900">{{ $buzzedTeam['name'] }}</span>
                                            <span class="text-sm text-gray-600">({{ $buzzedTeam['response_time_ms'] }}ms)</span>
                                        </div>
                                        
                                        <div class="flex gap-2">
                                            <button wire:click="selectTeam({{ $buzzedTeam['id'] }})" 
                                                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-semibold transition">
                                                Select
                                            </button>
                                            <button wire:click="awardPoints({{ $buzzedTeam['id'] }}, true)" 
                                                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm font-semibold transition">
                                                ✓ Correct
                                            </button>
                                            <button wire:click="awardPoints({{ $buzzedTeam['id'] }}, false)" 
                                                    class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm font-semibold transition">
                                                ✗ Wrong
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        </div>
        
        <!-- Teams & Scores (Right Side) -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-lg p-6 sticky top-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-2xl font-bold text-gray-900">Teams</h2>
                    <button wire:click="openTeamModal" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold transition">
                        + Add Team
                    </button>
                </div>
                
                @if($teams->count() > 0)
                    <div class="space-y-3">
                        @foreach($teams->sortByDesc('score') as $team)
                            <div class="p-4 rounded-lg border-2 transition-all
                                @if($selectedTeamId === $team->id) ring-4 ring-yellow-400 @endif"
                                 style="background-color: {{ $team->color }}20; border-color: {{ $team->color }};">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center gap-3">
                                        <div class="w-4 h-4 rounded-full" style="background-color: {{ $team->color }};"></div>
                                        <span class="font-bold text-gray-900">{{ $team->name }}</span>
                                    </div>
                                    <button wire:click="removeTeam({{ $team->id }})" 
                                            wire:confirm="Are you sure you want to remove this team?"
                                            class="text-red-600 hover:text-red-800 text-sm">
                                        Remove
                                    </button>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-3xl font-bold" style="color: {{ $team->color }};">
                                        ${{ $team->score }}
                                    </span>
                                    <span class="text-xs text-gray-600 font-mono">{{ $team->buzzer_code }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">
                        <p class="mb-2">No teams yet</p>
                        <p class="text-sm">Add teams to start playing</p>
                    </div>
                @endif
                
                <!-- Team Join Instructions -->
                <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <h3 class="font-bold text-blue-900 mb-2">Team Join Instructions:</h3>
                    <p class="text-sm text-blue-800 mb-2">Students can join at:</p>
                    <p class="text-xs font-mono bg-white p-2 rounded border border-blue-300 break-all">
                        {{ route('play.team') }}?code={{ $session->session_code }}
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Team Modal -->
    @if($showTeamModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" wire:click="closeTeamModal">
            <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md" wire:click.stop>
                <h3 class="text-2xl font-bold text-gray-900 mb-4">Add New Team</h3>
                
                <form wire:submit="addTeam">
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Team Name</label>
                        <input type="text" wire:model="teamName" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Enter team name" required>
                        @error('teamName') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>
                    
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Team Color</label>
                        <div class="grid grid-cols-4 gap-2">
                            @foreach($availableColors as $color)
                                <button type="button" 
                                        wire:click="$set('teamColor', '{{ $color }}')"
                                        class="w-full aspect-square rounded-lg border-4 transition-all hover:scale-110
                                               @if($teamColor === $color) border-gray-900 @else border-gray-300 @endif"
                                        style="background-color: {{ $color }};">
                                </button>
                            @endforeach
                        </div>
                    </div>
                    
                    <div class="flex gap-3">
                        <button type="button" wire:click="closeTeamModal" 
                                class="flex-1 px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 font-semibold transition">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold transition">
                            Add Team
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
    
    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="fixed bottom-4 right-4 bg-green-600 text-white px-6 py-3 rounded-lg shadow-lg z-50" 
             x-data="{ show: true }" 
             x-show="show" 
             x-init="setTimeout(() => show = false, 3000)">
            {{ session('success') }}
        </div>
    @endif
    
    @if (session()->has('error'))
        <div class="fixed bottom-4 right-4 bg-red-600 text-white px-6 py-3 rounded-lg shadow-lg z-50" 
             x-data="{ show: true }" 
             x-show="show" 
             x-init="setTimeout(() => show = false, 3000)">
            {{ session('error') }}
        </div>
    @endif
    
    @if (session()->has('info'))
        <div class="fixed bottom-4 right-4 bg-blue-600 text-white px-6 py-3 rounded-lg shadow-lg z-50" 
             x-data="{ show: true }" 
             x-show="show" 
             x-init="setTimeout(() => show = false, 3000)">
            {{ session('info') }}
        </div>
    @endif
</div>