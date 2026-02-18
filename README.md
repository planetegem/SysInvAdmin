<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>


## About SysInvAdmin

## Setup
### Artisan Commands To Get You Started
1) Run 'php artisan migrate' to create database. I chose an SQlite db, because I was not expecting a lot of concurrent write operations, but you're free to select any type of database you want. Laravel should handle the difference in setup.
2) Run 'php artisan db:seed' to create some defaults. At the moment, these are just languages, which can't be created any other way.
3) Run 'php artisan make:admin {mail} {password}' to create the first admin user, which you can use as login. Once inside, you can use the interface to invite additional users/admins.
4) [TO DO: explain how invitations work]
