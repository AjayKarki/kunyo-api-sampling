# Application Module

#### Installing packages

`composer require laravel/passport`

- [laravel/passport](https://github.com/laravel/passport)

**Process**

- `php artisan migrate`
- `php artisan passport:install`
- Add `HasApiTokens` on User Model
- [Documentation](https://laravel.com/docs/7.x/passport)

**[Test Documentation](https://laravel.com/docs/6.x/testing)**

**Replace testsuites on phpunit.xml with the code given below**
````
   <testsuites>
        <testsuite name="Feature">
            <directory suffix="Test.php">./Modules/**/tests/Feature</directory>
        </testsuite>

        <testsuite name="Unit">
            <directory suffix="Test.php">./Modules/**/tests/Unit</directory>
        </testsuite>
   </testsuites>
````

