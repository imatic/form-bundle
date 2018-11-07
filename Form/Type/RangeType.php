<?php declare(strict_types=1);
namespace Imatic\Bundle\FormBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Miloslav Nenadal <miloslav.nenadal@imatic.cz>
 */
class RangeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('start', $options['field_type'], $options['field_options'])
            ->add('end', $options['field_type'], $options['field_options']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'field_type' => NumberType::class,
            'field_options' => [],
        ]);
    }
}
