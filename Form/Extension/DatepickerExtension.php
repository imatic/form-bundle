<?php

namespace Imatic\Bundle\FormBundle\Form\Extension;

use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\Options;

/**
 * Datepicker extension
 *
 * @author Pavel Batecko <pavel.batecko@imatic.cz>
 */
class DatepickerExtension extends AbstractTypeExtension
{
    /** @var string */
    protected $type;

    /**
     * @param string $type
     */
    public function __construct($type)
    {
        $this->type = $type;
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if ($options['rich']) {
            $pickDate = false;
            $pickTime = false;
            $config = [];

            switch ($this->type) {
                case DateType::class:
                    $pickDate = true;
                    break;

                case DateTimeType::class:
                    $pickDate = true;
                    $pickTime = true;
                    break;

                case TimeType::class:
                    $pickTime = true;
                    break;

                case BirthdayType::class:
                    $pickDate = true;
                    $config['viewMode'] = 'years';
                    break;

                default:
                    throw new \OutOfBoundsException(sprintf('The type "%s" is not valid', $this->type));
            }

            $view->vars['pick_date'] = $pickDate;
            $view->vars['pick_time'] = $pickTime;
            $view->vars['config'] = $config;
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'rich' => true,
            'template' => function (Options $options, $default) {
                return $options['rich']
                    ? 'ImaticFormBundle:Form:datepicker.html.twig'
                    : $default
                ;
            },
            'widget' => function (Options $options, $default) {
                return $options['rich']
                    ? 'single_text'
                    : $default
                ;
            },
        ]);
    }

    public function getExtendedType()
    {
        return $this->type;
    }
}
