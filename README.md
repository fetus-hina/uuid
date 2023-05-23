jp3cki/uuid
===========

[![Latest Stable Version](https://poser.pugx.org/jp3cki/uuid/v)](//packagist.org/packages/jp3cki/uuid)
[![License](https://poser.pugx.org/jp3cki/uuid/license)](//packagist.org/packages/jp3cki/uuid)

A simple UUID implementation.<br>
UUIDv6, v7 and v8 are implemented based on the [draft](https://www.ietf.org/archive/id/draft-ietf-uuidrev-rfc4122bis-03.txt).

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

### Timestamp-based UUID (version 1, 6, 7)

```php
<?php

declare(strict_types=1);

use jp3cki\uuid\Uuid;

require_once(__DIR__ . '/vendor/autoload.php'); // composer autoloader

echo Uuid::v1() . "\n";                    // outputs: 171f5526-f910-11ed-88b6-ea8c2b49d6b3
echo Uuid::v1('08:00:2b:01:02:03') . "\n"; // outputs: 171f6804-f910-11ed-bae3-08002b010203

// Sortable version of UUIDv1
echo Uuid::v6() . "\n";                    // outputs: 1edf9101-71f6-69a8-9474-0ea6f6ebdfa1
echo Uuid::v6('08:00:2b:01:02:03') . "\n"; // outputs: 1edf9101-71f6-6a7a-a7fb-08002b010203

// Sortable and no host information
echo Uuid::v7() . "\n";                    // outputs: 01884666-7e1c-7cc2-a6e0-34adc6d76b52
```

### Random-based UUID (version 4, aka GUID)

```php
<?php

declare(strict_types=1);

use jp3cki\uuid\Uuid;

require_once(__DIR__ . '/vendor/autoload.php'); // composer autoloader

echo Uuid::v4() . "\n"; // outputs: 4c9d5550-f58e-4259-ba00-5e59b15895a0
```

### Hash-based UUID (version 3 or 5)

```php
<?php

declare(strict_types=1);

use jp3cki\uuid\Uuid;
use jp3cki\uuid\NS as UuidNS;

require_once(__DIR__ . '/vendor/autoload.php');

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


### Hash-based UUID (version 8, sha2-256)

```php
<?php

declare(strict_types=1);

use jp3cki\uuid\Uuid;
use jp3cki\uuid\NS as UuidNS;

require_once(__DIR__ . '/vendor/autoload.php');

echo Uuid::sha256(UuidNS::dns(), 'www.example.com') . "\n"; // output: 401835fd-a627-870a-873f-ed73f2bc5b2c

```


### User-defined format UUID (version 8)

```php
<?php

declare(strict_types=1);

use jp3cki\uuid\Uuid;

require_once(__DIR__ . '/vendor/autoload.php');

// Returns an arbitrary 128-bit (16-octet) binary, adjusted to match UUIDv8.
echo Uuid::v8(str_repeat(chr(0x00), 16)) . "\n"; // output: 00000000-0000-8000-8000-000000000000

// You can also give a random number sequence, but you should use UUIDv4.
echo Uuid::v8(random_bytes(16)) . "\n";          // output: bfc47fb7-948f-8833-87e0-cae07c85d30d
echo Uuid::v4() . "\n";

```


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
