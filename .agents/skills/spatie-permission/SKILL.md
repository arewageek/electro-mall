---
name: spatie-permission
description: Best practices for implementing spatie/laravel-permission for role and permission management in Laravel.
---

# Spatie Laravel-Permission Agent Skill

This skill provides the AI with rules and context for using `spatie/laravel-permission` in this project.

## Core Rules

1. **Permission-Based Authorization & Naming (Crucial)**
   - **DO NOT** use `$user->hasRole('admin')` for business logic or access control. 
   - **ALWAYS** use `$user->can('article.edit')` or the `can:` middleware. 
   - **NAMING CONVENTION:** Always stick to clean architecture standard notation: `resource.action` (e.g. `dashboard.view`, `user.create`, `inventory.update`). Do NOT overcomplicate.

2. **Namespace Standard**
   - **DO NOT** use inline FQCN/namespaces when referencing classes (e.g. `\Spatie\Permission\Models\Role`). 
   - **ALWAYS** import the class at the top of the file via `use` statements. 

3. **Super Admin Pattern**
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

4. **Seeding Best Practices**
   - When writing a database seeder for roles and permissions, you MUST clear the cache first using proper imports to prevent errors:
     ```php
     use Spatie\Permission\Models\Role;
     use Spatie\Permission\Models\Permission;
     use Spatie\Permission\PermissionRegistrar;

     public function run()
     {
         app()[PermissionRegistrar::class]->forgetCachedPermissions();

         // create permissions and roles...
     }
     ```

5. **Middleware Protection**
   - Register the middleware in `bootstrap/app.php` (for Laravel 11+) if you need role-specific middleware, but prefer native `can` middleware:
     ```php
     Route::get('/dashboard', function () {
         //
     })->middleware(['can:dashboard.view']);
     ```

6. **Blade Directives**
   - Use native `@can('article.edit')` for UI rendering logic. Only use `@role('Super Admin')` for showing specific badges or titles.

## Model Setup
Ensure the `User` model uses the `HasRoles` trait properly imported:
```php
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasRoles;
}
```
