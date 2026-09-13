# Release History

*****************

## Release ONDEWO SURVEY PHP Client 2.0.0

### New Features

* Initial release of the ONDEWO SURVEY gRPC client for PHP. The whole client surface is generated from the
  [ondewo-survey-api](https://github.com/ondewo/ondewo-survey-api) protocol buffer definitions by the
  `ondewo-php-proto-compiler` image of
  [ondewo-proto-compiler 5.15.0](https://github.com/ondewo/ondewo-proto-compiler/releases/tag/5.15.0),
  which is vendored as a git submodule and pinned to that tag: protoc's built-in `--php_out` for the messages
  and enums, `grpc_php_plugin` for the `<Service>Client` stubs, and a composer package whose optimized
  classmap autoloader is built and verified inside the image.
* The generated stubs are **committed** under `src/` — 37 files covering both services the api declares,
  `Ondewo\Survey\SurveysClient` and `Ondewo\Survey\FHIRClient`, their messages and enums, and the
  non-well-known `google/*` protos they import. Survey vendors no other product's protos, so everything this
  package ships lives under `Ondewo\Survey`. Packagist serves the tree of a git tag verbatim and composer has
  no build step, so stubs that are not committed do not exist for anybody who installs the package.
* Ships as the composer package `ondewo/survey-client-php`, installable with
  `composer require ondewo/survey-client-php`. Requires PHP >= 8.1 and the `grpc` PHP extension, which every
  generated `<Service>Client` needs because it extends `\Grpc\BaseStub`.
* Hand-written sources live in `auth/` at the repository root, never in the compiler-owned `src/`.
  `Ondewo\Survey\Auth\BearerTokenAuthenticator` turns a token into the `$opts` array a generated stub is
  constructed with and stamps `authorization: Bearer <token>` onto the metadata of every call. The namespace
  is SURVEY's own rather than a shared `Ondewo\Auth`, so installing this client next to another ONDEWO PHP
  client cannot collide.
* `make build` reproduces the stubs end to end — pinned submodules, compiler image, generation, ownership
  hand-back and version propagation into `composer.json`.

### Testing

* A real PHPUnit suite under `tests/` exercises the generated code rather than asserting around it: every
  committed class is loaded through the autoloader, `initOnce()` is called on every one of the 4 `GPBMetadata`
  descriptors (so a missing transitive import fails CI rather than a consumer's first RPC), messages are
  round-tripped through the binary and JSON wire formats including the nested `Answer.UserInfo`, enum zero
  constants are pinned, and both service stubs are constructed against a dummy channel and checked for the
  RPC methods and arities the api declares.
* Two cases the other ONDEWO clients carry are deliberately absent because ondewo-survey-api has nothing to
  assert them against: it declares no `proto3 optional` field anywhere, and no streaming RPC at all.
* `make coverage` measures the hand-written sources (`phpunit.xml.dist`'s `<source>` is `auth/`) and fails the
  build below 100% line coverage. Generated code is excluded from that metric and covered by the tests above.
* GitHub Actions runs `composer validate`, `php -l`, the suite and the coverage gate on PHP 8.1 and 8.4
  against the committed stubs — no docker image is built and no submodule is checked out there. No step is
  guarded by a directory check, so a tree without code goes red instead of reporting success.
* The dev tool chain (PHPUnit, the coverage gate) lives in its own composer project under `tools/`. It is
  deliberately **not** `require-dev` in the root manifest: `composer update --no-dev` still resolves dev
  requirements, and the compiler image resolves the merged manifest with the network disabled, so one
  `require-dev` entry would break `make generate_ondewo_protos`.

*****************
