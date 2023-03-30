<?php declare(strict_types=1);
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
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if ($options['rich']) {
            $pickDate = false;
            $pickTime = false;

            $type = $form->getConfig()->getType()->getInnerType();

            switch (\get_class($type)) {
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
                    throw new \OutOfBoundsException(\sprintf('The type "%s" is not valid', \get_class($type)));
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
            'config' => function (Options $options, $default) {
                return $default ?? [];
            },
            'config_locale' => function (Options $options, $default) {
                return $default ?? [];
            },
            'date_format' => function (Options $options, $default) {
                return $default ?? 'YYYY-MM-DD';
            },
            'date_time_format' => function (Options $options, $default) {
                return $default ?? 'YYYY-MM-DD HH:mm';
            },
            'time_format' => function (Options $options, $default) {
                return $default ?? 'HH:mm';
            },
            'template' => function (Options $options, $default) {
                return $options['rich']
                    ? '@ImaticForm/Form/datepicker.html.twig'
                    : $default;
            },
            'widget' => function (Options $options, $default) {
                return $options['rich']
                    ? 'single_text'
                    : $default;
            },
        ]);

        $resolver->setAllowedTypes('config', 'array');
        $resolver->setAllowedTypes('config_locale', 'array');
    }

    public static function getExtendedTypes(): iterable
    {
        return [
            DateType::class,
            DateTimeType::class,
            TimeType::class,
            BirthdayType::class,
        ];
    }
}
