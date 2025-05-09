<?php

namespace Splash\Bundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\ArrayChoiceList;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Webmozart\Assert\Assert;

class KeyValueRowType extends AbstractType
{
    /**
     * @inheritdoc
     *
     * @param array{
     *     key_type: string,
     *     key_options: array,
     *     value_type: string,
     *     value_options: array,
     *     allowed_keys: array|null,
     * } $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if (null === $options['allowed_keys']) {
            $builder->add('key', (string) $options['key_type'], $options['key_options']);
        } else {
            $builder->add('key', ChoiceType::class, array_merge(
                array('choices' => $options['allowed_keys']),
                $options['key_options']
            ));
        }
        $builder->add('value', (string)  $options['value_type'], $options['value_options']);
    }

    /**
     * @inheritdoc
     */
    public function getBlockPrefix(): string
    {
        return 'burgov_key_value_row';
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array(
            'key_type' => TextType::class,
            'key_options' => array(),
            'value_options' => array(),
            'allowed_keys' => null
        ));
        $resolver->setRequired(array('value_type'));
        $resolver->setAllowedTypes('key_type', 'string');
        $resolver->setAllowedTypes('allowed_keys', array('null', 'array'));
    }
}
