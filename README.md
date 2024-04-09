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
4. Start the docker containers
    ```shell
    docker compose up -d
    ```
5. Install dependencies
    ```shell
    docker compose exec app composer install
    ```
6. Migrate the database
    ```shell
    docker compose exec app php artisan migrate
    ```
7. Visit `http://localhost`
