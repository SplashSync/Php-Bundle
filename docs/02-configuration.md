# Configuration

## Bundle configuration (`config/packages/splash.yaml`)

```yaml
splash:
    ############################################################################
    # Connections: one entry per Splash server (webservice)
    connections:
        acme_prod:                              # local connection id (serverId)
            id:             "<WsIdentifier>"    # REQUIRED: Splash server identifier
            key:            "<WsEncryptionKey>" # REQUIRED: Splash server encryption key
            name:           "Acme - Production" # REQUIRED: display name (Validator "sequence")
            connector:      acme                # connector profile name (default: standalone)
            server_host:    ~                   # override the auto-detected hostname of this server
            host:           https://www.splashsync.com/ws/soap   # expert: Splash server url
            config:                             # connector configuration array
                ApiKey:     '%env(resolve:ACME_API_KEY)%'

    ############################################################################
    # Roles allowed to see Splash notifications in the application
    notify:         [ROLE_ADMIN, ROLE_SUPER_ADMIN, ROLE_ADMINISTRATION_ACCESS]

    ############################################################################
    # Cache of the connectors configurations
    cache:
        enabled:    true
        lifetime:   ~                           # seconds, null = default pool lifetime

    ############################################################################
    # Company informations reported to Splash (informations())
    infos:
        company:    "Acme Corp"
        address:    "1 Main Street"
        zip:        "75000"
        town:       "Paris"
        country:    "France"
        www:        "www.acme.com"
        email:      "contact@acme.com"
        phone:      "+33 1 00 00 00 00"
        ico:        ~                           # path to icon
        logo:       ~                           # path to logo

    ############################################################################
    # Validator overrides (see splash/validator docs)
    test:           {}
```

Rules:
- `connector` is the profile `name` of the connector (`getProfile()['name']`); a bridged worker
  is addressed as `<version>@<name>` (e.g. `3.0@acme`).
- `config` is passed as is to `configure()`. Secrets injected with `%env()%` work in a **native**
  host only: a `.splx` worker compiles its own container and cannot see the host parameters — its
  connector must read secrets from the process environment at runtime.
- Every connection is a test **sequence** for the Validator: keep a sandbox connection next to
  the real ones so the suite can run without credentials.

## Connector runtime configuration

The connector configuration is an array persisted by the host and injected by
`configure(string $type, string $webserviceId, array $configuration)`.

```php
$apiKey = $this->getParameter("ApiKey");               // read, with optional default & domain
$this->setParameter("ListsIndex", $lists);              // write in memory
$this->updateConfiguration();                           // persist: UpdateConfigurationEvent
```

- `getConfiguration()` returns the whole array; `isConfigured()` tells whether `configure()` ran.
- `updateConfiguration()` is synchronous and host-agnostic: the bundle stores the configuration
  (cache pool when `splash.cache.enabled`), a bridge host forwards it to its own storage. Call it
  once per logical change, not per key.
- Keep configuration keys in a `Dictionary` class of the connector; never scatter string literals.
- Do not store what can be recomputed: remote catalogs (lists, attributes) are fine as caches
  refreshed by `connect()`, but treat them as such (tolerate absence).

## Environment & secrets

| Need | Where | Why |
|---|---|---|
| Customer credentials (API key, private app) | Connector configuration (form fields) | Per connection |
| Shared application credentials (OAuth2 client of the connector) | **Process environment** read at runtime (`$_SERVER`, `$_ENV`, `getenv()`) | Must exist in a compiled worker, which only inherits the host process environment |
| `APP_SECRET` | Real environment variable of the host process | Signs OAuth2 states inside workers |

Symfony `.env` files are loaded by Dotenv into the current process only: they are **not** exported
to child processes such as bridge workers. Docker `environment:` and php-fpm `env[]` are.

## Toolkit & tests

In the Toolkit (`splash/toolkit`), `config/packages/splash.yaml` is mounted from the connector
repository and each connection becomes a Validator sequence (`SPLASH_SEQUENCE="Acme - Sandbox"`).
The `test` key holds Validator setting overrides (currencies, languages, fake files...), read
through the Local class `testParameters()`.
