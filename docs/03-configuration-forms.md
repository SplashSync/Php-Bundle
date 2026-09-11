# Configuration Forms

The host (Toolkit, Splash admin, bridge host) renders a form to edit the configuration of a
connection. The connector only describes it: `getFormBuilderName()` returns the FQCN of a
Symfony `FormType`; the host builds it with the **persisted configuration as data** and saves
the submitted values back into the configuration.

```php
public function getFormBuilderName(): string
{
    // The form may depend on the connection state
    return $this->getParameter("ListsIndex", false) ? EditFormType::class : NewFormType::class;
}
```

## Writing the form

```php
abstract class AbstractAcmeType extends AbstractType
{
    const string DOMAIN = "AcmeBundle";

    public function addApiKeyField(FormBuilderInterface $builder): static
    {
        $builder->add('ApiKey', TextType::class, array(
            'label' => "var.apikey.label",
            'help' => "var.apikey.desc",
            'required' => true,
            'translation_domain' => self::DOMAIN,
        ));

        return $this;
    }

    public function addListField(FormBuilderInterface $builder, array $options): static
    {
        // Choices come from the persisted configuration, filled by connect()
        if (empty($options["data"]["ListsIndex"])) {
            return $this;
        }
        $builder->add('DefaultList', ChoiceType::class, array(
            'label' => "var.list.label",
            'required' => true,
            'translation_domain' => self::DOMAIN,
            'choice_translation_domain' => false,
            'choices' => array_flip($options["data"]["ListsIndex"]),
        ));

        return $this;
    }
}

class EditFormType extends AbstractAcmeType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $this->addApiKeyField($builder)->addListField($builder, $options);
    }
}
```

Field names are the configuration keys. Labels and help texts are translation keys of the
connector domain (`src/Resources/translations/<Domain>.<locale>.yaml`).

## Allowed form types & options

A connector may run as a compiled bridge worker: the host then does not have the connector
classes, only a **serialized description** of the form (name, type, options). Hence:

- **Symfony core types only**: `TextType`, `PasswordType`, `UrlType`, `EmailType`, `IntegerType`,
  `NumberType`, `CheckboxType`, `ChoiceType`, `DateType`, `DateTimeType`, `TextareaType`,
  `HiddenType`... plus **`Splash\Bundle\Form\Type\KeyValueType`** (below). A form type class
  defined by the connector does not exist on the host.
- **Only these options survive serialization**: `label`, `help`, `required`, `disabled`,
  `translation_domain`, `placeholder`, `empty_data`, `attr`, `choices`, `multiple`, `expanded`,
  `preferred_choices`, `widget`, `format`, `input`, `key_type`, `key_options`, `value_type`,
  `value_options`. Everything else (`constraints`, `data_class`, `mapped`, `choice_loader`,
  event listeners, closures) is dropped — validate in `selfTest()` instead.
- Options must be JSON-serializable scalars or arrays: never compute `choices` from a live
  API call inside the form; store the catalog in the configuration during `connect()`.
- The form is built from the persisted data: a field depending on another value shows up on the
  next render, after `updateConfiguration()`.

## `KeyValueType`

`Splash\Bundle\Form\Type\KeyValueType` (inspired by `burgov/key-value-form-bundle`, block prefix
`burgov_key_value`) edits an associative array as add/remove rows of key + value — custom field
mappings, per-list settings, headers...

```php
$builder->add('FieldsMapping', KeyValueType::class, array(
    'label' => "var.mapping.label",
    'required' => false,
    'translation_domain' => self::DOMAIN,
    'key_type' => TextType::class,                  // default: TextType
    'key_options' => array('label' => "Remote code"),
    'value_type' => ChoiceType::class,              // REQUIRED, any core type
    'value_options' => array('choices' => Dictionary::getChoices()),
));
```

The stored value is a plain `array<string, mixed>`. The options `allowed_keys` and
`use_container_object` exist for native hosts only: they are not part of the serialized
description and must not be relied on by a bridged connector.

## OAuth2 forms

Connectors using `splash/oauth2` extend `Oauth2ConfigurationForm` (shared application, optional
private mode) or `PrivateAppConfigurationForm`, and add their own fields after
`parent::buildForm()`.
