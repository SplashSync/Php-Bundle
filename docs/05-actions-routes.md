# Actions & Routes

## Routes

The bundle routing (`Resources/config/routing.php`, names in `SplashBundleRoutes`) is mounted by
the host under a prefix (the Toolkit uses `/ws`):

| Route name | Path | Purpose |
|---|---|---|
| `splash_main_soap` | `/splash` | SOAP endpoint called by the Splash servers |
| `splash_test_soap` | `/splash-test` | SOAP connect & test endpoint |
| `splash_connector_action_master` | `/{connectorName}` | **Master action**: one url per connector, no webservice id (OAuth2 callbacks, provider-wide webhooks) |
| `splash_connector_action` | `/{connectorName}/{webserviceId}/{action}` | **Public actions**: unauthenticated, per connection (webhooks receivers), `action` defaults to `index` |
| `splash_connector_secured_action` | `/{connectorName}/{webserviceId}/secured/{action}` | **Secured actions**: logged-in host user (OAuth2 connect / refresh / revoke, manual sync, setup) |

`connectorName` is the profile name (`acme`) or the bridged name (`3.0@acme`).

## Declaring actions

```php
public function getMasterAction(): ?string
{
    return Actions\Master::class;              // or null
}

public function getPublicActions(): array
{
    return array(
        "index" => Actions\Webhooks\Receive::class,
    );
}

public function getSecuredActions(): array
{
    return array(
        "webhooks" => Actions\Webhooks\Setup::class,
    );
}
```

An action is an invokable controller (`AbstractController`) receiving the request and the
configured connector; it returns a `Response`. `ActionsController` resolves the connector and
its configuration from the route parameters before invoking it.

Rules:
- Build action urls with `ConnectorRoutesBuilder` (`RoutesBuilderAwareTrait`:
  `$this->getRouteBuilder()->getPublicUrl(...)`), never from the router with hard-coded names:
  the host prefix and the connector name are only known at runtime.
- Public actions are reachable by anyone: verify signatures / tokens of incoming webhooks in the
  action itself.
- Secured actions may assume an authenticated host user, nothing more (no session storage).
- OAuth2 flows use the master action as provider callback (`splash/oauth2` provides it).

## Profile

`getProfile()` describes the connector to the host and to the bridge manifest:

```php
return array(
    'enabled' => true,
    'beta' => false,
    'type' => self::TYPE_ACCOUNT,                 // TYPE_ACCOUNT (SaaS) | TYPE_SERVER (self-hosted)
    'name' => 'acme',                             // lowercase, no space, stable: bridge id, routes, connections
    'connector' => 'splash.connectors.acme',      // service id
    'title' => 'profile.card.title',              // translation keys...
    'label' => 'profile.card.label',
    'domain' => 'AcmeBundle',                     // ...in this domain (en, fr, it, es, de)
    'ico' => '/bundles/acme/img/icon.png',
    'www' => 'www.acme.com',
);
```

`getConnectedTemplate()`, `getOfflineTemplate()` and `getNewTemplate()` return the Twig templates
of the connection card in the three states; keep them in `src/Resources/views/Profile/`.
