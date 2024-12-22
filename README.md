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
    ->parameterContext('dsn', 'pdo.dsn')
    ->parameterContext('user', 'pdo.user')
    ->parameterContext('password', 'pdo.pw')
    ->registerAs('pdo_constructed');

$config
    ->object(new \PDO('<your dsn>', '<your user>', '<your pw>'))
    ->registerAs('pdo_object');

$config
    ->closure(static function(\Philiagus\Figment\Container\Contract\BuilderContainer $provider) {
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
    #[\Philiagus\Figment\Container\Attribute\Instance('pdo_object')] \PDO $pdo
    ) {}
}

```
