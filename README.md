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
php artisan vendor:publish --provider="Nahid\Permit\PermitServiceProvider"
```

and then go to `config/permit.php` and edit with your desire credentials.

```php

return [
    'users' => [
        'model' => \App\Models\User::class,
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

You are all most done, just add this trait `Nahid\Permit\Users\Permitable` in your `User` model. Example

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

We store permissions as JSON format with specific modules and abilities.

```json
{
    "user": {
        "create": true,
        "update": true
    },
    "post": {
        "create": false,
        "update":"\\App\\Permit\\Policies\\PostPolicy@update",
        "delete": true
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

if (Permit::userCan($user, 'post.create')) {
    //do something
}
```
In `post.create` is an event with module/service. Here `post` is a module and `create` is an ability.

So if the user is authorized with post create event then the user will be passed.

`Permit::userCan()` method return boolean. If you want to throw Unauthorized exception you may use

`Permit::userAllows()` with same parameters.

### Check user role ability

```php
$user = User::find(1);

if (Permit::roleCan($user, 'post.create')) {
    //do something
}
```

Here when given users role allowed this event then its passed. Here is a similar method for throw exception

`Permit::roleAllows()`

### Check Users all ability

You can check user ability from user or user role. Here we check both(user and role) permissions but if user specific permission was set then its priority will be first.

```php
$user = User::find(1);

if (Permit::can($user, 'post.create')) {
    //do something
}
```

and here is a alternate method for throw exception

`Permit::allows()`


## Policy

Policy is a feature like laravel native authorization but its quite easy. Permit allows you to manage ACL and Authorization in a same line.
I know your first question is where we use `Policy`?

Lets see an example, suppose you have a user commenting system where every user comment under a blog post and comment owner can edit and deletes their comments.
So you have to apply an authorization system where user can modify his/her own comment. So here we have to implement our custom policy. Take a look

#### Make a policy

First we have to create a class for policy. 

```php
namespace App\Policies;

use App\Comment;
use App\User;

class CommentPolicy
{
    public function update(User $user, Comment $comment)
    {
        return $user->id == $comment->user_id;
    }
}
```

and now map this policy with our config file. Go to `config/permit.php` and update this section in `policies

```php
    ,'policies'  => [
        'comment'  => [
            'update'    => '\App\Policies\CommentPolicy@update'
        ]
    ]
```

Now you have bind this policy with an ability. Suppose we have a module about comment. so this ability will look like in `config/permit.php` `abilities` section

```php
"comment"  => ['create', 'update'=>'comment.update', 'delete'],
```
here `'update'=>'comment.update'` update is an ability and `comment.update` is a policy. This system are bind policy with ability.
so now you can use this policy like a general ability.

You can predefined your all roles permissions in config file. First set your aprox abilities and then assign abilities to roles. Take a look

```php
'abilities'   => [
        "comment"  => ['create', 'update'=>'comment.update', 'delete'],
        "user"  => ['create', 'update', 'delete'],
    ],

    'roles' => [
        'admin' => [
            'post.*',
            'user.*',
        ],

        'user'    => [
            'post.create',
            'post.update',
            'user.create',
            'user.update',
        ]
    ],

    'policies'  => [
        'comment'  => [
            'update'    => '\App\Policies\CommentPolicy@update'
        ]
    ]
```

Here admin and user are role and its value is permissions or abilities. But you can't use this because its not synced with database. so run this command from your terminal

```shell
php artisan permit:sync
```

#### How to use policy based ability

In previous section we are bind `comment.update` policy with an ability and thats are same name. Lets check currently opened comment is authorized for logged in user.

```php
$comment = Comment::find(1);
Permit::allows(auth()->user(), 'comment.update', [$comment]);
```
here first parameter is authorized user, second one is permission and third one is policy method's parameter. we are always automatically bind authenticated user as a first parameter
and then others parameter will pass.

You can use others method like `roleCan`, 'userCan', all helper functions and blade directives as same procedure.

Sometimes you have to check if the given user able to perform for any ability. so we make it easy. lets see

```php
Permit::allows(auth()->user(), ['post.create', 'comment.create']);
```

But if your ability was bind with a policy and its required paramters, then you can pass abilities with associative array.

```php
$comment = Comment::find(1);
Permit::allows(auth()->user(), ['post.create', 'comment.update'=>[$comment], 'comment.create']);
```

Here if the given user is assigned to any one abilities then its allows.

### Commands

We provide several command for make user experience better

### `php artisan permit:sync`

Sync with your composed permissions with database.

### `php artisan permit:set`

Add permission to an user or role

### `php artisan permit:remove`

Remove permissions from an user or role

### `php artisan permit:fetch`

Get permissions of an user or a role

### `php artisan permit:role`

Create a new role

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

#### canDo()

You can use `canDo()` instead of `Permit::can()`

#### allows()

You can use `allows()` instead of `Permit::allows()


## Blade Directives

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
- `@elseUserCan`
- `@endUserCan()`
- `@roleCan()`
- `@elseRoleCan()`
- `@endRoleCan()`
- `@allows()`
- `@endAllows()`
- `@elseAllows()`

If you have any kind of query, please feel free to share with me

Thank you
