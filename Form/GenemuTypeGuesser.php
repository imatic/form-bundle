<?php

namespace Imatic\Bundle\FormBundle\Form;

use Symfony\Bridge\Doctrine\Form\DoctrineOrmTypeGuesser as BaseDoctrineOrmTypeGuesser;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Form\Guess\TypeGuess;
use Symfony\Component\Form\Guess\Guess;
use Doctrine\ORM\Mapping\ClassMetadata;

/**
 * Uses Genemu widgets instead Form core widgets.
 *
 * @author Viliam Husár <viliam.husar@imatic.cz>
 */
class GenemuTypeGuesser extends BaseDoctrineOrmTypeGuesser
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    public function setTranslator(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function guessType($class, $property)
    {
        if (!$ret = $this->getMetadata($class)) {
            return new TypeGuess('text', array(), Guess::LOW_CONFIDENCE);
        }

        list($metadata, $name) = $ret;

        /** @var $metadata  ClassMetadata */
        if ($metadata->hasAssociation($property)) {
            $multiple = $metadata->isCollectionValuedAssociation($property);
            $mapping = $metadata->getAssociationMapping($property);

            $placeholder = $multiple ? "Select values" : "Select a value";
            $placeholder = $this->translator->trans($placeholder, array(), 'messages');

            return new TypeGuess('genemu_jqueryselect2_entity', array(
                'em' => $name,
                'class' => $mapping['targetEntity'],
                'multiple' => $multiple,
                'required' => false,
                'configs' => array('placeholder' => $placeholder, 'allowClear' => true),
            ), Guess::VERY_HIGH_CONFIDENCE);
        }

        switch ($metadata->getTypeOfField($property)) {
            case 'date':
                $options = array(
                    'widget' => 'single_text',
                    'format' => 'd.M.y',
                    'configs' => array('dateFormat' => 'd/M/Y')
                );

                return new TypeGuess('genemu_jquerydate', $options, Guess::VERY_HIGH_CONFIDENCE);
        }

        return parent::guessType($class, $property);
    }
}
