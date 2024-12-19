# Figment: Container

This is the implementation of the basic figment container, the DI and container implementation of the growing Figment PHP Framework.

## How to define dependencies?

Everything starts with a configuration class.

```php

use Philiagus\Figment\Container\Configuration;

$config = new Configuration([
    'pdo.dsn' => '<your dsn>',
    'pdo.user' => '<your user>',
    'pdo.pw' => '<your pw>'
]);

$config
    ->constructed(\PDO::class)
    ->parameterConfig('dsn', 'pdo.dsn')
    ->parameterConfig('user', 'pdo.user')
    ->parameterConfig('password', 'pdo.pw')
    ->registerAs('pdo_constructed');

$config
    ->object(new \PDO('<your dsn>', '<your user>', '<your pw>'))
    ->registerAs('pdo_object');

$config
    ->generator(static function(\Philiagus\Figment\Container\Contract\Provider $provider) {
        $context = $provider->context();
        return new new \PDO(
            $context->get('pdo.dsn'),
            $context->get('pdo.user'),
            $context->get('pdo.pw')
        );
    })
    ->registerAs('pdo_generator');

class MyObject {

    public function __construct(
    #[\Philiagus\Figment\Container\Attribute\Inject('pdo_object')] \PDO $pdo
    ) {}
}

```
