<?php declare(strict_types=1);
namespace Imatic\Bundle\FormBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TemplateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired([
            'template',
        ]);
        $resolver->setDefaults([
            'mapped' => false,
            'required' => false,
            'label' => false,
        ]);
    }
}
