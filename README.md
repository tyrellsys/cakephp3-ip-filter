# cakephp3-ip-filter
restrict access by ip address for CakePHP3 Component

## Installation

You can install this plugin into your CakePHP application using [composer](http://getcomposer.org).

The recommended way to install composer packages is:

```
composer require tyrellsys/cakephp3-ip-filter
```

## Configuration

Controller::initialize
```
...
        public function initialize($event)
        {
....
            $this->loadComponent('Tyrellsys/CakePHP3IpFilter.IpFilter', [
                'trustProxy' => true,
                'whitelist' => '192.168.0.0/24' // array OR comma separate value
            ]);
...
```

## Method

- `bool check(string $ip = null)` - returns compare `whitelist`.
- `void checkOrFail(string $ip = null)` - throws \Cake\Http\Exception\ForbiddenException when bad ip address.
