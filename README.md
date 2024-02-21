# dev-ocean-test

<h3>Project deployment: </h3>

Clone from repository: <br>
``
git clone https://github.com/yevgenii-v/dev-ocean-test.git
``

Create .env: <br>
``
cp .env.example .env
`` <br>
Set mysql settings, admin and user accounts (for seeder)

Create docker-compose.override.yml: <br>
``
cp docker-compose.override.yml.dist docker-compose.override.yml
`` <br>

Run docker containers: <br>
``
docker network create dev_ocean_networks
`` <br>
``
docker-compose build
`` <br>
``
docker-compose up -d
`` <br>

Inside docker container: <br>
``
composer install
``

Run migration: <br>
``
php artisan migrate
``

Laravel Passport Installation: <br>
``
php artisan passport:install
``
<br> [And set values to .env file](https://laravel.com/docs/10.x/passport#creating-a-personal-access-client)

Run factories and seeders: <br>
``
php artisan db:seed
`` <br>
