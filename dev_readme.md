#Development readme

Testing deployment instructions 
```
# clone and initialize dependencies for a new instance of project
git clone https://github.com/jkopstals/dingo-api.git
cd dingo-api
composer install

# set up dev environment with sqlite databases for build and testing
cp .env.example .env
touch database/database.sqlite
touch database/testing.sqlite
php artisan migrate --seed
php artisan migrate --database=sqlite_testing --seed

# run tests to verify project
./vendor/bin/phpunit

# run a simple test server (on http://localhost:8000/api)
php artisan serve

# generate new API manual revision
php artisan api:docs --name="JK Laravel/Dingo API manual" --output-file=api_manual.md
```
