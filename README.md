# Laravel Authorization Plus

Exposes Gate Functionality to a full ACL with user roles, user groups, and resources. Authorization plus empowers you to use subresources to provide fine grained control over access of specific parts of resources.

## Why is this needed?

This extends the authorization functionality of Laravel by allowing you to define fine-grained control of access to resources via a database.  This enables you to control access within the application (allowing an administrative user to disable or enable access to a resource from a UI).

This package also includes support for user roles and user groups to allow you to group access permissions together. 

## Roles versus Groups

In this implementation, a role is a static representation of the user type.  For example, an admin or an end user.  A user can only be assigned one role and this usually does not change.  Roles can indicate different routing and a different user interface while groups usually just determine the access one has to a specific action.

Groups are a collection of permissions that act as a many to many relationship with users.  For example, a user group could distinguish a "support manager" from a "support representative" while both belong to the "admin" role.  Users can belong to multiple groups and groups can have multiple users.


## Installation and Usage

1.  Add AuthPlusServiceProvider to config/app.php.
2.  Publish assets (`php artisan vendor:publish`) and run migrations (`php artisan migrate`).
3.  Use RoleMiddleware to limit routes based on the user role. (See next section)
4.  Use the Controllable Trait in the User Model.
5.  Create groups, assign permissions to groups, assign groups to users.

## Installing and using RoleMiddleware

1.  Adding RoleMiddleware to Route Stack in app/Http/Kernel.php

```php
protected $routeMiddleware = [
   ...
   'roles' => \Devon\AuthPlus\Middleware\RoleMiddleware::class,
];
```

2.  Utilize the middleware in the routes you wish to protect and specify the roles you want the route available to:

```php
Route::group([
    'middleware' => ['auth', 'roles'],
    'roles' => ['admin'],
], 
function() {
    Route::get('/path/only/available/to/admins', 'AdminController@index');
});
```

## How access checking works

1. If a resource and ability are defined:
   1. If access is disallowed, deny access and return.
   2. If access is allowed and:
      1.  No policy exists, grant access.
      2.  Policy exists, fallback to Laravel to evaluate policy.
2. If a resource and ability are not defined, fall back to Laravel's authorization.

This allows you to continue to use Laravel's gates and policies along with Authorization Plus.

## How resources and subresources work

Resources can be any one part string, examples being 'User' or a qualified model like 'App\User'.

Subresources can be multiple parts that relate to the resource, examples being 'address' or 'address.line.1'.  Parts should be separated by periods.

Subresources can be checked against wildcards.  The possibilities (in order of execution) for 'App\User' and 'address.line.1' would be:

```
Resource  |  Subresource
App\User     address.line.1
App\User     address.line.*
App\User     address.line
App\User     address.*
App\User     address
App\User     *
```

This allows fine grained control over any possible resource.

## Checking Access to Resources

Just like Laravel, you can pass the name of the model (or resource) or an instance of the model.

```php
$user = User::first();

Auth::user()->can('create', $user);
// or
Gate::allows('create', $user);
```

## Checking Access to Subresources

When checking access against subresources, the second argument needs to be an array of the resource and subresource.

```php
// The user whose address you are trying to check access permissions to
$user = User::first();

Auth::user()->can('create', [$user, 'address']);
// or
Gate::allows('create', [$user, 'address.line.1']);
```
