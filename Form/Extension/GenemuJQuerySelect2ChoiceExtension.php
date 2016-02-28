<?php

namespace Imatic\Bundle\FormBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Miloslav Nenadal <miloslav.nenadal@imatic.cz>
 */
class GenemuJQuerySelect2ChoiceExtension extends AbstractTypeExtension
{
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'configs' => function (Options $options, $configs) {
                if (empty($configs)) {
                    $configs = [];

                    if (!$options->get('required')) {
                        $configs['allowClear'] = true;
                    }
                }

                return $configs;
            },
        ]);
    }

    public function getExtendedType()
    {
        return 'genemu_jqueryselect2_choice';
    }
}
