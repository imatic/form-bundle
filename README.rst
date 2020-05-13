.. image:: https://secure.travis-ci.org/imatic/form-bundle.png?branch=master
   :alt: Build Status
   :target: http://travis-ci.org/imatic/form-bundle
|
.. image:: https://img.shields.io/badge/License-MIT-yellow.svg
   :alt: License: MIT
   :target: LICENSE

================
ImaticFormBundle
================

**********
Form types
**********

- `Imatic\\Bundle\\FormBundle\\Form\\Type\\AjaxChoiceType </Form/Type/AjaxChoiceType.php>`_
- `Imatic\\Bundle\\FormBundle\\Form\\Type\\AjaxEntityChoiceType </Form/Type/AjaxEntityChoiceType.php>`_
- `Imatic\\Bundle\\FormBundle\\Form\\Type\\DateRangeType </Form/Type/DateRangeType.php>`_
- `Imatic\\Bundle\\FormBundle\\Form\\Type\\DateTimeRangeType </Form/Type/DateTimeRangeType.php>`_
- `Imatic\\Bundle\\FormBundle\\Form\\Type\\RangeType </Form/Type/RangeType.php>`_

Ajax entity choice
------------------

Implements choice of single entity or collection of entities using XHR.

Dependencies: jQuery, Select2

.. sourcecode:: php

    <?php

    use Imatic\Bundle\FormBundle\Form\Type\AjaxEntityChoiceType;

    class ExampleType extends AbstractType
    {
        public function buildForm(FormBuilderInterface $builder, array $options)
        {
            $builder->add('author', AjaxEntityChoiceType::class, [
                'class' => 'MyExampleBundle:User',
                'route' => 'app_example_example_autocomplete',
            ]);
        }
    }

The widget will send a GET request to the specified route with the entered
search term. The request format depends on the ``request_type`` option:

- ``filter`` - ?filter[search][value]=term
- ``simple`` - ?search=term

The application should reply with a JSON response, example:

.. sourcecode:: json

    [
        {"id": 1, "text": "First Item"},
        {"id": 2, "text": "Second Item"}
    ]

Additional options:

- ``multiple`` - allow multiple items to be selected
- ``placeholder`` - text displayed if no item is selected
- ``query_builder`` - instance of QueryBuilder or Closure(EntityManager $em, $class): QueryBuilder
- ``id_provider`` - callable(object $entity): scalar (should return ID of the entity)
- ``text_provider`` - callable(object $item): string (should return text representation of the entity)
- ``request_type`` - filter (default) or simple
- ``route_attrs`` - custom route attributes
- ``entity_manager`` - name of the entity manager to use

Datepicker
----------

* `Imatic\\Bundle\\FormBundle\\Form\\Extension\\DatepickerExtension </Form/Extension/DatepickerExtension.php>`_

This example shows, how to change default date type format and modify moment configurations by form extension.

.. sourcecode:: php

    <?php

    namespace App\Form\Extension;

    use Imatic\Bundle\FormBundle\Form\Extension\DatepickerExtension;
    use Symfony\Component\OptionsResolver\OptionsResolver;

    class DateTypeExtension extends DatepickerExtension
    {
        public function configureOptions(OptionsResolver $resolver)
        {
            parent::configureOptions($resolver);

            $resolver->setDefaults([
                'format' => 'dd.MM.yyyy',
                'date_format' => 'DD.MM.YYYY',
                'config_locale' => [
                    'en' => [
                        'week' => ['dow' => 1],
                    ],
                ],
            ]);
        }

        public static function getExtendedTypes(): iterable
        {
            return [DateType::class];
        }
    }


***************
Form extensions
***************

Form theme
----------

This extensions allows you to set form theme through the type's options.

.. sourcecode:: php

    <?php

    class ExampleType extends AbstractType
    {
        public function buildForm(FormBuilderInterface $builder, array $options)
        {
            // example: setting template of child form (field)
            $builder->add('example', null, [
                // override form theme template
                'template' => 'MyBundle:Form:example_theme.html.twig',

                // pass extra variables to the theme templates when this field is rendered
                'template_parameters' => [
                    'foo' => 'bar',
                ],
            ]);
        }

        public function configureOptions(OptionsResolver $resolver)
        {
            // example: setting template of the form type itself
            $resolver->setDefaults([
                'template' => 'MyBundle:Form:example_theme.html.twig',
                'template_parameters' => ['foo' => 'bar'],
            ]);
        }
    }


*********************
Validator constraints
*********************

Number
------

* ensures that number have correct precision and scale

options
```````
* precision
* scale

Latitude
--------

Longtitude
----------

NotNullGroup
------------

This class-level constraint ensures that all given properties are either set or null.

Valid states: all properties are null, all properties are NOT null.


NotNullOneOf
------------
This class-level constraint ensures that at least one of the given properties is NOT null.

Valid states: at least one property is NOT null


Example
-------

.. sourcecode:: php

    <?php

    use Imatic\Bundle\FormBundle\Validator\Constraints as ImaticAssert;

    /**
     * Evidence
     *
     * @ORM\Entity
     * @ImaticAssert\NotNullGroup(properties={"sitterFirstName", "sitterLastName", "sitterId", "sitterPhone", "sitterRelation"})
     * @ImaticAssert\NotNullOneOf(properties={"mother", "father"}, message="Either the mother or the father information must be specified.")
     */
    class Evidence
    {
        // ...
    }


*****************
Data transformers
*****************

EmptyEntityToNullTransformer
----------------------------

This transformers converts an entity object to null, if it is considered empty. The
check is performed based on list of properties that are to be verified.

If strict mode is disabled (default), both nulls and empty strings are considered empty.

If strict mode is enabled, only nulls are considered empty.

.. sourcecode:: php

    <?php

    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\Form\FormBuilderInterface;
    use Imatic\Bundle\FormBundle\Form\DataTransformer\EmptyEntityToNullTransformer;
    
    /**
     * Address type
     */
    class AddressType extends AbstractType
    {
        /**
         * @param FormBuilderInterface $builder
         * @param array $options
         */
        public function buildForm(FormBuilderInterface $builder, array $options)
        {
            $builder
                ->add('street')
                ->add('number')
                ->add('city')
                ->add('postalCode', 'text')
            ;
    
            $builder->addModelTransformer(new EmptyEntityToNullTransformer(
                array_keys($builder->all())
            ));
        }
        
        // ...
    }
