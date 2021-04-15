jp3cki/uuid
===========

[![Latest Stable Version](https://poser.pugx.org/jp3cki/uuid/v)](//packagist.org/packages/jp3cki/uuid)
[![License](https://poser.pugx.org/jp3cki/uuid/license)](//packagist.org/packages/jp3cki/uuid)

The simple UUID implementation.

Requirements
------------

* PHP ≥ 7.0
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

### Random-based UUID (version 4, aka GUID)

```php
<?php

require_once(__DIR__ . '/vendor/autoload.php'); // composer autoloader

use jp3cki\uuid\Uuid;

echo Uuid::v4() . "\n"; // outputs: 4c9d5550-f58e-4259-ba00-5e59b15895a0
```

### Hash-based UUID (version 3 or 5)

```php
<?php

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

- v2.0.0
  - All public classes are marked as `final`. You are not permitted to inherit classes from this library.

  - The parameter type is now explicitly specified.
    I believe most users will not be affected by this, but you may get unexpected results if you call it using a
    variable of the wrong type.

- v1.0.0
  - Changed minimum requirement to PHP 7.0
