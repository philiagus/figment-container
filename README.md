# Figment: Container

This is the implementation of the basic figment container, the DI and container implementation of the growing Figment PHP Framework.

## How to define dependencies?

In order to create a figment configuration simply use the `\Philiagus\Figment\Container\Configuration` class and later hand it over to the container.

```php

use Philiagus\Figment\Container\Configuration;
use \Philiagus\Figment\Container\Container;

$config = new Configuration();

$config
    // target my class to be instantiable by the container
    ->instanceClass(MyClass::class)
    // add some injectable data
    ->setContext([
        'someDirectory' => __DIR__ . '/../somewhere',
        'maxSomething' => 123,
        'url' => 'http://example.org'
    ])
    // expose the instance under this name
    ->exposeAs('myObject');

$config
    // define a list of instances that can be injected as a list
    // example: HTTP middlewares, CLI commands, etc...
    ->list(
        // add the defined instance of MyClass to the list
        // the framework is lazy, so you could define this
        // dependency even before defining the MyClass 
        $config->exposedInstance('myObject'),
        // on the fly define OtherClass to be instantiable as well 
        $config->instanceClass(OtherClass::class),
    )
    ->exposeAs('myList')

$container = new Container($config);

$container->instance('myObject'); // will create an instance of MyClass
```

Now lets take a look at MyClass:
```php

use Philiagus\Figment\Container\Contract\Injectable;
use Philiagus\Figment\Container\Contract\Injector;
use Philiagus\Figment\Container\Contract\List\InstanceList;

class MyClass implements
    // any class that wants to be created by the container must implement this interface
    Injectable { 

    private ?InstanceList $myList;

    public function __construct(Injector $injector) {
        $injector
            // configures an exposed list called "myList" to be injected
            // into the private $myList property
            // the property must be type-hinted as nullable due to
            // PHP wanting to set it to null for this by-reference handling
            ->list('myList', $this->myList);    
        // the property is still null at this line!
        // the injection takes place outside any constructors in order to
        // ensure bug-fee behaviour for circular references
    }
}
```

Speaking of circular references, this is completely valid:

```php

use Philiagus\Figment\Container\Configuration;
use Philiagus\Figment\Container\Contract\Injectable;
use Philiagus\Figment\Container\Contract\Injector;

$config = new Configuration();

class CircularMe implements Injectable {
    
    private ?self $me;
    
    public function __construct(Injector $injector) {
        $injector
            // inject exposed object named "circular" into me
            ->instance('circular', $this->me);
    }
}

$config
    ->instanceClass(CircularMe::class)
    ->exposeAs('circular');
```

When requesting `circular` from the container this will create an object of class `CircularMe` that has itself injected into its private `$me`-property.

This just goes to show that you can create circular references without any problems.
