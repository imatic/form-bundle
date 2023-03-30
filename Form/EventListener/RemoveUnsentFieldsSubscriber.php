<?php declare(strict_types=1);
namespace Imatic\Bundle\FormBundle\Form\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * @author Miloslav Nenadal <miloslav.nenadal@imatic.cz>
 */
class RemoveUnsentFieldsSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SUBMIT => 'preSubmit',
        ];
    }

    public function preSubmit(FormEvent $event)
    {
        $usedFields = \array_keys($event->getData());
        $children = \array_keys($event->getForm()->all());
        \array_map([$event->getForm(), 'remove'], \array_diff($children, $usedFields));
    }
}
