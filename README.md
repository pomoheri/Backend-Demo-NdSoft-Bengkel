# Backend Si Bengkel INT


## Requirements software
```
"require": {
        "php": "^8.1",
        "guzzlehttp/guzzle": "^7.2",
        "laravel/framework": "^10.10",
        "laravel/sanctum": "^3.3",
        "laravel/tinker": "^2.8"
    },
    "require-dev": {
        "fakerphp/faker": "^1.9.1",
        "laravel/pint": "^1.0",
        "laravel/sail": "^1.18",
        "mockery/mockery": "^1.4.4",
        "nunomaduro/collision": "^7.0",
        "phpunit/phpunit": "^10.1",
        "spatie/laravel-ignition": "^2.0"
    },
```
## how to install
1. Clone this repository
2. open terminal and run cp .env.example .env
3. set the environment in .env
    - DB_CONNECTION=mysql
    - DB_HOST=127.0.0.1
    - DB_PORT=3306
    - DB_DATABASE=sibengkel
    - DB_USERNAME=root
    - DB_PASSWORD=
4. run composer install
5. run php artisan key:generate
6. run php artisan migrate --seed
7. run development in webserver with php artisan serve

