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
composer install && php bin/console doctrine:migrations:migrate
```

(You will need to confirm the migration execution.)

### 7. Generate JWT Keys

Run the following command to create a directory for JWT tokens and generate the necessary keys:

```bash
export JWT_PASSPHRASE=$(openssl rand -base64 32) && echo $JWT_PASSPHRASE
```

This will generate a passphrase and display it. Copy the passphrase and add to `JWT_PASSPHRASE` in the `.env` file.

Run command:
```bash
 php bin/console lexik:jwt:generate-keypair
```

### 8. Exit and reboot the Container

```bash
exit
```
```bash
docker-compose down
```
```bash
docker-compose up -d
```

### 9. Access the Application

The application is available at [http://127.0.0.1:8080](http://127.0.0.1:8080) (or another port specified in
`docker-compose.yml -> nginx -> ports`).

### Available Routes

- **Send SMS with Authorization Code**  
  `POST http://127.0.0.1:8080/api/sms/send/auth`  
  Request Body (JSON):
  ```json
  {
    "phone": "+380660000004"
  }
  ```

- **Validate Authorization Code**  
  `POST http://127.0.0.1:8080/api/auth/verify`  
  Request Body (JSON):
  ```json
  {
    "phone": "+380660000004",
    "code": 958533
  }
  ```

After validating the authorization code, a JWT token will be returned in the response.

## Overview

In this case, Provider 1 is set as the default. In practice, when using multiple providers, they are added to a
credentials table and retrieved from the database through a connection with the user (in this case, it is simulated).

To reduce the load on the database, Redis is used with a code lifespan of 15 minutes. If the code is generated more than
three times, an exception is returned. Caching was chosen as it helps offload the database and simplifies the logic for
clearing codes after they are used. It also facilitates the addition of extra parameters for identifying the legitimacy
of the request, managing the lifespan of the code, and scaling.

Typically, SMS sending is implemented through a queue, but in this case, we can validate the data and, if it is invalid,
refrain from adding unnecessary tasks to the queue.

To prevent spam, a rate limit is used. In this implementation, the limit is one request per minute per route. Requests
are checked by a combination of IP and route name. Device identifiers or other parameters can also be added for improved
identification.

To prevent spam using invalid phone numbers, additional packages can be used alongside the Symfony validator, such as
`misd/phone-number-bundle`.