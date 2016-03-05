<?php namespace nyx\framework\diagnostics\debug\delegates;

    // External dependencies
    use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
    use Symfony\Component\HttpFoundation\JsonResponse;
    use nyx\diagnostics as base;

    /**
     * JSON Displayer
     *
     * @package     Nyx\Framework
     * @version     0.0.1
     * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
     * @copyright   2012-2014 Nyx Dev Team
     * @link        http://docs.muyo.pl/nyx/framework/index.html
     */

    class JsonDisplayer implements base\debug\interfaces\Delegate
    {
        /**
         * {@inheritDoc}
         */

        public function handle(base\debug\Inspector $inspector)
        {
            // Get the Exception from the Inspector.
            $exception = $inspector->getException();

            // Determine which headers and status code to respond with depending on what kind of Exception
            // we're dealing with.
            $status  = $exception instanceof HttpExceptionInterface ? $exception->getStatusCode() : 500;
            $headers = $exception instanceof HttpExceptionInterface ? $exception->getHeaders() : [];

            return new JsonResponse(['error' => base\Debug::exceptionToArray($exception)], $status, $headers);
        }
    }