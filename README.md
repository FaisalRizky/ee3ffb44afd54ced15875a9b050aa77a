# PHP Mailer

PHP Based Application to send mail.

## Description

This project is a PHP-based application designed to send mail using various PHP libraries and tools. It leverages the power of PHP 8.2 and several popular libraries to provide a robust and efficient mailing solution.

## Flowchart and ERD
![ERD drawio](https://github.com/user-attachments/assets/69f28386-7fba-48c3-8415-6240876b9e0a)
![Flowchart_ drawio](https://github.com/user-attachments/assets/90618683-ace9-494b-81b4-0465ff3a8859)

## Features

- Environment configuration using `vlucas/phpdotenv`
- Database management with `illuminate/database` and migrations with `robmorgan/phinx`
- Logging using `monolog/monolog`
- RabbitMQ support with `php-amqplib/php-amqplib`
- Routing with `altorouter/altorouter`
- OAuth2 client integration using `league/oauth2-client`
- Validation, HTTP handling, container, and support from the Illuminate library
- UUID generation using `ramsey/uuid`
- Email sending with `phpmailer/phpmailer`
- PostgreSQL for database management
- RabbitMQ for message queuing

## Requirements

- PHP >= 8.2
- Composer

## Installation

1. Clone the repository:

   git clone https://github.com/faisal/php-mailer.git
    ```sh
   cd php-mailer

3. Install dependencies:
   ```sh
   composer install
   
4. Copy the `.env.example` file to `.env` and configure your environment variables:
    ```sh
   cp .env.example .env

4. Start the required services (PostgreSQL, RabbitMQ) using Docker Compose:
   ```sh
   docker-compose up -d

## Autoloading

This project follows PSR-4 autoloading standards. The autoloading configuration can be found in the `composer.json` file

## Scripts

The following scripts are available for managing and running the application:

- **Post-Install and Post-Update Commands:**

  These commands ensure that the `vendor/autoload.php` file is copied after installation or updates.

  "post-install-cmd": [
      "@php -r \"file_exists('vendor/autoload.php') || copy('vendor/autoload.php', 'vendor/autoload.php');\""
  ],
  "post-update-cmd": [
      "@php -r \"file_exists('vendor/autoload.php') || copy('vendor/autoload.php', 'vendor/autoload.php');\""
  ]

- **Migrate:**

  Run database migrations:
  ```sh
  composer migrate

- **Rollback:**

  Rollback the last database migration:
  ```sh
  composer migrate:rollback

- **Start:**

  Start the PHP built-in server:
  ```sh
  composer start

- **Start Consumer:**

  Start the message consumer:
  ```sh
  composer start:consumer

## To Do

- [ ] Run classes as instances
- [ ] Add unit tests for all major components
- [ ] Improve error handling and logging
- [ ] Optimize Docker setup for production
- [ ] Implement rate limiting for email sending
- [ ] Add support for multiple email providers
- [ ] Enhance security with proper input validation and sanitization

## License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.

## Authors

- **Faisal Rizky** - *Initial work* - [Faisal Rizky](mailto:isalriz9@gmail.com)
