## Ecommerce API Using Laravel 9

The API contains: 
- 2 User types (Seller, Cusomter).
- Seller store setting.
- Multilingual (Using @spatie /laravel-translatable).
- Cart calcaultion.

## How to run?
- Install docker & docker-compose.
- setup the env file.
- In the terminal inside the project folder:
- run 'sail up -d'.
- run 'sail composer install'.
- run 'sail artisan key:generate'.
- run 'sail artisan storage:link'.
- run 'sail artisan migrate'.
- run 'sail artisan db:seed'
- go to api.php to discover the endpoints.

The default password for all seeded users is 'password'.

## License
The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

Please, give it a star if you find this halpful.
