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

After installation complete successfully you have to configure it. First copy these line paste it in `config/app.php` where `providers` array is exists.

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
    "users" => [
        'model' => \App\User::class,
        'table' => 'users'
    ]

];
```