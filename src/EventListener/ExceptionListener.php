<?php

namespace App\EventListener;

use ErrorException;
use Symfony\Component\HttpFoundation\Response;
use JMS\Serializer\Exception\NotAcceptableException;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ExceptionListener
{
    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();

        if (
            $exception instanceof NotAcceptableException ||
            $exception instanceof NotFoundHttpException ||
            $exception instanceof ErrorException
        ) {
            $message = "Bad request, please use our documentation : https://bilemo.fabienvernieres.com";
            $statusCode = Response::HTTP_NOT_FOUND;
        }

        $event->setResponse((new Response())->setContent($message)->setStatusCode($statusCode));
    }
}