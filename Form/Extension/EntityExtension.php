<?php declare(strict_types=1);
namespace Imatic\Bundle\FormBundle\Form\Extension;

use Psr\Log\LoggerInterface;
use Symfony\Bridge\Doctrine\Form\ChoiceList\IdReader;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\ChoiceList\Factory\Cache\ChoiceValue;
use Symfony\Component\Form\FormBuilderInterface;

class EntityExtension extends AbstractTypeExtension
{
    private $logger;
    private $throw;

    public function __construct(LoggerInterface $logger, bool $throw)
    {
        $this->logger = $logger;
        $this->throw = $throw;
    }

    private function unmanagedErrorPossibility(array $options): bool
    {
        $choiceValue = $options['choice_value'] ?? null;

        if (\class_exists('Symfony\Component\Form\ChoiceList\Factory\Cache\ChoiceValue') && $choiceValue instanceof ChoiceValue) {
            $choiceValue = $choiceValue->getOption();
        }

        return \is_array($choiceValue)
            && \count($choiceValue) === 2
            && $choiceValue[1] === 'getIdValue'
            && $choiceValue[0] instanceof IdReader;
    }

    private function createErrorMessage(string $formType): string
    {
        return "Form type `{$formType}` does not specify `choice_value` option which can lead to unmanaged entity exception in case `UnitOfWork` state is cleared before constructing the form.";
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($this->unmanagedErrorPossibility($options)) {
            $formType = \get_class($builder->getFormConfig()->getType()->getInnerType());
            $message = $this->createErrorMessage($formType);

            if ($this->throw) {
                throw new \RuntimeException($message);
            }

            $this->logger->error($message);
        }
    }

    public static function getExtendedTypes(): iterable
    {
        return [EntityType::class];
    }
}
