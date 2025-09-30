# JepluSaaS Setup Guide

## Overview
JepluSaaS is a Jeopardy-style quiz game platform for teachers, built with Laravel 12, Livewire, and Tailwind CSS.

## Prerequisites
- PHP 8.2+
- Composer
- Node.js 20.x
- SQLite (or MySQL/PostgreSQL)
- Stripe Account (for payments)
- OpenAI API Key (for AI features)

## Installation Steps

### 1. Environment Configuration

Copy the `.env.example` to `.env` and configure the following:

```bash
cp .env.example .env
php artisan key:generate
```

### 2. Database Configuration

For SQLite (default):
```env
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database/database.sqlite
```

Create the database file:
```bash
touch database/database.sqlite
```

### 3. Stripe Configuration

Add your Stripe keys to `.env`:
```env
STRIPE_KEY=your_stripe_publishable_key
STRIPE_SECRET=your_stripe_secret_key
STRIPE_WEBHOOK_SECRET=your_webhook_secret

# Subscription Plan Price IDs
STRIPE_BASIC_MONTHLY=price_xxxxx
STRIPE_BASIC_YEARLY=price_xxxxx
STRIPE_PREMIUM_MONTHLY=price_xxxxx
STRIPE_PREMIUM_YEARLY=price_xxxxx
```

### 4. OpenAI Configuration

Add your OpenAI API key:
```env
OPENAI_API_KEY=your_openai_api_key
```

### 5. Laravel Reverb Configuration

For real-time features:
```env
REVERB_APP_ID=your_app_id
REVERB_APP_KEY=your_app_key
REVERB_APP_SECRET=your_app_secret
REVERB_HOST=localhost
REVERB_PORT=8080
REVERB_SCHEME=http
```

### 6. Run Migrations

```bash
php artisan migrate
```

### 7. Install Dependencies

```bash
composer install
npm install
```

### 8. Build Assets

```bash
npm run build
```

## Running the Application

### Development Mode

Terminal 1 - Laravel Server:
```bash
php artisan serve
```

Terminal 2 - Vite Dev Server:
```bash
npm run dev
```

Terminal 3 - Laravel Reverb (WebSocket Server):
```bash
php artisan reverb:start
```

Terminal 4 - Queue Worker:
```bash
php artisan queue:work
```

### Production Mode

```bash
npm run build
php artisan optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Subscription Tiers

### Free Tier
- Max 2 games
- Max 5 categories per game
- Max 3 questions per category
- Max 2 teams
- Branded

### Basic Tier ($4.99/month or $49/year)
- Max 10 games
- Max 5 categories per game
- Max 5 questions per category
- Max 5 teams
- Change background
- Change timers for buzzer

### Premium Tier ($7.99/month or $79/year)
- Unlimited games
- Max 10 categories per game
- Max 10 questions per category
- Max 10 teams
- Change background
- Change theme
- Add/change background music
- Add/change team buzzer sound
- AI Generated Questions (200/month)
- Change timers for buzzer

## Features

### Game Management
- Create, edit, and delete games
- Organize questions into categories
- Set point values and time limits for each question
- Jeopardy-style game board interface

### Game Play
- Teacher control interface
- Presentation view for projector/board
- Student/team buzzer interface
- Real-time buzzer events with WebSockets
- Automatic scoring
- Timer for answers

### AI Integration (Premium)
- Generate questions using OpenAI GPT-4
- Customizable difficulty levels
- Category-specific question generation
- Monthly usage limits

### Subscription Management
- Stripe integration for payments
- Multiple subscription plans
- Billing portal
- Automatic tier enforcement

## Testing

Run the test suite:
```bash
php artisan test
```

## Troubleshooting

### Database Issues
If migrations fail, ensure the database file exists and has proper permissions:
```bash
touch database/database.sqlite
chmod 664 database/database.sqlite
```

### Asset Build Issues
Clear the cache and rebuild:
```bash
npm run build
php artisan optimize:clear
```

### WebSocket Connection Issues
Ensure Reverb is running and the configuration matches your `.env` file.

## Support

For issues or questions, please refer to the documentation or contact support.