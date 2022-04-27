<?php

namespace App\EventSubscriber;

use App\Request\ValidationRequest;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ValidationSubscriber implements EventSubscriberInterface
{
    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function onKernelControllerArguments(ControllerArgumentsEvent $event)
    {
        $arguments = $event->getArguments();

        /** @var ValidationRequest $validationRequest */
        $validationRequests = array_filter($arguments, fn($argument) => $argument instanceof ValidationRequest);

        $validationRequest = $validationRequests[0] ?? null;

        if ($validationRequest === null) {
            return;
        }

        $validationRequest->setRequest($event->getRequest());

        $rules = $validationRequest->rules();

        $violations = $this->validator->validate(
            $event->getRequest()->request->all(),
            new Collection(
                [
                    'fields' => $rules,
                    'allowExtraFields' => true,
                ]
            )
        );

        if (count($violations) > 0) {
            $messages = [];

            /** @var ConstraintViolationInterface $violation */
            foreach ($violations as $violation) {
                $property = $violation->getPropertyPath();
                $messages[$property][] = $violation->getMessage();
            }

            throw new UnprocessableEntityHttpException(json_encode(['errors' => $messages]));
        }
    }


    public static function getSubscribedEvents()
    {
        return [
            'kernel.controller_arguments' => 'onKernelControllerArguments',
        ];
    }
}
