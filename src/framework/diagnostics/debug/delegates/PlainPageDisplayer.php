<?php namespace nyx\framework\diagnostics\debug\delegates;

    // External dependencies
    use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
    use Symfony\Component\HttpFoundation\Response;
    use nyx\diagnostics\debug as base;

    /**
     * Plain Page Displayer
     *
     * @package     Nyx\Framework
     * @version     0.0.1
     * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
     * @copyright   2012-2014 Nyx Dev Team
     * @link        http://docs.muyo.pl/nyx/framework/index.html
     */

    class PlainPageDisplayer extends Displayer
    {
        /**
         * {@inheritDoc}
         */

        public function handle(base\Inspector $inspector)
        {
            // Get the Exception from the Inspector.
            $exception = $inspector->getException();

            // Determine which headers and status code to respond with depending on what kind of Exception
            // we're dealing with.
            $status  = $exception instanceof HttpExceptionInterface ? $exception->getStatusCode() : 500;
            $headers = $exception instanceof HttpExceptionInterface ? $exception->getHeaders() : [];

            ob_start();

            require $this->getResourcesPath().'/plain-page-template.php';

            return new Response(ob_get_clean(), $status, $headers);
        }
    }