# Symfony Project

This is a Symfony project developed by AronnoDIU.

## Project Description

This project is an Inventory Management System. It allows you to manage products, their quantities, and the locations where they are stored. It also handles purchase events and updates the stock accordingly.

## Technologies Used

- PHP
- Symfony
- Doctrine ORM
- Composer
- Nginx
- MySQL
- Symfony Mailer
- Symfony Messenger
- Symfony Validator
- Symfony Twig
- Symfony Security
- Symfony Form
- Symfony Csp
- Symfony Csrf
- Symfony HttpFoundation

## Setup

To run this project, you need to have PHP and Composer installed on your machine.

1. Clone the repository:
```bash
git clone https://github.com/AronnoDIU/symfony.git
```

2. Install dependencies:
```bash
cd symfony
composer install
```

3. Run the server:
```bash
php bin/console server:run
```

4. Open http://localhost:8000 in your browser

5. Run the tests:
```bash
phpunit
```

6. Run the server with debug mode:
```bash
php bin/console server:run --debug
```

7. Open http://localhost:8000 in your browser
8. Open http://localhost:8000/_profiler in your browser
9. Open http://localhost:8000/_error/{status_code} in your browser
10. Open http://localhost:8000/_wdt/{token} in your browser

## Features

- Manage products, their quantities, and the locations where they are stored
- Handle purchase events and update the stock accordingly
- Send email notifications for low stock products
- Send email notifications for purchase events
- Send SMS notifications for low stock products
- Send SMS notifications for purchase events

## Contributing

To contribute to this project, please follow the [contributing guidelines](https://github.com/symfony/symfony/blob/master/CONTRIBUTING.md).

## License

This project is licensed under the MIT license.