<?php
namespace Imatic\Bundle\FormBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Datepicker extension.
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
    public function __construct(string $type)
    {
        $this->type = $type;
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if ($options['rich']) {
            $pickDate = false;
            $pickTime = false;

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
                    $options['config']['viewMode'] = 'years';
                    break;

                default:
                    throw new \OutOfBoundsException(\sprintf('The type "%s" is not valid', $this->type));
            }

            $view->vars['pick_date'] = $pickDate;
            $view->vars['pick_time'] = $pickTime;
            $view->vars['config'] = $options['config'];
            $view->vars['config_locale'] = $options['config_locale'];
            $view->vars['date_format'] = $options['date_format'];
            $view->vars['date_time_format'] = $options['date_time_format'];
            $view->vars['time_format'] = $options['time_format'];
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'rich' => true,
            'config' => [],
            'config_locale' => [],
            'date_format' => 'YYYY-MM-DD',
            'date_time_format' => 'YYYY-MM-DD HH:mm',
            'time_format' => 'HH:mm',
            'template' => function (Options $options, $default) {
                return $options['rich']
                    ? 'ImaticFormBundle:Form:datepicker.html.twig'
                    : $default;
            },
            'widget' => function (Options $options, $default) {
                return $options['rich']
                    ? 'single_text'
                    : $default;
            },
        ]);
        $resolver->setAllowedTypes('config', 'array');
    }

    public function getExtendedType()
    {
        return $this->type;
    }
}
