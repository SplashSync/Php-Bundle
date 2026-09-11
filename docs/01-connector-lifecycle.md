# Connector Lifecycle

`splash/php-bundle` is the Symfony integration of Splash Sync: it hosts one or many
**connectors**, exposes them to the Splash servers (SOAP endpoint) and to the application
(actions, forms, widgets), and routes every event between them.

## What a connector is

A connector is a **Symfony service** implementing `Splash\Bundle\Interfaces\ConnectorInterface`
and tagged `splash.connector` (`ConnectorInterface::TAG`). The interface is the composition of:

| Interface | Responsibility | Key methods |
|---|---|---|
| `ConfigurationInterface` | Runtime configuration of one connection | `configure()`, `isConfigured()`, `getSplashType()`, `getParameter()`, `setParameter()` |
| `AdminInterface` | Health checks & identity | `ping()`, `connect()`, `selfTest()`, `informations()` |
| `ObjectsInterface` | Objects catalog & CRUD | `getAvailableObjects()`, `getObjectDescription()`, `getObjectFields()`, `getObjectList()`, `getObject()`, `setObject()`, `deleteObject()`, `commit()`, `isTrackingConnector()` |
| `WidgetsInterface` | Dashboard widgets | `getAvailableWidgets()`, `getWidgetDescription()`, `getWidgetContents()` |
| `FilesInterface` | Raw files delivery | `getFile()` |
| `ProfileInterface` | Presentation, forms & actions | `getProfile()`, `get*Template()`, `getFormBuilderName()`, `getMasterAction()`, `getPublicActions()`, `getSecuredActions()`, `updateConfiguration()` |

Optional: `PrimaryKeysInterface` (`getObjectIdByPrimary()`), `TrackingInterface` (change tracking
for connectors without webhooks), `Oauth2AwareInterface` (`splash/oauth2`).

`Splash\Bundle\Models\AbstractConnector` implements the generic parts (configuration, logger,
event dispatcher, tracking, scopes, `identify()`, `commit()`, `file()`, `objectIdChanged()`);
a connector extends it and adds the API-specific behaviour, usually through traits:

```php
#[AutoconfigureTag(ConnectorInterface::TAG)]
class AcmeConnector extends AbstractConnector
{
    use GenericObjectMapperTrait;       // objects from static $objectsMap
    use GenericWidgetMapperTrait;       // widgets from static $widgetsMap
    use RoutesBuilderAwareTrait;        // urls of the connector actions

    public function __construct(EventDispatcherInterface $eventDispatcher, LoggerInterface $logger)
    {
        parent::__construct($eventDispatcher, $logger);
    }
}
```

## Connections, connectors & the manager

One connector class serves **many connections** (one per Splash server / webservice id). The
`ConnectorsManager` service holds the connections declared under `splash.connections`
(see [Configuration](02-configuration.md)), knows every registered connector service, and
instantiates a **configured** connector on demand:

- `ConnectorsManager::get($serverId)` → the connector configured for that connection
  (`configure($type, $webserviceId, $configuration)` is called for you);
- `identify($webserviceId)` / `identifyByHost($type, $hostname)` → find the connection a
  request belongs to (SOAP calls carry the webservice id; some actions only know the host);
- `getServersNames()`, `getServerConfiguration()`, `getConnectorConfigurations($connectorName)`
  → introspection used by the Toolkit and the Validator.

Connector names come from `getProfile()['name']`; bridged connectors (see `splash/bridge-bundle`)
are registered under `<version>@<name>` and behave exactly like native ones.

## Request flow

```
Splash server ──SOAP──▶ /splash (SoapController) ──▶ Local class ──▶ ConnectorsManager::get()
                                                                        └─▶ connector->getObject() ...
Application UI ──▶ /{connector}/{webserviceId}/secured/{action} ──▶ ActionsController ──▶ connector action
Third-party API ──▶ /{connector}/{webserviceId}/{action}          ──▶ ActionsController ──▶ connector action
```

The `Local` class (`Splash\Bundle\Local\Local`) bridges `splash/phpcore` (which speaks SOAP and
knows objects, widgets and files providers) with the bundle: every phpcore call is forwarded to
the identified connector.

## Events

Connectors never call the host directly: they dispatch events through the injected dispatcher,
and the bundle (or the bridge host) reacts.

| Event | Dispatched by | Meaning |
|---|---|---|
| `ObjectsCommitEvent` | `$this->commit($objectType, $ids, $action, $user, $comment)` | Objects changed locally: notify the Splash server (create / update / delete) |
| `ObjectsIdChangedEvent` | `$this->objectIdChanged($type, $old, $new)` | A local id changed (e.g. temporary id after creation) |
| `ObjectFileEvent` | `$this->file($path, $md5)` | Ask the host for the raw contents of a file the Splash server holds |
| `UpdateConfigurationEvent` | `$this->updateConfiguration()` | Persist the connector configuration after `setParameter()` |
| `IdentifyServerEvent` / `IdentifyHostEvent` | manager | Resolve which connection a webservice id / hostname belongs to |

Use the `AbstractConnector` helpers rather than dispatching events yourself: they are the
contract shared by the native host and the bridge worker.

## Lifecycle of a call

1. `configure()` — the manager injects the connection configuration (never rely on
   constructor-time configuration: the same service instance is reconfigured per connection).
2. `selfTest()` — validates the configuration without network; every `ping()` / `connect()`
   starts with it.
3. The requested operation runs (`getObject()`, an action...). Errors are reported through
   `Splash::log()->err()` and `null` / `false` return values, never exceptions.
4. `setParameter()` + `updateConfiguration()` persist any state the operation produced
   (tokens, caches of remote lists, cursors).
