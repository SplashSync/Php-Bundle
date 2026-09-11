[![N|Solid](https://github.com/SplashSync/Php-Core/raw/3.0/img/github.jpg)](https://www.splashsync.com)

# Splash Php-Bundle

Symfony integration of [Splash Sync](https://www.splashsync.com): hosts one or many
**connectors**, exposes them to the Splash servers (SOAP endpoint) and to the application
(actions, configuration forms, widgets), and routes every event between them.

```bash
composer require splash/php-bundle
```

## Documentation

- [Connector lifecycle](docs/01-connector-lifecycle.md) — `ConnectorInterface`, `AbstractConnector`, `ConnectorsManager`, request flow, events
- [Configuration](docs/02-configuration.md) — `splash:` bundle configuration, runtime configuration, secrets & environment
- [Configuration forms](docs/03-configuration-forms.md) — allowed form types & options, `KeyValueType`
- [Objects, widgets & standalone services](docs/04-objects-widgets.md) — `$objectsMap`, standalone attributes, scopes, files, tracking
- [Actions & routes](docs/05-actions-routes.md) — soap / master / public / secured routes, profile

AI coding agents: see `llms.txt`, and the `splash-connector` skill (`splash/skills`).

## Related packages

| Package | Purpose |
|---|---|
| `splash/phpcore` | Objects, fields, helpers, SOAP protocol |
| `splash/metadata` | Map API models / entities to Splash fields with PHP 8 attributes |
| `splash/scopes` | Standard field templates & feature scopes |
| `splash/openapi` | REST API clients (JSON, JSON-LD, HAL) |
| `splash/oauth2` | Stateless OAuth2 flows for connectors |
| `splash/bridge-builder` / `splash/bridge-bundle` | Compile & host connectors as isolated `.splx` workers |
| `splash/toolkit` / `splash/validator` | Development sandbox & conformance test suite |

## License

MIT - See [LICENSE](src/LICENSE)
