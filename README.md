# Laravel Permit

Laravel Permit is an authorization and ACL package for laravel. Its fast and more customizable.
You can easily handle role based ACL or specific user wise permission. So, Lets start a journey with Laravel Permit.

## Installation

You can start it from composer. Go to your terminal and run this command from your project root directory.

```shell
composer require nahid/permit
```

Wait for a while, its download all dependencies.

## Configurations

After complete installation then you have to configure it. First copy these line paste it in `config/app.php` where `providers` array are exists.

```php
Nahid\Permit\PermitServiceProvider::class,
```

and add the line for facade support

```php
'Permit'    => Nahid\Permit\Facades\Permit::class,
```

hmm, Now you have to run this command to publish necessary files.

```shell
php artisan vendor:publish --provider=Nahid\Permit\PermitServiceProvider
```

and then go to `config/permit.php` and edit with your desire credentials.

```php

return [
    'users' => [
        'model' => \App\User::class,
        'table' => 'users',
        'role_column'   => 'type'
    ],

    'super_user'    =>  'admin',

    'abilities'   => [
       // "module"  => ['ability1', 'ability2', 'ability3'=>'policy_module.policy'],
    ],


    'policies'  => [
        /*'module' => [
            'update'    => '\App\Permit\Policies\PostPolicy@update',
        ],*/
    ],



    'roles' => [
        /*'role_name' => [
            'module.ability',
        ],*/
    ]
];
```

Now run this command for migrations

```shell
php artisan migrate
```

You are all most done, just add this trait `Nahid\Permit\Users\Permitable` in you `User` model. Example

```php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Nahid\Permit\Users\Permitable;

class User extends Model
{
    use Permitable;
}
```

Yeh, its done.

## How does it work?

Its a common question. But first you have to learn about our database architecture.
When you run migrate command then we create a table 'permissions' with field 'role_name' and 'permission', and
add two column 'role' and 'permissions' in `users` table. `role` column store users role and `permissions` column store user specific controls.
Here `role` column has a relation with `permissions.role_name` column with its controls. `permissions.permission` handle role based control.

We store permissions as JSON format with specific service and abilities.

```json
{
    "user": {
        "create": true,
        "update": true
    },
    "post": {
        "create": false,
        "update":"\\App\\Permit\\Policies\\PostPolicy@update",
        "delete": false
    }
}
```

Here `user` and `post` is a service/module name and `create`, `update` and `delete` or others are abilities.

### Set User Role

##### Syntax

`bool Permit::setUserRole(int $user_id, string $role_name)`

##### Example

```php
Permit::setUserRole(1, 'admin');
```

### Set User Permission

##### Syntax

`bool Permit::setUserPermissions(int $user_id, string $module, array $abilities)`

##### Example

```php
Permit::setUserPermissions(1, 'post', ['create'=>true, 'update'=>true]);
```


### Set Role Permission

##### Syntax

`bool Permit::setRolePermissions(string $role_name, string $module, array $abilities)`

##### Example

```php
Permit::setRolePermissions('admin', 'post', ['create'=>true, 'update'=>true]);
```


## How to Authorize an event?

### Check user ability

```php
$user = User::find(1);

if (Permit::userCan($user, 'post:create')) {
    //do something
}
```
In `post:create` is an event with module/service. Here `post` is a module and `create` is an ability.

So if the user is authorized with post create event then the will be user passed.

`Permit::userCan()` method return boolean. If you want to throw Unauthorized exception you may use

`Permit::userAllows()` with same parameters.

### Check user role ability

```php
$user = User::find(1);

if (Permit::roleCan($user, 'post:create')) {
    //do something
}
```

Here when given users role allowed this event then its passed. Here is a similar method for throw exception

`Permit::roleAllows()`

### User ability

You can check user ability from user or user role. Here we check both(user and role) permissions but if user specific permission is set then its priority first.

```php
$user = User::find(1);

if (Permit::can($user, 'post:create')) {
    //do something
}
```

and here is a alternate method for throw exception

`Permit::allows()`


### Helper functions

Here you can use helper function instead of facades.

#### user_can()

You can use `user_can()` instead of `Permit::userCan()`

#### user_allows()

You can use `user_allows()` instead of `Permit::userAllows()`

#### role_can()

You can use `role_can()` instead of `Permit::roleCan()`

#### role_allows()

You can use `role_allows()` instead of `Permit::roleAllows()`

#### can_do()

You can use `can_do()` instead of `Permit::can()`

#### allows()

You can use `allows()` instead of `Permit::allows()


## Blade Directive

Sometimes you may want to use this functionalities in you view. Permit comes with all blade directives.


#### Example

```
@userCan($user, 'post:create')
    <a href="#">Link</a>
@endUserCan
```

You can also use else directive

```
@userCan($user, 'post:create')
    <a href="#">Link</a>
@elseDo
    <a href="#">Link 2</a>
@endUserCan
```

#### List of directives

- `@userCan()`
- `@endUserCan()`
- `@roleCan()`
- `@endRoleCan()`
- `@allows()`
- `@endAllows()`
- `@elseDo()`

If you have any kind of query, please feel free to share with me

Thank you
