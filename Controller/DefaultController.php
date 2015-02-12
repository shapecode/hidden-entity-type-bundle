<?php

namespace Glifery\EntityHiddenTypeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('GliferyEntityHiddenTypeBundle:Default:index.html.twig', array('name' => $name));
    }
}
