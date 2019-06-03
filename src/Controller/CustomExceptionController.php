<?php

namespace App\Controller;

use Symfony\Bundle\TwigBundle\Controller\ExceptionController;
use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class CustomExceptionController
 * @package App\Controller
 */
class CustomExceptionController extends ExceptionController
{
    /**
     * Converts an Exception to a Response.
     *
     * A "showException" request parameter can be used to force display of an error page (when set to false) or
     * the exception page (when true). If it is not present, the "debug" value passed into the constructor will
     * be used.
    /**
     * @param Request                   $request
     * @param FlattenException          $exception
     * @param DebugLoggerInterface|null $logger
     *
     * @return Response
     *
     * @throws \InvalidArgumentException When the exception template does not exist
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    final public function showAction(
        Request              $request,
        FlattenException     $exception,
        DebugLoggerInterface $logger = null
    ): Response {
        $currentContent = $this->getAndCleanOutputBuffering(
            $request->headers->get('X-Php-Ob-Level', -1)
        );
        $showException  = $request->attributes->get('showException', $this->debug);
        $code           = $exception->getStatusCode();

        if($request->isXmlHttpRequest()) {
            if($code === 403) {
                $template = 'security/_form-login.html.twig';

                $response = new Response($this->twig->render($template, [
                    'forbidden_message' => 'We are sorry, but you do not have access to this page. Please, login',
                ]), 200, [
                    'Content-Type' => $request->getMimeType($request->getRequestFormat())
                        ?: 'text/html'
                ]);
            }

            if($code === 500 && stripos($request->getPathInfo(), 'register')) {
                $template = 'bundles/TwigBundle/Exception/_error.html.twig';
                //$template = 'security/_form-register.html.twig';

                $response = new Response($this->twig->render($template, [
                    'error_message' => 'We are sorry, but something went wrong. Please, reload the page and try again with another credentials',
                ]), 200, [
                    'Content-Type' => $request->getMimeType($request->getRequestFormat())
                        ?: 'text/html'
                ]);
            } else {
                $template = 'bundles/TwigBundle/Exception/_error.html.twig';

                $response = new Response($this->twig->render($template, [
                    'error_message' => 'We are sorry, but something went wrong. Please reload the page and try again',
                ]), 200, [
                    'Content-Type' => $request->getMimeType($request->getRequestFormat())
                        ?: 'text/html'
                ]);
            }
        } else {
            $response = new Response($this->twig->render(
                (string) $this->findTemplate($request, $request->getRequestFormat(), $code, $showException),
                [
                    'status_code' => $code,
                    'status_text' => isset(Response::$statusTexts[$code]) ? Response::$statusTexts[$code] : '',
                    'exception'   => $exception,
                    'logger'      => $logger,
                    'currentContent' => $currentContent,
                ]
            ), 200, ['Content-Type' => $request->getMimeType($request->getRequestFormat()) ?: 'text/html']);
        }

        $response->headers->set('X-Target-URL', $request->getRequestUri());

        return $response;
    }


    /**
     * @param Request $request
     * @param string  $format
     * @param int     $code          An HTTP response status code
     * @param bool    $showException
     *
     * @return string
     */
    final protected function findTemplate(Request $request, $format, $code, $showException): string
    {
        $name = $showException ? 'exception' : 'error';

        if ($showException && 'html' == $format) {
            $name = 'exception_full';
        }

        // For error pages, try to find a template for the specific HTTP status code and format
        if (!$showException) {
            $template = sprintf('@Twig/Exception/%s%s.%s.twig', $name, $code, $format);
            if ($this->templateExists($template)) {
                return $template;
            }
        }

        // try to find a template for the given format
        $template = sprintf('@Twig/Exception/%s.%s.twig', $name, $format);
        if ($this->templateExists($template)) {
            return $template;
        }

        // default to a generic HTML exception
        $request->setRequestFormat('html');
        return sprintf('@Twig/Exception/%s.html.twig', $showException ? 'exception_full' : $name);
    }

}
