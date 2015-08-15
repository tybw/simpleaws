<?php

namespace Webfit\AWS\AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class DefaultController extends Controller
{

    protected $aws = array();
    private   $config = array();
    protected $container;


    public function mainAction(Request $request)
    {
        $this->config_aws();
        return $this->render('WebfitAWSAppBundle:Main:index.html.twig', []);
    }

    public function ec2StatusAction(Request $request, $profile)
    {
        $this->config_aws();
        $result = array();

        if (isset($this->aws[$profile])) {
            $result = $this->aws[$profile]->ec2InstanceStatus();
        }

        return new JsonResponse($result);
    }

    public function ec2Action(Request $request, $profile)
    {
        $this->config_aws();
        $result = array();

        if (isset($this->aws[$profile])) {
            $result = $this->aws[$profile]->ec2InstanceDetails();
        }
        
        return new JsonResponse($result);
    }

    private function config_aws()
    {

        $profiles = $this->container->getParameter('aws_profile');
        $regions  = $this->container->getParameter('aws_region');
        $versions = $this->container->getParameter('aws_version');

        for ($i = 0; $i < count($profiles); $i++) {
            $this->config[$profiles[$i]] = array(
                'profile' => $profiles[$i],
                'region'  => $regions[$i],
                'version' => $versions[$i]
            );
            $this->aws[$profiles[$i]] = $this->get('awsService');
            $this->aws[$profiles[$i]]->setProfile($this->config[$profiles[$i]]);
        }
    }
}
