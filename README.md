jp3cki/uuid
===========

[![Latest Stable Version](https://poser.pugx.org/jp3cki/uuid/v)](//packagist.org/packages/jp3cki/uuid)
[![License](https://poser.pugx.org/jp3cki/uuid/license)](//packagist.org/packages/jp3cki/uuid)

The simple UUID implementation.

Requirements
------------

* PHP ≥ 8.0 (64 bit)
* Basic extensions
    - PCRE (preg)
    - Hash


Install
-------

```
$ composer.phar require jp3cki/uuid
```


Usage
-----

### Timestamp-based UUID (version 1)

```php
<?php

declare(strict_types=1);

require_once(__DIR__ . '/vendor/autoload.php'); // composer autoloader

use jp3cki\uuid\Uuid;

echo Uuid::v1() . "\n";                    // outputs: b45d8864-9db7-11eb-9ef1-4e3ad45d3da7
echo Uuid::v1('08:00:2b:01:02:03') . "\n"; // outputs: b45d9c96-9db7-11eb-8ec4-08002b010203
```

### Random-based UUID (version 4, aka GUID)

```php
<?php

declare(strict_types=1);

require_once(__DIR__ . '/vendor/autoload.php'); // composer autoloader

use jp3cki\uuid\Uuid;

echo Uuid::v4() . "\n"; // outputs: 4c9d5550-f58e-4259-ba00-5e59b15895a0
```

### Hash-based UUID (version 3 or 5)

```php
<?php

declare(strict_types=1);

require_once(__DIR__ . '/vendor/autoload.php');

use jp3cki\uuid\Uuid;
use jp3cki\uuid\NS as UuidNS;

// version 3, MD5
echo Uuid::v3(UuidNS::dns(), 'www.example.com') . "\n"; // output: 5df41881-3aed-3515-88a7-2f4a814cf09e

// version 5, SHA-1
echo Uuid::v5(UuidNS::dns(), 'www.example.com') . "\n"; // output: 2ed6657d-e927-568b-95e1-2665a8aea6a2
```

Predefined UUIDs (for "namespace"):

* `jp3cki\uuid\NS::nil()` : `00000000-0000-0000-0000-000000000000`
* `jp3cki\uuid\NS::dns()` : `6ba7b810-9dad-11d1-80b4-00c04fd430c8`
* `jp3cki\uuid\NS::url()` : `6ba7b811-9dad-11d1-80b4-00c04fd430c8`
* `jp3cki\uuid\NS::oid()` : `6ba7b812-9dad-11d1-80b4-00c04fd430c8`
* `jp3cki\uuid\NS::x500()` : `6ba7b814-9dad-11d1-80b4-00c04fd430c8`


License
-------

Under the MIT License.

Please refer the [LICENSE](https://github.com/fetus-hina/uuid/blob/master/LICENSE) file.


Breaking Changes
----------------

- v3.0.0
  - Changed minimum requirement to PHP 8.0.

  - Strict type checking is performed by PHP engine.

- v2.0.0
  - This library no longer works with the 32-bit version of PHP.

  - All public classes are marked as `final`. You are not permitted to inherit classes from this library.

  - The parameter type is now explicitly specified.
    I believe most users will not be affected by this, but you may get unexpected results if you call it using a
    variable of the wrong type.

- v1.0.0
  - Changed minimum requirement to PHP 7.0
