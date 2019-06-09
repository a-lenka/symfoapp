<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Request;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class TemplateRenderer
{
    /** @var string HOME_PAGE */
    public const HOME_PAGE = 'home/index.html.twig';

    /** @var string ACCOUNT_PAGE */
    public const ACCOUNT_PAGE = 'account/index.html.twig';

    /** @const string FORM_PAGE */
    public const FORM_PAGE = 'form.html.twig';

    /** @const string LIST_PAGE */
    public const LIST_PAGE = 'list.html.twig';

    /** @const string CONFIRM_PAGE */
    public const CONFIRM_PAGE = 'confirm.html.twig';

    /** @const string DETAILS_PAGE */
    public const DETAILS_PAGE = 'details.html.twig';

    /** @var Environment */
    private $twig;

    /**
     * TemplateRenderer constructor.
     * @param Environment $twig
     */
    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * Check if the given Request is Ajax Request,
     * render the part of template for Ajax Requests.
     * For HTTP Requests the full page will be rendered
     *
     * @param Request $request=null
     * @param array   $props
     *
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    final public function renderTemplate(array $props, Request $request=null): string
    {
        if($request) {
            $template = $request->isXmlHttpRequest() ? $props['part'] : $props['page'];
        } else {
            $template = $props['page'];
        }

        return $this->twig->render($template, $props);
    }
}
