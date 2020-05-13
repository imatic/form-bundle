<?php declare(strict_types=1);
namespace Imatic\Bundle\FormBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Collection extension.
 *
 * @author Pavel Batecko <pavel.batecko@imatic.cz>
 */
class CollectionExtension extends AbstractTypeExtension
{
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if (isset($view->vars['prototype'])) {
            $view->vars['prototype_name'] = $options['prototype_name'];
            $view->vars['collection_button_style'] = $options['collection_button_style'];
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'template' => function (Options $options, $default) {
                return $options['prototype']
                    ? '@ImaticForm/Form/collection.html.twig'
                    : $default;
            },
            'collection_button_style' => 'bootstrap-horizontal',
            'entry_options' => function (Options $options, $default) {
                return ($default ?: []) + [
                    'label' => false,
                ];
            },
        ]);
    }

    public static function getExtendedTypes(): iterable
    {
        return [CollectionType::class];
    }
}
