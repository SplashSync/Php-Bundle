# AI Tools

A connector can expose **AI Tools**: typed operations an AI agent can discover and call through
the Splash Toolkit MCP server (`listServerTools`, `executeServerTool`, capability tools,
`splash://servers/{serverId}/resources/...`). Tools are defined in `splash/phpcore`
(`AbstractAiTool`, see `vendor/splash/phpcore/docs/08-ai-tools`); standard contracts, the
**capabilities**, come from `splash/scopes`.

`AbstractConnector` uses `Connectors\AiToolsTrait`, which exposes no tool. Two registration
styles exist, as for objects and widgets.

## Mapped tools (`GenericAiToolsMapperTrait`)

A classic connector lists its tool classes in a static map; the trait derives every
`AiToolsInterface` method from it.

```php
use Splash\Bundle\Models\Connectors\GenericAiToolsMapperTrait;

class AcmeConnector extends AbstractConnector
{
    use GenericAiToolsMapperTrait;

    /**
     * AI Tools Classes: Tool Type is given by each Tool
     *
     * @var array<int, class-string<AiToolInterface>>
     */
    protected static array $aiToolsMap = array(
        AiTools\ContactLists::class,
        AiTools\CheckStock::class,
    );
}
```

- **A list, not a `type => class` map**: each tool gives its type with `getType()`. A tool
  implementing a capability takes the type of the capability (`splash_capabilities_...`), the
  same on every server; a hand-written key could contradict it.
- **Construction**: `new $toolClass($connector)`, the tool receives the connector and reaches its
  API and services. A tool extending `AbstractStandaloneAiTool` is also configured with its type,
  the webservice id and the connector configuration.
- **Built on every call**: one connector service may serve several servers, a tool always works
  with the current configuration.
- **Safety rules**, as for objects and widgets:
  - `getAvailableAiTools()` and `getAiToolDescription()` return nothing when `selfTest()` fails;
  - disabled tools (`isDisabled()`) are never exposed;
  - `executeAiTool()` on an unknown or disabled type returns a `tool_not_found` failure response,
    never throws.
- **Configuration errors throw**: a mapped class that does not exist or does not implement
  `AiToolInterface`, and two tools giving the same type.

## Standalone tools (`#[AsStandaloneAiTool]`)

On the `standalone` connector, tools are services extending `AbstractStandaloneAiTool` and
tagged with the attribute:

```php
#[AsStandaloneAiTool(type: "product_get_raw")]
class GetRaw extends AbstractStandaloneAiTool
{
    // ...
}

#[AsStandaloneAiTool]
class CheckStock extends AbstractStandaloneAiTool implements CapabilityAwareInterface
{
    public function getCapability(): string
    {
        return \Splash\Capabilities\Product\CheckStock::class;
    }
    // ...
}
```

The type is required, unless the tool implements a capability: the capability gives it, and a
type given to the tag is ignored. Two tools of the same type throw when the container is built.

## Writing a tool

Only `run(array $inputs): array` is required: it returns a list of items. Inputs are validated
and filtered before `run()`, outputs are filtered to declared fields after it. Name, description,
kind, inputs and outputs come from static properties and `buildInputs()` / `buildOutputs()`, or
from the capability when the tool implements one.
