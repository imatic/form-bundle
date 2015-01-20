================
ImaticFormBundle
================


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

******************
Entity subscribers
******************

RemoveUnsentFieldsSubscriber
----------------------------

* removes fields which weren't submitted


.. sourcecode:: php

   <?php

   $post = [
       'title' => 'Jackie Chan Adventures',
       'type' => 'cartoon',
   ];
   $request = new Request([], $post);

   $this->createFormBuilder()
       ->add('title', 'text')
       ->add('type', 'text')
       ->add('description', 'text')
       ->addEventSubscriber(new RemoveUnsentFieldsSubscriber())
       ->getForm()
   ;

   $form->handleRequest($request);
   $form->all(); // contains only fields: title, type

