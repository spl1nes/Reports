# Reports

The Reports repository is a custom company report environment for internal purposes only. The purpose is to visualizes key sales and cost figures in simple tables and charts.

This implementation will not work in any other environment as the intended company.

# Server Requirements

The server requirements are as follows:

1. 256MB RAM
2. 50MB free hard drive space
3. PHP >= 7.1
4. Apache2

## Required PHP modules & drivers

1. SQLSRV 4.0
2. ODBC 11 Driver

# Setup

## Installation

1. Download the Reports content to your web directory (e.g. /var/www/html/Reports). 
2. Download the [Models](https://github.com/Orange-Management/Model) from Orange-Management into the same directory as the content of the Reports (e.g. /var/www/html/Reports).
3. Download the [jsOMS](https://github.com/Orange-Management/jsOMS) framework from Orange-Management into the same directory as the content of the Reports (e.g. /var/www/html/Reports).
4. Download the [phpOMS](https://github.com/Orange-Management/phpOMS) framework from Orange-Management into the parent directory (e.g. /var/www/html/).
5. Create a caching directory (e.g. /var/www/html/cache).
6. Enable and allow mod_rewrite.

## Configuration

1. Set the connection credentials for the database.
2. Set the page root if Reports is located in a subdirectory of your web directeory (e.g. /Reports/).
3. Set the start month of the fiscal year.
