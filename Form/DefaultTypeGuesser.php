<?php

namespace Imatic\Bundle\FormBundle\Form;

use Symfony\Bridge\Doctrine\Form\DoctrineOrmTypeGuesser;
use Symfony\Component\Form\Guess\Guess;
use Symfony\Component\Form\Guess\TypeGuess;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * @author Viliam Husár <viliam.husar@imatic.cz>
 * @author Pavel Batecko <pavel.batecko@imatic.cz>
 */
class DefaultTypeGuesser extends DoctrineOrmTypeGuesser
{
    /** @var TranslatorInterface */
    protected $translator;

    public function setTranslator(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function guessType($class, $property)
    {
        if (!$ret = $this->getMetadata($class)) {
            return;
        }

        list($metadata, $name) = $ret;

        /** @var $metadata ClassMetadata */
        if ($metadata->hasAssociation($property)) {
            $multiple = $metadata->isCollectionValuedAssociation($property);
            $mapping = $metadata->getAssociationMapping($property);

            $placeholder = $multiple ? 'Select values' : 'Select a value';
            $placeholder = $this->translator->trans($placeholder, [], 'ImaticFormBundle');

            return new TypeGuess('genemu_jqueryselect2_entity', [
                'em' => $name,
                'class' => $mapping['targetEntity'],
                'multiple' => $multiple,
                'configs' => ['placeholder' => $placeholder, 'allowClear' => true],
            ], Guess::VERY_HIGH_CONFIDENCE);
        }

        $type = $metadata->getTypeOfField($property);

        switch ($type) {
            case 'date':
            case 'datetime':
            case 'time':
                $options = [
                    'widget' => 'single_text',
                    "{$type}picker" => true,
                ];

                return new TypeGuess($type, $options, Guess::VERY_HIGH_CONFIDENCE);
        }

        return parent::guessType($class, $property);
    }
}
