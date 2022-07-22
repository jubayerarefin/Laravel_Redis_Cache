## About this project

- This project fetches data from RDBMS and Caches in Redis for a certain amount of time.
- It uses a Sorted Set to store the data in Redis.
- It uses a custom Laravel LengthAwarePaginator for fetch only the rows for a single page at a time.
- It uses a custom Blade directive to display the data in the view.
- It has 2 filtering options and when the filtering combinations are changed, existing cache is cleared. Fresh data is fetched from the RDBMS and cached in Redis.
- Queries to the RDBMS are under 250ms.
 
# Installation
- Assuming a database is already created in PostgreSQL and is running at 5432 port.
- Assuming a Redis server is already running at port 6379.
- A working copy of the configuration is provided in the `.env.example` file
- Install composer dependencies by `composer install`.
- Run `php artisan telescope:install`
- Run `npm install && npm run build`
- Run `php artisan serve --port=80`
- Open http://localhost:80/
- Open http://localhost:80/telescope/
