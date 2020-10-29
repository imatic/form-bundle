<?php declare(strict_types=1);
namespace Imatic\Bundle\FormBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Choice extension.
 *
 * @author Pavel Batecko <pavel.batecko@imatic.cz>
 */
class ChoiceExtension extends AbstractTypeExtension
{
    /** @var TranslatorInterface */
    protected $translator;

    /** @var array */
    protected $select2Config;

    public function __construct(TranslatorInterface $translator, $select2Config)
    {
        $this->select2Config = $select2Config;
        $this->translator = $translator;
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if ($options['rich']) {
            $placeholder = $options['placeholder'] ?: '';
            if ($placeholder && $options['translation_domain'] !== false) {
                $placeholder = $this->translator->trans($options['placeholder'], [], $options['translation_domain']);
            }

            $view->vars['select2_options'] = [
                'placeholder' => $placeholder,
                'multiple' => $options['multiple'],
                'allowClear' => $options['multiple'] ? false : !$options['required'],
            ] + $this->select2Config;
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'rich' => function (Options $options) {
                return !$options['expanded'];
            },
            'template' => function (Options $options) {
                return $options['rich']
                    ? '@ImaticForm/Form/choice.html.twig'
                    : null;
            },
        ]);
    }

    public static function getExtendedTypes(): iterable
    {
        return [ChoiceType::class];
    }
}
