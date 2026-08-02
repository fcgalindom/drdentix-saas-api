# AGENTS.md

## Commands

```bash
composer setup             # Full setup: deps, .env, key:generate, migrate, npm install & build
composer dev               # Dev servers: artisan serve + queue + pail logs + Vite (concurrently)
composer test              # Run full test suite (clears config first)
php artisan test --filter=TestName
./vendor/bin/pint          # Format PHP code (Laravel Pint)
php artisan migrate:fresh --seed
php artisan scribe:generate  # Generate API docs (knuckleswtf/scribe)
```

## Stack

- **PHP 8.3**, strict types, typed properties, return types everywhere
- **Laravel 13.8** — uses `$fillable` / `$hidden` properties (NOT `#[Fillable]` / `#[Hidden]` attributes)
- **PostgreSQL** (pgsql) in dev and test; test DB: `dental_saas_test`
- **Sanctum** for API auth
- **Spatie/laravel-permission v8.3** (installed, but routes use custom `RoleMiddleware` with `type_user` column)
- **Database-backed** sessions, cache, queue (no Redis)
- **Vite 8** + **Tailwind CSS 4** for frontend (minimal, API-focused)
- **PHPUnit 12**, **Laravel Pint** for formatting

## Architecture

**Multi-tenant via `company_id`.** The `HasCompany` concern (`app/Models/Concerns/HasCompany.php`) applies a global scope and auto-assigns `company_id` on create from the authenticated user or a fallback default company.

```
app/
├── Http/Controllers/Api/   # 12 controllers (Auth, Branch, Dentist, Patient, Procedure, Product, Promotion, Appointment, Report, User, Role, Permission)
├── Http/Middleware/         # RoleMiddleware — gates on type_user column, NOT Spatie hasRole()
├── Http/Requests/{Domain}/ # 33 Form Request classes — all validation lives here
├── Http/Resources/          # 13 API Resource classes
├── Models/                  # 12 models (User, Branch, Dentist, Patient, Procedure, DentistProcedure, Appointment, Schedule, Invoice, Product, Promotion, Company)
├── Models/Concerns/         # HasCompany trait (multi-tenant global scope)
├── Services/                # 13 service classes extending abstract Service
bootstrap/app.php            # Routing, middleware aliases, exception handling
routes/api.php               # Full CRUD with role-based groups (Administrator, Dentist, Patient)
routes/web.php               # GET / (welcome view)
```

## Routing & Auth

- API routes in `routes/api.php` registered via `->withRouting(api: ...)` in `bootstrap/app.php`
- Public endpoints: `POST /auth/login/patient`, `POST /auth/login/staff`
- Authenticated endpoints under `auth:sanctum`
- Three role-based prefixes:
  - `admin/` — `type_user: Administrator` (branches, dentists, patients, procedures, appointments, products, promotions, reports)
  - `staff/` — `type_user: Administrator,Dentist` (select endpoints, appointment form data/slots)
  - `dentist/` — `type_user: Dentist` (schedule, my appointments)
  - `patient/` — `type_user: Patient` (me, appointments, cancel)

## Code Style

- **`$fillable` / `$hidden` properties** on models (not `#[Fillable]` / `#[Hidden]` attributes)
- `casts()` method (not `$casts` property)
- **Form Request classes** for all validation — never `$request->validate([...])` inline
- **Service layer** — controllers delegate to Service classes; base `Service` provides `all()`, `find()`, `create()`, `saveOrUpdate()`, `beforeSave()` hook
- **Spanish domain language** — table/column names in Spanish (sucursales/branches, odontólogos/dentists, pacientes/patients, procedimientos/procedures, citas/appointments)
- **snake_case** for DB columns and migration method names; **PascalCase** for classes; **camelCase** for methods/properties
- Return types on all methods; avoid docblocks where type hints suffice
- Migration format: `return new class extends Migration` with typed `up(): void` / `down(): void`

## Database

- **FK naming inconsistency**: `users` table uses `id_user` (referenced in dentists, patients); other tables use `{table}_id` pattern (`branch_id`, `patient_id`, etc.)
- Many tables have `state` column defaulting to `'Activo'`
- Soft deletes on products (`softDeletes()`)
- Appointment states include `'Activo'`, `'Recordado'`, `'Eliminado'`; scopes `active()` and `notDeleted()` available

## Testing

- **PHPUnit 12** — tests use PostgreSQL; env configured in `phpunit.xml` (array cache/queue/mail, sync queue, pgsql to `dental_saas_test`)
- Place tests in `tests/Feature/` or `tests/Unit/`; extend `Tests\TestCase`
- No external services needed

## Key Conventions

- New models: create migration (anonymous class), model with `$fillable`, `casts()`, `HasCompany` trait if tenant-scoped, factory, service, controller, form request, resource, route
- New controller method with validation: create Form Request in `app/Http/Requests/{Domain}/`, inject as typed parameter, use `$request->validated()`
- New migration: follow FK naming convention of the target table (check `id_user` vs `{table}_id`)
- Run `./vendor/bin/pint` before committing
