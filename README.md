# Campaign Questionnaire Platform

Laravel 10+ full-stack application for creating and running campaign questionnaires with multiple question types and scoring.

---

## Project summary

**What it is**  
A web app where admins create **campaigns** (questionnaires) and share a link. Participants open the link, enter their details, answer questions one by one, and see a results summary. Admins view and export response reports.

**What it contains**

- **Admin area** (`/admin`): Dashboard, campaign CRUD, question management (add/edit/delete, reorder), publish/draft, reports and CSV export. Admin login is separate (no public registration for admin).
- **Public area**: Welcome page, campaign entry by **slug** (e.g. `/campaign/demo-customer-satisfaction`), flow: name/email → one question per screen → completion page with score breakdown.
- **Data model**: **Campaign** (title, description, status, slug, optional expiry, allow multiple responses) → **Question** (text, type, order, mandatory) → type-specific data: **Option** (MCQ), **TextKeyword** (keyword rules), **NumberRule** (exact or min/max + score). Each participation is a **Response** with **ResponseAnswer** per question; scoring is computed server-side.

**How it's built**

- **Backend:** Laravel 12, PHP 8.2, MySQL (or SQLite). Auth via Laravel (session); admin role gate (`admin` middleware).
- **Frontend:** Blade views, Bootstrap 5, Quill.js for rich-text question/description editing, vanilla JS for the add-question form and public flow. Vite for asset build.
- **Scoring:** Central `ScoringService` scores each response from stored answers: MCQ (single/multi) by option scores, text by keyword rules, number by exact or range rules. Totals and per-answer scores stored on `responses` and `response_answers`.

**Features in use**

- Admin auth (login/logout, role check).
- Campaign CRUD with unique slug; rich text (Quill) for title/description/question text.
- Question types: **MCQ Single** (one correct), **MCQ Multi** (multiple options, scores summed), **Text** (keyword → score rules), **Number** (exact value or min–max range + score).
- Validation: mandatory questions, number-rule no-overlap, duplicate submission control (optional per campaign).
- Public flow: session-based; one question per page; next/previous; completion page with score breakdown.
- Reports: list participants, per-response detail, CSV export, pagination, average score.
- Optional campaign expiry; optional allow multiple responses per participant.

---

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
