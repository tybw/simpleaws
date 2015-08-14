<?php

namespace Webfit\AWS\AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function mainAction(Request $request)
    {
        $this->get('awsService');

        return $this->render('WebfitAWSAppBundle:Main:index.html.twig', []);
    }
}
