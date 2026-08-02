# Introduction

RESTful API for managing roles, permissions, and users in a dental SaaS platform. Uses Laravel Sanctum for authentication and Spatie Laravel Permission for role-based access control (RBAC).

<aside>
    <strong>Base URL</strong>: <code>http://localhost</code>
</aside>

    This API manages authentication, roles, permissions, and user assignments for the Dentix dental SaaS platform.

    ## Authentication
    Most endpoints require a Bearer token. Obtain one via `POST /api/auth/login` and include it in the `Authorization` header:
    ```
    Authorization: Bearer YOUR_TOKEN_HERE
    ```

    ## Authorization
    Roles and permissions use Spatie Laravel Permission. The seeder creates two default roles:
    - **Administrador** — all permissions
    - **Editor** — read-only permissions (list roles, permissions, users)

