# Campaign Questionnaire Platform

Laravel 10+ full-stack application for creating and running campaign questionnaires with multiple question types and scoring.

## Requirements

- PHP 8.2+
- Composer
- Node.js & npm
- MySQL (or SQLite for local dev)

## Setup

1. **Install dependencies**
   ```bash
   composer install
   npm install && npm run build
   ```

2. **Environment**
   - Copy `.env.example` to `.env`
   - For MySQL: set `DB_CONNECTION=mysql`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
   - Create the database: `mysql -e "CREATE DATABASE campaign_with_quiz;"`

3. **Key & migrations**
   ```bash
   php artisan key:generate
   php artisan migrate
   php artisan db:seed
   ```

4. **Admin login**
   - URL: `/admin/login`
   - Email: `admin@example.com`
   - Password: `password`

## Start the project

1. **Start the Laravel development server**
   ```bash
   php artisan serve
   ```
   The app will be available at **http://localhost:8000** (or the URL shown in the terminal).

2. **Optional: frontend assets**
   - After `npm run build`, the app works as-is. For live CSS/JS reload during development, run in a separate terminal:
   ```bash
   npm run dev
   ```
   Keep this running while you work on Blade/JS.

3. **Quick links**
   - Home: http://localhost:8000
   - Admin login: http://localhost:8000/admin/login
   - Demo campaign: http://localhost:8000/campaign/demo-customer-satisfaction

## Usage

- **Admin**: `/admin` — Dashboard, create/edit campaigns, add questions (MCQ single/multi, text, number), publish, view/export reports.
- **Public**: `/campaign/{slug}` — Take a published campaign (e.g. `/campaign/demo-customer-satisfaction` after seeding).

## Features

- Admin auth (registration via seeder)
- Campaign CRUD with rich text (Quill.js) and unique slug
- Question types: MCQ single/multi, text (keyword scoring), number (exact/range)
- No-overlap validation for number ranges
- ScoringService for consistent scoring
- Public flow: name/email → one question at a time → results with breakdown
- Reports: list participants, per-response detail, CSV export, pagination, average score
- Campaign expiry (optional), duplicate submission protection, mandatory validation

## Demo data

After `php artisan db:seed`, a published campaign `demo-customer-satisfaction` is available at:

`http://your-domain/campaign/demo-customer-satisfaction`
