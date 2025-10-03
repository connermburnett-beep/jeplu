<div class="min-h-screen bg-gradient-to-br from-blue-900 via-blue-800 to-blue-900 flex items-center justify-center p-8">
    <div class="bg-white rounded-2xl shadow-2xl p-12 w-full max-w-md">
        <div class="text-center mb-8">
            <div class="text-6xl mb-4">🎮</div>
            <h1 class="text-4xl font-bold text-gray-900 mb-2">Join Game</h1>
            <p class="text-gray-600">Enter your session and team codes</p>
        </div>
        
        <form wire:submit="joinSession" class="space-y-6">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Session Code</label>
                <input 
                    type="text" 
                    wire:model="sessionCode" 
                    class="w-full px-4 py-3 text-2xl font-mono text-center border-2 border-gray-300 rounded-lg focus:ring-4 focus:ring-blue-500 focus:border-transparent uppercase"
                    placeholder="ABC12345"
                    maxlength="8"
                    required
                    autofocus>
                @error('sessionCode') 
                    <span class="text-red-600 text-sm mt-1 block">{{ $message }}</span> 
                @enderror
            </div>
            
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Team Code</label>
                <input 
                    type="text" 
                    wire:model="teamCode" 
                    class="w-full px-4 py-3 text-2xl font-mono text-center border-2 border-gray-300 rounded-lg focus:ring-4 focus:ring-blue-500 focus:border-transparent uppercase"
                    placeholder="XYZ123"
                    maxlength="6"
                    required>
                @error('teamCode') 
                    <span class="text-red-600 text-sm mt-1 block">{{ $message }}</span> 
                @enderror
            </div>
            
            <button 
                type="submit" 
                class="w-full px-6 py-4 bg-blue-600 text-white text-xl font-bold rounded-lg hover:bg-blue-700 transform transition-all hover:scale-105 active:scale-95 shadow-lg">
                Join Game
            </button>
        </form>
        
        @if (session()->has('error'))
            <div class="mt-6 p-4 bg-red-100 border-2 border-red-300 rounded-lg">
                <p class="text-red-800 font-semibold text-center">{{ session('error') }}</p>
            </div>
        @endif
        
        <div class="mt-8 pt-8 border-t border-gray-200">
            <p class="text-sm text-gray-600 text-center">
                Ask your teacher for the session code and your team's buzzer code.
            </p>
        </div>
    </div>
</div>