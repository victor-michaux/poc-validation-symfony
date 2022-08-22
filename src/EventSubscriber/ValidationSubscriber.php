<?php

namespace App\EventSubscriber;

use App\Exception\FormRequestValidationException;
use App\Request\ValidationRequest;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ValidationSubscriber implements EventSubscriberInterface
{
    private ValidatorInterface $validator;
    private SerializerInterface $serializer;

    public function __construct(ValidatorInterface $validator, SerializerInterface $serializer)
    {
        $this->validator = $validator;
        $this->serializer = $serializer;
    }

    /**
     * @throws FormRequestValidationException
     */
    public function onKernelControllerArguments(ControllerArgumentsEvent $event): void
    {
        $arguments = $event->getArguments();

        /** @var ValidationRequest $validationRequest */
        $validationRequests = array_filter($arguments, fn($argument) => $argument instanceof ValidationRequest);

        $validationRequest = $validationRequests[0] ?? null;

        if ($validationRequest === null) {
            return;
        }

        $this->serializer->deserialize($event->getRequest()->getContent(), get_class($validationRequest), JsonEncoder::FORMAT, [
            AbstractNormalizer::OBJECT_TO_POPULATE => $validationRequest,
        ]);

        $violations = $this->validator->validate($validationRequest);

        if (count($violations) > 0) {
            $errors = [];

            /** @var ConstraintViolationInterface $violation */
            foreach ($violations as $violation) {
                $property = $violation->getPropertyPath();
                $errors[$property][] = $violation->getMessage();
            }

            throw new FormRequestValidationException($errors);
        }
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if ($exception instanceof FormRequestValidationException === false) {
            return;
        }

        $response = new JsonResponse(
            [
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'message' => 'Validation failed.',
                'errors' => $exception->getErrors(),
            ]
        );

        $event->setResponse($response);
    }


    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER_ARGUMENTS => 'onKernelControllerArguments',
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }
}
