# AGENTS.md

Guidance for AI coding agents working on this repository.

## Commands

```bash
composer setup           # Full setup: install deps, .env, key:generate, migrate, npm install & build
composer dev             # Run dev servers: artisan serve + queue + pail logs + Vite
composer test            # Run full test suite (clears config first)
php artisan test tests/Feature/ExampleTest.php
php artisan test --filter=ExampleTest
./vendor/bin/pint        # Format PHP code (Laravel Pint)
php artisan migrate
php artisan migrate:fresh --seed
php artisan tinker
npm run build            # Build frontend (Vite + Tailwind)
npm run dev              # Vite dev server
```

## Stack

- **PHP 8.3** with strict types, typed properties, return types everywhere
- **Laravel 13.8** — uses PHP 8 attributes (`#[Fillable]`, `#[Hidden]`) over traditional `$fillable` / `$hidden` properties
- **PostgreSQL** (pgsql) — both dev and test environments
- **Database-backed** sessions, cache, and queue (no Redis)
- **Vite 8** + **Tailwind CSS 4** for frontend (minimal, API-focused)
- **PHPUnit 12** for testing

## Code Style

- **PHP 8 attributes** for model metadata (`#[Fillable]`, `#[Hidden]`, `#[HasFactory]` via `use`)
- **Type hints** on all methods and properties; `: void` return on mutation methods
- **Laravel Pint** (`./vendor/bin/pint`) for formatting — run before committing
- **Spanish domain language** — models/tables use Spanish terms (sucursales/branches, odontólogos/dentists, pacientes/patients, procedimientos/procedures, citas/appointments)
- **snake_case** for database columns and migration method names
- **PascalCase** for class names, **camelCase** for methods/properties
- Return types on all methods; avoid docblocks where type hints suffice
- Migration format: `return new class extends Migration` with typed `up(): void` / `down(): void`
- **Form Request classes** for all validation — never use `$request->validate([...])` inline. Place under `app/Http/Requests/{Domain}/`, inject into controller method as the typed parameter, and call `$request->validated()`.

## Architecture

```
app/
├── Http/Controllers/
│   └── Api/                           # API controllers (Auth, Branch, Dentist, Patient, etc.)
├── Http/Requests/                     # Form Request classes per domain (Auth, Branch, Dentist, ...)
├── Models/
│   └── User.php                       # Only model so far
└── Providers/AppServiceProvider.php   # Empty
bootstrap/app.php                      # Middleware, exceptions, routing config
routes/
├── web.php                            # GET / (welcome view)
├── api.php                            # NOT YET CREATED — needs to be added
└── console.php                        # Artisan commands
database/
├── migrations/                        # 14 migration files
├── factories/UserFactory.php           # Only factory so far
└── seeders/DatabaseSeeder.php          # Seeds test@example.com user
tests/
├── Unit/ExampleTest.php
├── Feature/ExampleTest.php
└── TestCase.php                        # Base (empty)
config/                                # 10 config files
```

### Request Lifecycle
- `bootstrap/app.php` — routing, middleware, exception handling
- `routes/web.php` — web routes
- `routes/api.php` — should be added with `->withRouting(api: __DIR__.'/../routes/api.php')` in `bootstrap/app.php` and `api` middleware group
- Controllers extend abstract `Controller` in `App\Http\Controllers`

### Current State
- API routes active via `routes/api.php` with full CRUD for all domains
- Controllers exist for all domains (Auth, Branch, Dentist, Patient, Procedure, Product, Promotion, Appointment, Report, User, Role, Permission)
- Form Request classes used for all validation (never `$request->validate([...])` inline)
- Domain tables exist (branches, dentists, patients, etc.) with corresponding controllers and form requests
- User model is out of sync with the users migration (see Notes)

## Database

### Connection
- **PostgreSQL** in all environments
- Test DB: `dental_saas_test` (configured in phpunit.xml)

### Migration Patterns
- Anonymous class: `return new class extends Migration`
- Foreign keys: mix of `foreignId('id_user')->constrained('users')` and `foreignId('branch_id')->constrained('branches')`
- Many tables have a `state` column with default `'Activo'`
- Soft deletes where needed (`softDeletes()` on products)

### Tables & Domain Model

| Table | Key Fields | Relationships |
|---|---|---|
| `users` | document, email, password, type_user, birth, photo, state | — |
| `branches` | address, name, contact, city, state | — |
| `dentists` | name, city | `id_user` → users |
| `patients` | name, city, telephone | `id_user` → users |
| `procedures` | name, duration, state | — |
| `dentist_procedures` | — | `procedure_id` → procedures, `dentist_id` → dentists |
| `appointments` | day, hour, pay, type_state, state | `branch_id` → branches, `patient_id` → patients, `dentist_procedure_id` → dentist_procedures |
| `schedules` | hour_start, hour_end, break, break_start, break_end, attend, day | `dentist_id` → dentists |
| `promotions` | date_start, date_end, details, discount, limit_patients, status | — |
| `invoices` | price | `procedure_id` → procedures, `appointment_id` → appointments |
| `products` | active_principle, concentration, amount, pharmaceutical_form, batch, expiration_date, etc. | — |

### Foreign Key Naming Inconsistencies
- `users` table: FK columns use `id_user` pattern (referenced as `id_user` in dentists, patients)
- Other tables: use standard `{table}_id` pattern (e.g., `branch_id`, `patient_id`, `procedure_id`)
- `dentist_procedures` uses `procedure_id` / `dentist_id` with manual `foreign()` calls (not `constrained()`)

## Model Conventions

```php
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
```

- Use `#[Fillable]` and `#[Hidden]` class attributes instead of `$fillable` / `$hidden` properties
- `HasFactory` trait with `@use HasFactory<FactoryName>` docblock for IDE support
- `casts()` method (not `$casts` property) for attribute casting

## Testing

- **PHPUnit 12** — tests use PostgreSQL, array cache/queue/mail, sync queue
- Place tests in `tests/Feature/` or `tests/Unit/` matching the class under test
- Extend `Tests\TestCase` (which extends `Illuminate\Foundation\Testing\TestCase`)
- No external services needed for tests; DB is `dental_saas_test`
- Run full suite: `composer test` or `php artisan test`

## Common Tasks

### Adding API Routes
1. Create `routes/api.php`
2. Register api routing in `bootstrap/app.php`: `->withRouting(api: __DIR__.'/../routes/api.php', ...)`
3. Use `Route::apiResource()` or `Route::prefix('v1')->group(...)`

### Adding a New Model
1. Create migration with anonymous class pattern
2. Create model extending `Illuminate\Database\Eloquent\Model` (or `Authenticatable`)
3. Add `#[Fillable]`, `#[Hidden]` attributes; `HasFactory` trait; `casts()` method
4. Create factory in `database/factories/`
5. Create controller extending `App\Http\Controllers\Controller`
6. Add resource route in `routes/api.php`

### Adding a New Controller Method with Validation
1. Create a Form Request in `app/Http/Requests/{Domain}/` extending `Illuminate\Foundation\Http\FormRequest`
2. Define `rules(): array` returning the validation rules
3. Inject the Form Request as the controller method parameter (e.g., `StoreBranchRequest $request`)
4. Use `$request->validated()` to get the validated data
5. Never use `$request->validate([...])` inline

### Adding a New Migration
- Use `php artisan make:migration` or write manually with the anonymous class pattern
- Follow existing foreign key conventions (check which pattern the table uses)

## Notes

- **User model is out of sync with users migration** — the migration has columns `document`, `type_user`, `birth`, `verify_birth`, `verify_email`, `photo`, `state`, `two_factor_secret`, `two_factor_recovery_codes` but the model/factory still has the default Laravel scaffold (`name`, `email`, `password`, `remember_token`, `email_verified_at`). The model needs to be updated to match the actual schema.
- **No API routes exist yet** — the project needs `routes/api.php` and the corresponding `bootstrap/app.php` routing registration.
- **Spanish language domain** — table/column names use Spanish. Future features (reports, dashboards, etc.) should follow the same convention.
