## Project Setup

- change env, add JWT_EXPIRES_IN - env.example
- `composer install`
- `php artisan jwt:secret`
- `php artisan migrate --seed`
- `php artisan schedule:run`
- `php artisan serve`
- user: fbt-test@gmail.com/secret

------------

###### API Documentation
- go to [{rooturl}/api/documentation](http://http://localhost:8000/api/documentation "{rooturl}/api/documentation")
- generate swagger script
`php artisan l5-swagger:generate`