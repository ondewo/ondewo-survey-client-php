# Release History

*****************

## Release ONDEWO SURVEY PHP Client 2.1.1

### New Features

* [[OND236-120]](https://ondewo.atlassian.net/browse/OND236-120) Keycloak authentication: `Ondewo\Survey\Auth\ClientConfig` gains `keycloakUrl`, `realm`, `clientId`, `username`, `password`, `tokenExpirationInS`, `verifySsl` and `refreshToken` (appended after the existing parameters, `refreshToken` last), validated all-or-nothing and redacted in every rendering. With them set, `channelOptions()` attaches a shared `KeycloakTokenProvider` as `update_metadata`, so every call carries `authorization: Bearer <token>`; without them nothing changes.
* [[OND236-120]](https://ondewo.atlassian.net/browse/OND236-120) Offline-token start-up: with `refreshToken` set the provider starts with one `grant_type=refresh_token` exchange and never sends a password grant. Providers are shared per credential set through a SHA-256-keyed registry that never hands out a closed one; the default transport is curl with a 10 s bound and `verifySsl` scoped to the token request.
* [[OND236-120]](https://ondewo.atlassian.net/browse/OND236-120) Tests: the cross-SDK Keycloak test catalogue (T01-T62, lazy-path form for the loop cases) plus a real socket transport test against a scripted HTTP/HTTPS token endpoint.

### Bug Fixes

* [[OND236-120]](https://ondewo.atlassian.net/browse/OND236-120) Keycloak token refresh: a failed refresh no longer ends the refresh. Every failure kind (a 5xx from a proxy, a DNS or connection failure, a restarting Keycloak, a dead offline session) is retried with full-jitter exponential backoff, 5 s doubling up to 300 s and measured from the moment the failure was recorded; no request is sent while the backoff is pending. PHP has no threads, so the refresh is lazy, on the call that needs the token. A success resets the backoff.
* [[OND236-120]](https://ondewo.atlassian.net/browse/OND236-120) A token that is still valid keeps being served when its refresh fails inside the 30 s expiry leeway; only an EXPIRED token raises, and it raises `KeycloakAuthenticationException` (the cause in `getPrevious()`) whatever failed underneath (transport error, non-2xx status, a body that is not JSON or not a JSON object, a missing `access_token`, a non-numeric `expires_in`), with the original error kept as its cause. A malformed response changes no provider state. Concurrent callers share one in-flight refresh, so an outage costs one token request, not one per call.
* [[OND236-120]](https://ondewo.atlassian.net/browse/OND236-120) A token response without an `access_token` is reported by its field names only, never by its values (a token response is credential material), and a quoted Keycloak error body is cut at 500 characters.
* [[OND236-120]](https://ondewo.atlassian.net/browse/OND236-120) The error for a Keycloak configuration with neither `password` nor `refreshToken` names `refresh_token` as the spec spells it.

### Improvements

* [[OND236-120]](https://ondewo.atlassian.net/browse/OND236-120) The same Keycloak behaviour as every ONDEWO SDK (the python SDKs are the reference, ondewo-survey-client-python 2.1.1 ships the same fixes): the token request is bounded by a 10 s timeout, durations use a monotonic clock, the `tokenExpirationInS` window is armed on both start-up paths, each failed refresh logs one WARNING with the attempt number, the retry delay and how long the current token stays valid, and the first success afterwards one INFO. No credential appears in any log line, error or rendering of the configuration.
* Tracking API Version [2.1.0](https://github.com/ondewo/ondewo-survey-api/releases/tag/2.1.0) ( [Documentation](https://ondewo.github.io/ondewo-survey-api/) ), generated with ondewo-proto-compiler 5.17.0: the generated code is unchanged from 2.1.0

*****************

## Release ONDEWO SURVEY PHP Client 2.1.0

### Improvements

* Tracking API Version [2.1.0](https://github.com/ondewo/ondewo-survey-api/releases/tag/2.1.0) ( [Documentation](https://ondewo.github.io/ondewo-survey-api/) )
* Generated with ondewo-proto-compiler 5.17.0 (protoc 35.1, `grpc_php_plugin` built from grpc v1.85.0). New runtime floors: `google/protobuf` 5.35.1 (a major step from 4.33.6; requires php >= 8.2.0) and `grpc/grpc` 1.85.0.

*****************

## Release ONDEWO SURVEY PHP Client 2.0.1

### Improvements

* Tracking API Version [2.0.0](https://github.com/ondewo/ondewo-survey-api/releases/tag/2.0.0) ( [Documentation](https://ondewo.github.io/ondewo-survey-api/) )
* New `Ondewo\Survey\Auth\ClientConfig` (hand-written, in `auth/`) builds the target and the `$opts` of every
  generated `<Service>Client` for plaintext, TLS and mutual TLS, with the same rules as the other ONDEWO SDKs:
  * `grpcCert`, `grpcClientCert` and `grpcClientKey` take PEM **content**, never a file path; CRLF PEMs work. An
    empty `grpcCert` means the platform's default trust store.
  * `grpcClientCert` / `grpcClientKey` are both-or-neither: half a pair throws `InvalidArgumentException` when the
    config is built and again in `channelCredentials()`, before anything reaches ext-grpc (grpc-core aborts the
    process on a key without its certificate).
  * `useSecureChannel: false` with a client identity throws instead of silently dropping it; a plaintext channel
    logs a warning naming `host:port` on a PSR-3 logger if given, otherwise through `error_log()`.
  * `channelOptions()` refuses a `credentials` option of its own; every other option is passed through.
  * Exception messages name the field and `host:port`, never a PEM or the config. `(string)`, `var_dump()` /
    `print_r()` and `json_encode()` show a non-empty `grpcClientKey` as `***REDACTED***`; `serialize()`,
    `var_export()` and `(array)` are not redacted (documented). The key parameter is `#[\SensitiveParameter]`.
  * `target()` brackets a bare IPv6 literal (`[::1]:50051`) and leaves `[...]` / `scheme:` hosts alone.
  * `ClientConfig::DEFAULT_CHANNEL_OPTIONS` matches ondewo-client-utils-python: message size 2³¹-1 both ways,
    keepalive 30 s with `max_pings_without_data` 2 and no pings without calls, keepalive and HTTP/2 ping timeout
    20 s, reconnect backoff capped at 5 s. Documented gap: no per-method retry policy, only gRPC's transparent
    retries.
* `composer.json` suggests `psr/log` for the insecure-channel warning.
* README section "TLS, mutual TLS and certificates": modes, an openssl test PKI, security notes and troubleshooting.
* `make publish` / the release workflow: Packagist's `202 Accepted` counts as success, and the HTTP-code check no
  longer runs in a separate shell with an empty code (a comment inside the continued shell block split it), which
  failed every publish.
* Proto compiler pinned to [5.15.2](https://github.com/ondewo/ondewo-proto-compiler/releases/tag/5.15.2).

### Tests

* `tests/Auth/ClientConfigTest.php` covers `ClientConfig` (100% coverage of `auth/`).
* `tests/Tls/MutualTlsHandshakeTest.php`: real handshakes with a PKI generated at test time (ext-openssl) against a
  python grpcio server: TLS, mutual TLS, CRLF PEMs and `[::1]` connect; a missing or foreign client certificate, a
  wrong CA and the system roots against the test CA fail as `UNAVAILABLE`. CI installs grpcio 1.82.1.
* `tests/ReleaseNotesTest.php` pins the `RELEASE.md` heading and `*****` separator the GitHub release-notes slice
  relies on.

*****************

## Release ONDEWO SURVEY PHP Client 2.0.0

### New Features

* Initial release of the ONDEWO SURVEY gRPC client for PHP. The whole client surface is generated from the
  [ondewo-survey-api](https://github.com/ondewo/ondewo-survey-api) protocol buffer definitions by the
  `ondewo-php-proto-compiler` image of
  [ondewo-proto-compiler 5.15.1](https://github.com/ondewo/ondewo-proto-compiler/releases/tag/5.15.1),
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
  generated `<Service>Client` needs because it extends `\Grpc\BaseStub`, and — for the JSON wire format only —
  `ext-bcmath`.
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
* The job installs `ext-bcmath` alongside `ext-grpc`: `google/protobuf` only *suggests* bcmath, but its
  pure-PHP JSON parser range-checks every integer with `bccomp()`, so `mergeFromJsonString()` on a message
  with an int field dies without it. The suite covers that path explicitly.
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
