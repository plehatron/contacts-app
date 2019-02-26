<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class DefaultController extends AbstractController
{
    public function index()
    {
        return $this->render('default/index.html.twig');
    }
}
