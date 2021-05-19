# Usage

- Add an item to a cart
````
Cart::add(
    '5s3Oe', // productID
    'hello', // productName
    '2344', // ProductPrice
    9, // ProductQuantity
    [ 
        'extra' => [ 'description' => 'sachin rais' ]
         // attributes ie extra information [ optional ]
    ]
);
````

- Adding multiple items to a cart

````
Cart::add([
    [
        'id'        => '12',
        'name'      => 'uman',
        'price'     => 123,
        'quantity'  => 1,
    ],
    [
        'id'        => '13',
        'name'      => 'uman',
        'price'     => 123,
        'quantity'  => 1,
    ],    
]);
````

    - If you want to update quantity or any other attributes
     for the item of an cart
    ````
    Cart::add([
        [
            'id'        => '13',
            'name'      => 'uman',
            'price'     => 123,
            'quantity'  => 2,
            'attributes' => [ 'example' => 'Attributes can have anything/info' ]
        ],    
    ]);
    ````
    - We have updated cart with ID 13 with quantity 4 and added extra attributes as well.
    
- Clear Cart

`Cart::clear()`

- Remove an item from Cart
    - Can be removed by product id only meanwhile
    
    `Cart::remove('12')`

- Get Cart

`Cart::get()`

- Get Total 

`Cart::total()`

- USAGE

````
    @foreach(\Neputer\Supports\Cart\Cart::get() as $cart)
    <ul>
        <li>ID : {{ $cart->id }}</li>
        <li>Name : {{ $cart->name }}</li>
        <li>Quantity : {{ $cart->quantity }}</li>
        <li>Price : {{ $cart->price }}</li>
        <li>SubTotal : {{ $cart->subtotal }}</li>
    </ul>
    @endforeach
    <p>Total : {{ \Neputer\Supports\Cart\Cart::total() }}</p>
````

# New helper added to remove namespace for cart

`cart()` // Using this helper, you can access any mentioned above functions.

````
    @foreach(cart()->get() as $cart)
    <ul>
        <li>ID : {{ $cart->id }}</li>
        <li>Name : {{ $cart->name }}</li>
        <li>Quantity : {{ $cart->quantity }}</li>
        <li>Price : {{ $cart->price }}</li>
        <li>SubTotal : {{ $cart->subtotal }}</li>
    </ul>
    @endforeach
    <p>Total : {{ cart()->total() }}</p>
````
