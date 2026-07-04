---
name: spatie-permission
description: Best practices for implementing spatie/laravel-permission for role and permission management in Laravel.
---

# Spatie Laravel-Permission Agent Skill

This skill provides the AI with rules and context for using `spatie/laravel-permission` in this project.

## Core Rules

1. **Permission-Based Authorization (Crucial)**
   - **DO NOT** use `$user->hasRole('admin')` for business logic or access control. 
   - **ALWAYS** use `$user->can('edit articles')` or the `can:` middleware. Roles are just groupings of permissions. By checking permissions directly, the codebase remains flexible if roles change.

2. **Super Admin Pattern**
   - Implement the Super Admin bypass using Laravel's `Gate::before` in your `AppServiceProvider.php`:
     ```php
     use Illuminate\Support\Facades\Gate;

     public function boot(): void
     {
         Gate::before(function ($user, $ability) {
             return $user->hasRole('Super Admin') ? true : null;
         });
     }
     ```

3. **Seeding Best Practices**
   - When writing a database seeder for roles and permissions, you MUST clear the cache first to prevent errors:
     ```php
     use Spatie\Permission\Models\Role;
     use Spatie\Permission\Models\Permission;

     public function run()
     {
         app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

         // create permissions and roles...
     }
     ```

4. **Middleware Protection**
   - Register the middleware in `bootstrap/app.php` (for Laravel 11+) if you need role-specific middleware, but prefer native `can` middleware:
     ```php
     Route::get('/dashboard', function () {
         //
     })->middleware(['can:access dashboard']);
     ```

5. **Blade Directives**
   - Use native `@can('edit articles')` for UI rendering logic. Only use `@role('Super Admin')` for showing specific badges or titles.

## Model Setup
Ensure the `User` model uses the `HasRoles` trait:
```php
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasRoles;
}
```
