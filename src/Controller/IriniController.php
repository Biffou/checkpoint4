<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class IriniController extends AbstractController
{
    /**
     * @Route("/Irini", name="Irini_index")
     * @return Response
     */

    public function index() :Response
    {
        return $this->render('index.html.twig');
    }
}

