<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Subscription Plans') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <!-- Current Plan -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 mb-8">
                <h3 class="text-lg font-semibold mb-4">Current Plan</h3>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-2xl font-bold text-blue-600">{{ ucfirst($user->subscription_tier) }}</p>
                        @if($user->subscribed('default'))
                            <p class="text-sm text-gray-600 mt-1">
                                @if($user->subscription('default')->cancelled())
                                    Cancels on {{ $user->subscription('default')->ends_at->format('M d, Y') }}
                                @else
                                    Renews on {{ $user->subscription('default')->asStripeSubscription()->current_period_end }}
                                @endif
                            </p>
                        @endif
                    </div>
                    @if($user->subscribed('default'))
                        <div class="flex gap-2">
                            @if($user->subscription('default')->cancelled())
                                <form action="{{ route('subscription.resume') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                        Resume Subscription
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('subscription.cancel') }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel your subscription?');">
                                    @csrf
                                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                        Cancel Subscription
                                    </button>
                                </form>
                            @endif
                            <a href="{{ route('subscription.billing-portal') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Manage Billing
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Pricing Plans -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Free Plan -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 {{ $user->subscription_tier === 'free' ? 'ring-2 ring-blue-600' : '' }}">
                    <div class="text-center mb-6">
                        <h3 class="text-2xl font-bold mb-2">{{ $plans['free']['name'] }}</h3>
                        <p class="text-4xl font-bold text-gray-900">${{ $plans['free']['price'] }}</p>
                        <p class="text-gray-600">Forever</p>
                    </div>
                    <ul class="space-y-3 mb-6">
                        @foreach($plans['free']['features'] as $feature)
                            <li class="flex items-start">
                                <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="text-sm">{{ $feature }}</span>
                            </li>
                        @endforeach
                    </ul>
                    @if($user->subscription_tier === 'free')
                        <button disabled class="w-full bg-gray-300 text-gray-600 font-bold py-2 px-4 rounded cursor-not-allowed">
                            Current Plan
                        </button>
                    @endif
                </div>

                <!-- Basic Plan -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 {{ $user->subscription_tier === 'basic' ? 'ring-2 ring-blue-600' : '' }}">
                    <div class="text-center mb-6">
                        <h3 class="text-2xl font-bold mb-2">{{ $plans['basic']['name'] }}</h3>
                        <p class="text-4xl font-bold text-gray-900">${{ $plans['basic']['price_monthly'] }}</p>
                        <p class="text-gray-600">per month</p>
                        <p class="text-sm text-gray-500 mt-2">or ${{ $plans['basic']['price_yearly'] }}/year</p>
                    </div>
                    <ul class="space-y-3 mb-6">
                        @foreach($plans['basic']['features'] as $feature)
                            <li class="flex items-start">
                                <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="text-sm">{{ $feature }}</span>
                            </li>
                        @endforeach
                    </ul>
                    @if($user->subscription_tier === 'basic')
                        <button disabled class="w-full bg-gray-300 text-gray-600 font-bold py-2 px-4 rounded cursor-not-allowed">
                            Current Plan
                        </button>
                    @else
                        <button onclick="showSubscribeModal('basic')" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Subscribe
                        </button>
                    @endif
                </div>

                <!-- Premium Plan -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 {{ $user->subscription_tier === 'premium' ? 'ring-2 ring-blue-600' : '' }} relative">
                    <div class="absolute top-0 right-0 bg-yellow-400 text-gray-900 px-3 py-1 rounded-bl-lg rounded-tr-lg font-bold text-sm">
                        POPULAR
                    </div>
                    <div class="text-center mb-6">
                        <h3 class="text-2xl font-bold mb-2">{{ $plans['premium']['name'] }}</h3>
                        <p class="text-4xl font-bold text-gray-900">${{ $plans['premium']['price_monthly'] }}</p>
                        <p class="text-gray-600">per month</p>
                        <p class="text-sm text-gray-500 mt-2">or ${{ $plans['premium']['price_yearly'] }}/year</p>
                    </div>
                    <ul class="space-y-3 mb-6">
                        @foreach($plans['premium']['features'] as $feature)
                            <li class="flex items-start">
                                <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="text-sm">{{ $feature }}</span>
                            </li>
                        @endforeach
                    </ul>
                    @if($user->subscription_tier === 'premium')
                        <button disabled class="w-full bg-gray-300 text-gray-600 font-bold py-2 px-4 rounded cursor-not-allowed">
                            Current Plan
                        </button>
                    @else
                        <button onclick="showSubscribeModal('premium')" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Subscribe
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Subscribe Modal (Placeholder - would need Stripe Elements integration) -->
    <script>
        function showSubscribeModal(plan) {
            alert('Stripe payment integration would be implemented here for ' + plan + ' plan.\n\nIn production, this would:\n1. Load Stripe Elements\n2. Collect payment method\n3. Create subscription\n4. Redirect to success page');
        }
    </script>
</x-app-layout>