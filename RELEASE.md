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

### Publishing

* Published to [Packagist](https://packagist.org/packages/ondewo/survey-client-php) as `ondewo/survey-client-php`.
  Packagist accepts no upload — it serves the tree of a git tag — so `make publish` validates the package and
  then pings `https://packagist.org/api/update-package` with `PACKAGIST_USERNAME` + `PACKAGIST_API_TOKEN` to
  have the new tag crawled. It is wired into `make release` after `push_to_gh`, and
  `make run_release_with_devops` reads both credentials from `account_packagist.env` in the
  `ondewo-devops-accounts` repository. The package still has to be **submitted once by hand**; see README
  "Publishing to Packagist".
* `make packagist_dry_run` is the credential-free half of that path and runs in CI on every push:
  `composer validate`, `composer validate --strict` with the three deliberate warnings enumerated (the
  `version` field and the two exact `google/protobuf` / `grpc/grpc` pins — anything new fails the build), the
  agreement between `ONDEWO_SURVEY_VERSION`, `composer.json`, `RELEASE.md` and the git tag, and the exact update
  payload the real publish POSTs.
* `.github/workflows/release.yml` runs on a bare `X.Y.Z` tag push, re-runs the dry run and the full test
  suite against the tagged tree and only then publishes, using GitHub secrets. A missing secret fails its
  first step with an explicit `::error::` instead of posting an unauthenticated request.

*****************
