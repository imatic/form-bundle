<?php declare(strict_types=1);
namespace Imatic\Bundle\FormBundle\Twig\Extension;

use Symfony\Component\Form\FormRendererInterface;
use Symfony\Component\Form\FormView;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Form extension.
 *
 * @author Pavel Batecko <pavel.batecko@imatic.cz>
 */
class FormExtension extends AbstractExtension
{
    /** @var FormRendererInterface */
    private $renderer;
    /** @var int */
    private $prototypeRenderUidSeq = 0;

    public function __construct(FormRendererInterface $renderer)
    {
        $this->renderer = $renderer;
    }

    public function getFunctions()
    {
        return [
            // imatic_form_javascript
            new TwigFunction(
                'imatic_form_javascript',
                [$this, 'renderFormJavascript'],
                ['is_safe' => ['html']]
            ),

            // imatic_form_javascript_prototypes
            new TwigFunction(
                'imatic_form_javascript_prototypes',
                [$this, 'renderFormJavascriptPrototypes'],
                ['needs_context' => true, 'is_safe' => ['html']]
            ),

            // imatic_form_override_namespace
            new TwigFunction(
                'imatic_form_override_namespace',
                [$this, 'overrideFormNamespace']
            ),
        ];
    }

    /**
     * Render form javascript.
     *
     * @param FormView $view      a form view
     * @param bool     $prototype render inner JS only 1/0
     *
     * @return string
     */
    public function renderFormJavascript(FormView $view, $prototype = false)
    {
        $block = $prototype ? 'javascript_prototype' : 'javascript';

        return $this->renderer->searchAndRenderBlock($view, $block);
    }

    /**
     * Render javascript prototypes.
     *
     * The form view must have a prototype.
     *
     * @param array    $context
     * @param FormView $rootView
     *
     * @return string javascript array
     */
    public function renderFormJavascriptPrototypes(array $context, FormView $rootView)
    {
        if (!isset($rootView->vars['prototype'])) {
            throw new \InvalidArgumentException('The given root form view has no prototype');
        }

        $stack = [
            // FormView $prototype, string $prototypeName, string[] $parentPrototypeNames
            [$rootView->vars['prototype'], $rootView->vars['prototype']->vars['name'], []],
        ];

        $output = '[';
        $counter = 0;

        while (list($prototype, $prototypeName, $parentPrototypeNames) = \array_pop($stack)) {
            try {
                // the "unique_block_prefix" must be changed to prevent messing
                // up FormRenderer's internal cache and causing errors later on
                $prototype->vars['unique_block_prefix'] .= \sprintf('_%d', ++$this->prototypeRenderUidSeq);

                if (isset($prototype->vars['prototype'])) {
                    // directly nested collection prototypes lead to recursion in searchAndRenderBlock()
                    throw new \RuntimeException('Directly nested collections with prototypes are not supported. Add an intermediate type or disable prototypes.');
                }
                $code = $this->renderer->searchAndRenderBlock($prototype, 'javascript_prototype', $context);

                if (++$counter > 1) {
                    $output .= ",\n";
                }

                $output .= \sprintf(
                    '{id: %s, prototypeName: %s, parentPrototypeNames: %s, initializer: function ($field) { %s }}',
                    \json_encode($prototype->vars['id']),
                    \json_encode($prototypeName),
                    \json_encode($parentPrototypeNames),
                    $code
                );
            } catch (\LogicException $e) {
                foreach ($prototype->children as $child) {
                    $stack[] = isset($child->vars['prototype'])
                        ? [
                            $child->vars['prototype'],
                            $child->vars['prototype']->vars['name'],
                            \array_merge($parentPrototypeNames, [$prototypeName]),
                        ]
                        : [
                            $child,
                            $prototypeName,
                            $parentPrototypeNames,
                        ];
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
     *
     * @throws \InvalidArgumentException
     */
    public function overrideFormNamespace(FormView $rootForm, $newNamespace, $replaceFirstSegment = false)
    {
        $stack = [$rootForm];

        while ($form = \array_pop($stack)) {
            if (false !== ($firstSegmentPos = \strpos($form->vars['full_name'], '['))) {
                if ($form === $rootForm) {
                    throw new \InvalidArgumentException('The given form view is not a root form view');
                }

                $rest = \substr($form->vars['full_name'], $firstSegmentPos);

                $form->vars['full_name'] = $replaceFirstSegment
                    ? \sprintf(
                        '%s%s',
                        $newNamespace,
                        $rest
                    )
                    : \sprintf(
                        '%s[%s]%s',
                        $newNamespace,
                        \substr($form->vars['full_name'], 0, $firstSegmentPos),
                        $rest
                    );
            } else {
                $form->vars['full_name'] = \sprintf(
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
}
