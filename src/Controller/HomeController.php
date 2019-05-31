<?php

namespace App\Controller;

use App\Service\TemplateRenderer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Manage App's Home page
 *
 * Class HomeController
 * @package App\Controller
 */
class HomeController extends AbstractController
{
    /** @var TemplateRenderer */
    private $renderer;

    /**
     * HomeController constructor
     *
     * @param TemplateRenderer $templateRenderer
     */
    public function __construct(TemplateRenderer $templateRenderer)
    {
        $this->renderer = $templateRenderer;
    }

    /**
     * @Route("/{_locale}",
     *     name="home_index",
     *     methods="GET",
     *     defaults={"_locale"="%default_locale%"},
     *     requirements={"_locale": "%app_locales%"},
     * )
     *
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    final public function index(): Response
    {
        $props = [
            'page' => $this->renderer::HOME_PAGE,
        ];

        return new Response(
            $this->renderer->renderTemplate($props)
        );
    }
}
