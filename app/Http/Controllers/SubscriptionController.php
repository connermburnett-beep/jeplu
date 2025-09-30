<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show subscription plans
     */
    public function index()
    {
        $user = Auth::user();
        
        $plans = [
            'free' => [
                'name' => 'Free',
                'price' => 0,
                'features' => [
                    'Max 2 games',
                    'Max 5 categories',
                    'Max 3 questions per category',
                    'Max 2 teams',
                    'Branded',
                ],
            ],
            'basic' => [
                'name' => 'Basic',
                'price_monthly' => 4.99,
                'price_yearly' => 49,
                'stripe_monthly' => env('STRIPE_BASIC_MONTHLY'),
                'stripe_yearly' => env('STRIPE_BASIC_YEARLY'),
                'features' => [
                    'Max 10 games',
                    'Max 5 categories',
                    'Max 5 questions per category',
                    'Max 5 teams',
                    'Change background',
                    'Change timers for buzzer',
                ],
            ],
            'premium' => [
                'name' => 'Premium',
                'price_monthly' => 7.99,
                'price_yearly' => 79,
                'stripe_monthly' => env('STRIPE_PREMIUM_MONTHLY'),
                'stripe_yearly' => env('STRIPE_PREMIUM_YEARLY'),
                'features' => [
                    'Unlimited Games',
                    'Max 10 categories',
                    'Max 10 questions per category',
                    'Max 10 teams',
                    'Change background',
                    'Change theme',
                    'Add/change background music',
                    'Add/change team buzzer sound',
                    'AI Generated Questions (200/month)',
                    'Change timers for buzzer',
                ],
            ],
        ];

        return view('subscription.index', compact('plans', 'user'));
    }

    /**
     * Subscribe to a plan
     */
    public function subscribe(Request $request)
    {
        $request->validate([
            'plan' => 'required|in:basic_monthly,basic_yearly,premium_monthly,premium_yearly',
            'payment_method' => 'required|string',
        ]);

        $user = Auth::user();
        
        // Map plan to Stripe price ID
        $priceId = match($request->plan) {
            'basic_monthly' => env('STRIPE_BASIC_MONTHLY'),
            'basic_yearly' => env('STRIPE_BASIC_YEARLY'),
            'premium_monthly' => env('STRIPE_PREMIUM_MONTHLY'),
            'premium_yearly' => env('STRIPE_PREMIUM_YEARLY'),
        };

        // Determine tier
        $tier = str_starts_with($request->plan, 'basic') ? 'basic' : 'premium';

        try {
            $user->newSubscription('default', $priceId)
                ->create($request->payment_method);

            $user->update(['subscription_tier' => $tier]);

            return redirect()->route('subscription.index')
                ->with('success', 'Successfully subscribed to ' . ucfirst($tier) . ' plan!');
        } catch (\Exception $e) {
            return back()->with('error', 'Subscription failed: ' . $e->getMessage());
        }
    }

    /**
     * Cancel subscription
     */
    public function cancel()
    {
        $user = Auth::user();

        if ($user->subscribed('default')) {
            $user->subscription('default')->cancel();
            
            return redirect()->route('subscription.index')
                ->with('success', 'Subscription cancelled. You will have access until the end of your billing period.');
        }

        return back()->with('error', 'No active subscription found.');
    }

    /**
     * Resume cancelled subscription
     */
    public function resume()
    {
        $user = Auth::user();

        if ($user->subscription('default')->cancelled()) {
            $user->subscription('default')->resume();
            
            return redirect()->route('subscription.index')
                ->with('success', 'Subscription resumed successfully!');
        }

        return back()->with('error', 'Subscription is not cancelled.');
    }

    /**
     * Billing portal
     */
    public function billingPortal()
    {
        return Auth::user()->redirectToBillingPortal(route('subscription.index'));
    }
}