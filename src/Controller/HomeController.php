<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Manage App's Home page
 *
 * Class HomeController
 * @package App\Controller
 */
class HomeController extends AbstractController
{
    /**
     * @Route("/",
     *     name="home_index",
     *     methods="GET"
     * )
     *
     * @return Response
     */
    public function index(): Response
    {
        return $this->render('home/index.html.twig');
    }
}
