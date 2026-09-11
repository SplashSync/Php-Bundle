# Objects, Widgets & Standalone Services

A connector exposes **objects** (data types synchronized with Splash: `ThirdParty`, `Address`,
`Product`, `Order`, `Invoice`...) and **widgets** (dashboard blocks). Two registration styles
exist; both end up behind the same `ObjectsInterface` / `WidgetsInterface`.

## Mapped objects (`GenericObjectMapperTrait`)

The connector lists its object classes in a static map; the trait derives every
`ObjectsInterface` method from it and instantiates the object class with the connector.

```php
class AcmeConnector extends AbstractConnector
{
    use GenericObjectMapperTrait;
    use GenericObjectPrimaryMapperTrait;    // + getObjectIdByPrimary() when objects declare primary fields
    use GenericWidgetMapperTrait;

    protected static array $objectsMap = array(
        "ThirdParty" => Objects\ThirdParty::class,
        "Address" => Objects\Address::class,
    );

    protected static array $widgetsMap = array(
        "SelfTest" => Widgets\SelfTest::class,
    );
}
```

Object classes extend `Splash\Core\Models\Objects\AbstractObject` (or a richer base such as
`Splash\OpenApi\Models\Objects\AbstractRestAndMetadataObject` for REST + metadata connectors)
and receive the configured connector in their constructor. An object class that does not exist
or does not extend `AbstractObject` is silently removed from the map.

Widgets extend `Splash\Core\Models\Widgets\AbstractWidget`; a `SelfTest` widget rendering the
connector health is the conventional minimum.

## Standalone services (attributes)

For connectors written as **standalone** Symfony services (self-hosted applications, the
Toolkit test bench), objects, widgets, actions and extensions are plain services registered by
attributes, on the connector of type `standalone`:

| Attribute | Registers | Parameters |
|---|---|---|
| `#[AsStandaloneObject(type: "Product", scopes: array(...), bind: array(...))]` | an object mapper (`AbstractStandaloneObject`) | Splash type, provided scopes, extra constructor bindings |
| `#[AsStandaloneWidget(...)]` | a widget (`AbstractStandaloneWidget`) | widget type |
| `#[AsStandaloneAction(...)]` | an action | action code |
| `#[AsStandaloneExtension(...)]` | an object extension (extra fields on an existing object type) | target object type |

Attributes are `Autoconfigure` shortcuts: they tag the service (`StandaloneServiceTags`) so the
standalone connector discovers it. `Splash\Metadata\Objects\GenericDoctrineObject` (from
`splash/metadata`) is the usual base class for standalone objects mapped on Doctrine entities.

## Scopes

Scopes (`splash/scopes`) are contracts a connector commits to. Register them on the connector so
the Validator enforces them and the Splash server knows the connector capabilities:

```php
$this->registerScope(\Splash\Scopes\ThirdParty\Core::class);
$this->registerScope(\Splash\Scopes\Product\GalleryReceiver::class);
```

Standalone objects declare them in the attribute (`scopes:`). `getRegisteredScopes()` returns
the resolved scope codes.

## Files

When an object exposes `file` / `image` / `stream` fields, the connector must serve their raw
contents: implement `Splash\Core\Interfaces\FileProviderInterface` (`hasFile()`, `readFile()`)
on the object mapper, identified by `md5`. In the other direction, `$this->file($path, $md5)`
(`FilesInterface::getFile()`) fetches a file from the Splash server through an `ObjectFileEvent`.

## Tracking

Connectors that cannot push changes (no webhooks) implement `TrackingInterface` on their objects:
`isObjectTracked()`, `getObjectTrackingDelay()`, `getObjectUpdatedIds()`, `getObjectDeletedIds()`,
`doObjectChangesTracking()`. `TrackingTrait` on the connector schedules the polling and commits
the detected changes.
