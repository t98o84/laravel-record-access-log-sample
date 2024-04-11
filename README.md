# Laravel recode access log sample
Sample of how to record access logs using Laravel's Middleware and Context.

## Installation

1. Clone the repository
2. Change directory
    ```shell
    cd laravel-recode-access-log-sample
    ```
3. Create a `.env` file
   ```shell
   cp .env.example .env
   ```
4. Update the `.env` file 
   ```shell
   ANALYTICS_GTM_ID="GTM-XXXXXXXX"
   ```
5. Start the docker containers
    ```shell
    docker compose up -d
    ```
6. Install dependencies and build assets
    ```shell
    docker compose exec app composer install
    docker compose run --rm node npm install  
    docker compose run --rm node npm run build   
    ```
7. Migrate the database
    ```shell
    docker compose exec app php artisan migrate
    ```
8. Visit `http://localhost`
