#Development readme

Testing deployment instructions 
```
git clone https://github.com/jkopstals/dingo-api.git
cd dingo-api
composer install
cp .env.example .env
touch database/database.sqlite
touch database/testing.sqlite
php artisan migrate --seed
php artisan migrate --database=sqlite_testing --seed
./vendor/bin/phpunit
```

run a simple test server (on http://localhost:8000/api)
```
php artisan serve
```


Generate new API manual
```
php artisan api:docs --name="JK Laravel/Dingo API manual" --output-file=api_manual.md
```
