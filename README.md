# Lucky Prize Winner Simulation

This Laravel project simulates a prize winner event, allowing you to configure prizes with varying probabilities and distribute them to participants fairly and transparently.

## Features

- Effortlessly define prizes with names and probabilities.
- Run simulations with a specified number of participants.
- Generate detailed reports on the distribution of prizes awarded, ensuring they align with the configured odds.
- Display the prizes probibility and doughnut chart
- Display the actual awarded probibility and doughnut chart
- Delete prize with the confirmation
- Different types of validtions we have used while creating and updating

## Tech

Projects uses a number of open source projects to work properly:

- [Laravel](https://laravel.com/docs/10.x/installation) - Laravel
- [PHP](https://www.php.net/downloads.php) - PHP
- [MySQL](https://dev.mysql.com/downloads/) - MySQL

## Installation

Project requires [Laravel](https://laravel.com/docs/10.x/installation) v10+ to run.

Install the dependencies and devDependencies and start the server.

```sh
composer install
php artisan key:generate
php artisan migrate
```

Configure the database connection details in your .env file. 
We can refer to env.example for the sample env file.

## Plugins

Project is currently extended with the following plugins.
Instructions on how to use them in your own application are linked below.

| Plugin | README |
| ------ | ------ |
| ChartJS | [https://cdn.jsdelivr.net/npm/chart.js ,https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2] |
| sweetAlert | [https://cdn.jsdelivr.net/npm/sweetalert2@11] |
| GitHub | [plugins/github/README.md] |

#### Building for source

```sh
php artisan serve
```

#### Localhost
```sh
127.0.0.1:8000
```

[Working video](https://shorturl.at/cAGI8)



