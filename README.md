# Symfony SMS Authentication

## Installation Guide

Follow these steps to set up the Symfony SMS Authentication project.

### 1. Clone the Repository

```bash
git clone https://github.com/CarbinQR/symfony_sms_auth.git
```

### 2. Navigate to the Project Directory

```bash
cd symfony_sms_auth
```

### 3. Configure Environment Files

- **Copy the Environment File**

  ```bash
  cp .env.dist .env
  ```

- **Edit the `.env` File**  
  Fill in the database connection details.

- **Copy Application Environment File**

  ```bash
  cp ./app/.env.dist ./app/.env
  ```

- **Edit the `./app/.env` File**  
  Fill in the database and Redis connection details:
    - For `REDIS_URL`, if you haven't changed anything in `docker-compose.yml`, keep it as is. If there are changes:
        - Replace `redis-symfony` with the `container_name` from `docker-compose.yml -> redis`.
        - Replace `6379` with the internal port from `docker-compose.yml -> redis -> ports`.

    - For `DATABASE_URL`:
        - Replace `user` with `MYSQL_USER` from the Docker `.env` file.
        - Replace `password` with `MYSQL_PASSWORD` from the Docker `.env` file.
        - Replace `sms_auth` with `MYSQL_DATABASE` from the Docker `.env` file.
        - Replace `3306` with the internal port from `docker-compose.yml -> mysql -> ports` (if changed).

### 4. Start Docker Containers

```bash
docker-compose up -d
```

### 5. Access the PHP Container

```bash
docker exec -it php-symfony /bin/sh
```

### 6. Install Dependencies and Run Migrations

Within the `php-symfony` container, execute the following commands:

```bash
composer install
php bin/console doctrine:migrations:migrate
```

(You will need to confirm the migration execution.)

### 7. Generate JWT Keys

Run the following command to create a directory for JWT tokens and generate the necessary keys:

```bash
mkdir -p config/jwt && openssl genpkey -out config/jwt/private.pem -algorithm RSA -pkeyopt rsa_keygen_bits:4096 && openssl rsa -pubout -in config/jwt/private.pem -out config/jwt/public.pem
```

### 8. Exit the Container

```bash
exit
```

### 9. Access the Application

The application is available at [http://127.0.0.1:8080](http://127.0.0.1:8080) (or another port specified in `docker-compose.yml -> nginx -> ports`).

### Available Routes

- **Send SMS with Authorization Code**  
  `POST http://127.0.0.1:8080/api/sms/send/auth`  
  Request Body (JSON):
  ```json
  {
    "phone": "+38066******4"
  }
  ```

- **Validate Authorization Code**  
  `POST http://127.0.0.1:8080/api/auth/verify`  
  Request Body (JSON):
  ```json
  {
    "phone": "+38066******4",
    "code": 958533
  }
  ```

After validating the authorization code, a JWT token will be returned in the response.
