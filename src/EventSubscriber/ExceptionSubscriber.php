<?php


namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Psr\Log\LoggerInterface;

class ExceptionSubscriber implements EventSubscriberInterface
{
    public function __construct(private LoggerInterface $logger) {}

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        $this->logger->error(sprintf('Exception: %s with message: %s', get_class($exception), $exception->getMessage()));

        $statusCode = $exception instanceof HttpExceptionInterface ? $exception->getStatusCode() : 500;

        $json = json_encode([
            'error' => true,
            'message' => $exception->getMessage(),
            'code' => $statusCode,
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        $response = new JsonResponse($json, $statusCode, [], true);

//        $response = new JsonResponse([
//            'error' => true,
//            'message' => $exception->getMessage(),
//            'code' => $statusCode,
//        ], $statusCode);

        $event->setResponse($response);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'kernel.exception' => 'onKernelException',
        ];
    }
}