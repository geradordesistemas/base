<?php

namespace App\Application\Project\SecurityUserBundle\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ExceptionListener
{
    public function onKernelException(ExceptionEvent $event): void
    {
        // You get the exception object from the received event
        $exception = $event->getThrowable();
        // Get incoming request
        $request   = $event->getRequest();


        $contentType = $request->query->get('Content-Type');
        if(!$contentType)
            $contentType = $request->headers->get('Content-Type');

        // Check if it is a rest api request
        if ('application/json' === $contentType)
        {

            //dd($exception);
            // Customize your response object to display the exception details
            $response = new JsonResponse([
                'message'       => $exception->getMessage(),
                //'code'          => $exception->getPrevious()->getCode(),
                //'traces'        => $exception->getTrace()
            ]);



            if ($exception instanceof HttpExceptionInterface) {

                $response->setStatusCode($exception->getStatusCode());
                $response->headers->replace($exception->getHeaders());
                $request->headers->set('Content-Type', 'application/json');

            } else {
                $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            //dd($response);

            // sends the modified response object to the event
            $event->setResponse($response);
        }
    }
}