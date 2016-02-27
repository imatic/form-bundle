<?php

namespace Imatic\Bundle\FormBundle\Twig\Extension;

use Symfony\Bridge\Twig\Form\TwigRendererInterface;
use Symfony\Component\Form\FormView;
use Twig_Extension;
use Twig_Function_Method;

/**
 * Form extension
 *
 * @author Pavel Batecko <pavel.batecko@imatic.cz>
 */
class FormExtension extends Twig_Extension
{
    /** @var TwigRendererInterface */
    private $renderer;
    /** @var int */
    private $prototypeRenderUidSeq = 0;

    public function __construct(TwigRendererInterface $renderer)
    {
        $this->renderer = $renderer;
    }

    public function getFunctions()
    {
        return [
            'imatic_form_override_namespace' => new Twig_Function_method(
                $this,
                'overrideFormNamespace'
            ),
            'imatic_form_javascript_prototype' => new Twig_Function_Method(
                $this,
                'renderFormJavascriptPrototype',
                ['needs_context' => true, 'is_safe' => ['html']]
            ),
        ];
    }

    /**
     * @param array    $context
     * @param FormView $rootForm
     * @throws \InvalidArgumentException if the given root form has no prototype
     * @return string javascript array
     */
    public function renderFormJavascriptPrototype(array $context, FormView $rootForm)
    {
        if (!isset($rootForm->vars['prototype'])) {
            throw new \InvalidArgumentException('The given root form view has no prototype');
        }

        $stack = [
            // FormView $prototype, string $prototypeName, string[] $parentPrototypeNames
            [$rootForm->vars['prototype'], $rootForm->vars['prototype']->vars['name'], []],
        ];
        
        $output = '[';
        $counter = 0;

        while (list($prototype, $prototypeName, $parentPrototypeNames) = array_pop($stack)) {
            try {
                // the "unique_block_prefix" must be changed to prevent messing
                // up FormRenderer's internal cache and causing errors later on
                $prototype->vars['unique_block_prefix'] .= sprintf('_%d', ++$this->prototypeRenderUidSeq);

                if (isset($prototype->vars['prototype'])) {
                    // directly nested collection prototypes lead to recursion in searchAndRenderBlock()
                    throw new \RuntimeException('Directly nested collections with prototypes are not supported. Add an intermediate type or disable prototypes.');
                }
                $code = $this->renderer->searchAndRenderBlock($prototype, 'javascript_prototype', $context);
                
                if (++$counter > 1) {
                    $output .= ",\n";
                }

                $output .= sprintf(
                    '{id: %s, prototypeName: %s, parentPrototypeNames: %s, initializer: function ($field) { %s }}',
                    json_encode($prototype->vars['id']),
                    json_encode($prototypeName),
                    json_encode($parentPrototypeNames),
                    $code
                );
            } catch (\LogicException $e) {
                foreach ($prototype->children as $child) {
                    $stack[] = isset($child->vars['prototype'])
                        ? [
                            $child->vars['prototype'],
                            $child->vars['prototype']->vars['name'],
                            array_merge($parentPrototypeNames, [$prototypeName]),
                        ]
                        : [
                            $child,
                            $prototypeName,
                            $parentPrototypeNames,
                        ]
                    ;
                }
            }
        }

        $output .= ']';

        return $output;
    }

    /**
     * @param FormView $rootForm
     * @param string   $newNamespace
     * @param bool     $replaceFirstSegment
     * @throws \InvalidArgumentException
     */
    public function overrideFormNamespace(FormView $rootForm, $newNamespace, $replaceFirstSegment = false)
    {
        $stack = [$rootForm];

        while ($form = array_pop($stack)) {
            if (false !== ($firstSegmentPos = strpos($form->vars['full_name'], '['))) {
                if ($form === $rootForm) {
                    throw new \InvalidArgumentException('The given form view is not a root form view');
                }

                $rest = substr($form->vars['full_name'], $firstSegmentPos);

                $form->vars['full_name'] = $replaceFirstSegment
                    ? sprintf(
                        '%s%s',
                        $newNamespace,
                        $rest
                    )
                    :  sprintf(
                        '%s[%s]%s',
                        $newNamespace,
                        substr($form->vars['full_name'], 0, $firstSegmentPos),
                        $rest
                    )
                ;
            } else {
                $form->vars['full_name'] = sprintf(
                    '%s[%s]',
                    $newNamespace,
                    $form->vars['full_name']
                );
            }

            $form->vars['id'] = $newNamespace . '_' . $form->vars['id'];

            if (isset($form->vars['prototype'])) {
                $stack[] = $form->vars['prototype'];
            }
            foreach ($form->children as $child) {
                $stack[] = $child;
            }
        }
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'imatic_form';
    }
}
