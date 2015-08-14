<?php
namespace Webfit\AWS\AppBundle\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Aws\Sdk;
use Aws\Ec2\Ec2Client;
use Aws\S3\S3Client;
use Aws\Rds\RdsClient;
use Aws\AutoScaling\AutoScalingClient;
use Aws\ElasticLoadBalancing\ElasticLoadBalancingClient;
use Aws\CloudFront\CloudFrontClient;

class AWSService {

    public function __construct(EntityManager $em, ContainerInterface $container, $config = null)
    {
        $this->container = $container;
        $this->em = $em;

        if ($config != null) {
            return null;
        }

        $aws       = new Sdk($config);
        $ec2       = $aws->createEc2();
        $s3        = $aws->createS3();
        $autoscale = $aws->createAutoScaling();
    }
}
