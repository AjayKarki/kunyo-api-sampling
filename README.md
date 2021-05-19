<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## About Folder Neputer
This folder contains the important abstract classes, mixins (traits) and supports for the project.
Cannot edit unless you change the place you have used too. Contains :

- Custom Manageable theme, permissions and roles management, custom macros, directives

## About Folder Modules

This module's folder basically contains Module (HMVC architecture),
which will be directly dependent to the Neputer folder.

## About Folder app/Foundation

This foundation is specific for the current projects only. You can edit anything here for the project.
Whether you use Service Layer or Repository patterns , you can decide and set up Foundation module.

If want to start new project, all we have to use Neputer folder and foundation folder 
can be generated using commands. And you can add your custom modules or remove the unwanted one.


### JS utilities

- Router

`router.get('admin.product.show', { 'product': 23 })` will return route with parameters

- Many useful utils like :

`utils.getCsrfToken()` return csrf token
`utils.http.get` for get method ajax
`utils.http.post` for post method ajax
`utils.toast('Hello world', 'info')` for toastr plugin
`utils.sluggify('Hello SLug)` return `hello-slug` ie a url slug utils
