# Instructions to be followed

````
Please make seeder for every model
````

# About Supports

- Access (Neputer RBAC)


    - `php artisan access:generate`
This command will generate all the permissions from the routes dynamically with using th patterns given on config **permission.php**

    -  `Neputer\Supports\Access::class`

This trait should be used on Controller which you want to check if the logged-in user has access to the given route.
This is already programmed to check whether the user has access to the route using the patterns we have defined.

    - `Neputer\Supports\Access\HasAccessMiddleware`

A middleware to check the privileges using roles

We have cached all the permissions and optimized the queries. 

- Theme (Neputer Theme)

You can read detail of theme on :
`Neputer\Supports\Theme\README.md`


# Mixins

- Neputer\Supports\Mixins\Responsable

This trait is used strictly for json response. 
A strict structure for the json we will be providing or using throughout the application
whether it's ajax or for an apis.

- Neputer\Supports\Mixins\HasImage

This trait should be used on our model which contains images.
With the help of Image/Intervention package, this mixins contains very useful functionalities like
**Upload image, add watermark if required and generate thumbnail**


