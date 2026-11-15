# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project

`jp3cki/uuid` — a PHP library that implements UUIDv1/3/4/5/6/7/8 (v6–v8 follow draft-ietf-uuidrev-rfc4122bis-03) and ULID. Requires 64-bit PHP ≥ 8.3.

## Common commands

All build/test workflow goes through the `Makefile`, which bootstraps `composer.phar` and `vendor/` on demand.

- `make setup` — download `composer.phar` (if missing) and run `composer install`.
- `make test` — run the full PHPUnit suite.
- `make check-style` — run PHPCS (`src` + `test`, JP3CKI ruleset via `.phpcs.xml`) and PHPStan (level `max`, configured in `phpstan.neon`).
- `make fix-style` — apply PHPCBF auto-fixes to `src` and `test`.
- `make clean` — remove `composer.lock`, `composer.phar`, `coverage/`, and `vendor/`.

Run a single test (after `make setup`):

```
vendor/bin/phpunit --filter UuidTest
vendor/bin/phpunit --filter testV7 test/UuidTest.php
```

CI (`.github/workflows/ci.yml`) runs syntax check, PHPCS, PHPStan, and PHPUnit against PHP 8.3–8.6; the style/static checks pin to 8.3.

## Architecture

PSR-4 autoload: `jp3cki\uuid\` → `src/`, `jp3cki\uuid\test\` → `test/`. All public classes are `final` (no inheritance allowed; this is a deliberate v2.0 breaking change).

### Public surface (`src/`)

- `Uuid` — single immutable value class that holds the 16-octet binary in `$binary` and exposes one static factory per version (`v1`, `v3`, `v4`, `v5`, `v6`, `v7`, `v8`, `sha256`, plus `nil` / `max` / `fromString`). Every factory builds the raw bytes, then calls the private `fix(int $version)` to stamp the 4-bit version nibble at offset 6 and the 2-bit variant bits at offset 8 — that single helper is what guarantees output conforms to the RFC layout regardless of how the bytes were generated. `formatAsString` / `formatAsUri` render hyphenated and `urn:uuid:` forms; `getVersion` decodes the version nibble; `isValid` checks both the version nibble and the variant bits at offset 8 (accepting RFC 4122 `0b10xx` and Microsoft GUID `0b110x`; nil and max are only valid when fully zero / fully `0xff`).
- `NS` — namespace constants and factory methods. Beyond the classic DNS/URL/OID/X.500 namespaces it also exposes the draft's per-hash "hash space" IDs via `NS::hashSpace($algo)`, which `Uuid::sha256()` uses to seed v8 hash-based UUIDs.
- `Mac` — accepts MAC addresses in many textual formats (colon, hyphen, dotted, bare hex, 6-byte raw) via `Mac::fromString`. When constructed without an address, `Mac` itself generates a random locally-administered unicast address (sets bit 0x02, clears 0x01 in the first octet) — a valid MAC, but **not** a valid random UUID node ID. `Uuid::v1` / `Uuid::v6` therefore use `Mac` only for an explicitly given address; when the MAC is omitted (`null` / `''`) they build the node via the private `Uuid::nodeId()`, which sets the multicast bit (0x01) as RFC 9562 §6.10 requires. Don't route the omitted-MAC case back through `new Mac()`.
- `Ulid` — 128-bit ULID: 48-bit millisecond timestamp + 80 random bits. The timestamp half is encoded with the internal `CrockfordBase32::encodeIntegerLower` helper; the random half is encoded with `paragonie/constant_time_encoding`'s standard Base32 and then remapped to Crockford via `CrockfordBase32::fromStandardBase32`.
- `Exception` — the library's own exception type, thrown for invalid input and missing randomness.

### Internals (`src/internal/`, not part of public API)

- `Random` — single chokepoint for randomness; wraps PHP 8.2's `Random\Randomizer` with the `Secure` engine and re-throws random-source failures as `jp3cki\uuid\Exception`. `Random::bytes(int)` returns raw octets; `Random::int($min, $max)` returns a uniform integer (an invalid range propagates as `ValueError`). All UUID/ULID/MAC generators and the `Timestamp` fallbacks go through this — don't call `random_int()` / `random_bytes()` directly.
- `Timestamp` — provides both v1 (100ns ticks since 1582-10-15 UTC) and ULID (ms since Unix epoch) timestamps, each with a `microtime`-based primary path and a `time()`-based fallback. `Uuid::v1Timestamp` adds the `-12219292800` offset to convert Unix → UUIDv1 epoch.
- `CrockfordBase32` — used only by `Ulid`. `encodeIntegerLower/Upper` encode a non-negative int; `fromStandardBase32` rewrites a standard Base32 string into Crockford's alphabet.

### UUIDv7 layout

`Uuid::v7()` packs the 48-bit ms timestamp via `pack('J', …)` and takes its bottom 6 bytes (`substr(..., -6)`); the remaining 10 bytes (covering both `rand_a` and `rand_b`) are filled in a single `Random::bytes()` call, and `fix(7)` then stamps the version nibble and variant bits. This relies on a 64-bit PHP build, also enforced via `composer.json` (`php-64bit`).

This deliberately gives up strict intra-millisecond monotonicity — RFC 9562 §5.7 permits this — in exchange for removing the previous process-wide counter, which leaked request-rate / process-identity correlation and would wrap after 4096 IDs in the same millisecond. Sortability is preserved at millisecond granularity. If you need strict intra-ms ordering, use `Uuid::v6()` or carry an explicit sequence column.

## Conventions

- Strict types everywhere (`declare(strict_types=1);`) — PHPStan runs at `level: max`, so keep type hints precise (including phpstan-only annotations like `@param int<1, max>` and `non-negative-int`).
- The PHPCS ruleset is `JP3CKI` (from `jp3cki/coding-standard`); rely on `make fix-style` rather than reformatting by hand.
- `composer.json` is normalized by `ergebnis/composer-normalize` — don't reorder keys manually.
