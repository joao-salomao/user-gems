## UserGems

Requirements:

-   PHP 8.0.2+
-   Composer 2.4.4+
-   Docker and Docker Compose

TIP: If you are using Linux, take a look at the [asdf project](https://asdf-vm.com/) and the [PHP version manager plugin](https://github.com/asdf-community/asdf-php) to manage your PHP versions.

### Installation

1. Clone this repository
2. Run `composer install` to install the dependencies
3. Run `./vendor/bin/sail up -d` to start the Docker containers
4. Run `./vendor/bin/sail artisan migrate --seed` to migrate and seed the database

### Usage

The application behavior and details can be seen in the [Telescope dashboard](http://localhost/telescope) and the emails sent can be seen in the [Mailhog dashboard](http://localhost:8025).

There are implemented, for the sake of simplicity, the following features:

#### Sync people events from the Calendar API:

This command is scheduled to run every day at 2:00 AM
Command: `./vendor/bin/sail artisan events:synchronize`

#### Send a daily email to the users with the events of the day:

This command is scheduled to run every day at 7:50 AM
Command: `./vendor/bin/sail artisan events:send-daily-meetings-email`
