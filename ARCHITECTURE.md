# Architecture: uuid

## Purpose

A comprehensive PHP UUID library supporting RFC 4122 versions 1–8, GUID, COMB, and nonstandard UUIDs. Provides generation, parsing, comparison, and serialisation with multiple precision backends to support environments both with and without BCMath/GMP.

## Directory Structure

```
src/
  Uuid.php                   - Primary UUID value object; implements all RFC 4122 operations
  Uuid_Factory.php           - Creates UUIDs (v1/v3/v4/v5/v6/v7/v8) via pluggable generators
  Uuid_Factory_Interface.php - Contract for UUID factories
  Feature_Set.php            - Selects the best available implementations for the current PHP environment
  Rfc4122/                   - RFC 4122-compliant UUID types (v1, v3, v4, v5, v6, v7, v8, Nil, Max)
  Guid/                      - GUID (Microsoft byte-order) variant
  Nonstandard/               - Nonstandard UUIDs and COMB variants
  Lazy/                      - Lazy UUID: defers full parsing until a field is accessed
  Generator/                 - UUID byte generators (time, random, name, COMB, DCE)
  Provider/
    Node/                    - MAC address providers for v1 UUIDs
    Time/                    - Clock providers for v1 UUIDs
    Dce/                     - DCE Security domain providers for v2 UUIDs
  Codec/                     - Encode/decode UUID bytes ↔ string (standard, GUID, ordered-time, COMB)
  Converter/
    Number/                  - Convert UUID byte sequences to/from integer (BCMath, GMP, or degraded)
    Time/                    - Convert timestamps to/from UUID time format
  Validator/                 - String format validation
  Math/                      - Arbitrary-precision arithmetic backends (brick/math, degraded)
  Type/                      - Scalar wrapper types (Decimal, Hexadecimal, Integer, Time)
  Exception/                 - Domain exception hierarchy
  Fields/                    - Per-UUID-version field accessor interfaces
  Builder/                   - Assembles UUID objects from raw bytes via codec + fields
  functions.php              - Global helper functions: uuid1(), uuid3(), uuid4(), etc.
```

## Key Design Decisions

- **Feature detection**: `Feature_Set` auto-selects the best available implementations at runtime (GMP > BCMath > degraded 32-bit fallback), making the library portable across PHP environments.
- **Pluggable generators**: Every randomness/time/node source is an interface, enabling deterministic testing by injecting fixed-value providers.
- **Lazy UUIDs**: `Lazy_Uuid_From_String` defers expensive byte parsing until field access, reducing overhead when UUIDs are created from strings but only compared by value.
- **Codec abstraction**: String encoding is separated from the UUID value object, allowing standard (RFC 4122), GUID (Windows), and sorted (ULIDish) representations without changing the core object.

## Extension Points

- Implement `Uuid_Factory_Interface` to add custom UUID generation strategies.
- Implement `Node_Provider_Interface` to source MAC addresses from a custom provider.
- Implement `Codec_Interface` to add new string representation formats.

## Dependency Flow

```
Uuid::uuid4()
  └─> Uuid_Factory::uuid4()
        └─> Random_Generator (random_bytes())
        └─> Default_Uuid_Builder::build()
              └─> String_Codec::decode()
              └─> Rfc4122\Fields
  └─> returns Rfc4122\Uuid_V4 value object
```
