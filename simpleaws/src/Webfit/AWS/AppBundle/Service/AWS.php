<?php
namespace Webfit\AWS\AppBundle\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Aws\S3\S3Client;

class AWS {

    public function __construct(EntityManager $em, ContainerInterface $container)
    {
        $this->container = $container;
        $this->em = $em;

$s3 = new S3Client([
    'version' => 'latest',
    'region'  => 'ap-southeast-2a'
]);
var_dump($s3);
    }
}
