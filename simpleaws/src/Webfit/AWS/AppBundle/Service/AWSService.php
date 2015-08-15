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
use Aws\Api\DateTimeResult;
use Guzzle\Service\Resource;

class AWSService
{

    public $aws;
    public $ec2;
    public $s3;
    public $as;
    public $elb;
    public $cf;
    public $rds;

    public function __construct(EntityManager $em, ContainerInterface $container)
    {
        $this->container = $container;
        $this->em = $em;
    }

    public function setProfile($config)
    {

        $this->aws = new Sdk($config);

        if ($this->aws) {
            $this->ec2 = $this->aws->createEc2();
            $this->s3  = $this->aws->createS3();
            $this->as  = $this->aws->createAutoScaling();

            return true;
        } else {
            return false;
        }
    }

    public function ec2InstanceStatus()
    {
        $response = $this->ec2->describeInstanceStatus(array(
            'DryRun' => false
            /*
            'InstanceIds' => array()
            'Filters' => array(
                array(
                    'Name' => 'string',
                    'Values' => array('string', ... ),
                ),
            ),
            'NextToken' => 'string',
            'MaxResults' => integer,
            */
        ));

        $result = array();
        $groups = $response->get('InstanceStatuses');

        foreach ($groups as $group) {
            $result[$group['InstanceId']] = $group['InstanceState']['Name'];
        }

        return $result;
    }

    public function ec2InstanceDetails()
    {
        $response = $this->ec2->describeInstances(array(
            'DryRun' => false
        ));

        $result = array();
        $groups = $response->get('Reservations');
        foreach ($groups as $group) {
            $tags = array();
            foreach ($group['Instances'][0]['Tags'] as $tag) {
                if (strcmp($tag['Key'], 'aws:autoscaling:groupName') == 0) {
                    $tags[] = $tag['Value'];
                }
            }

            $instanceId = $group['Instances'][0]['InstanceId'];
            $result[$instanceId] = array(
                'InstanceId' => $instanceId,
                'ImageId'    => $group['Instances'][0]['ImageId'],
                'Status'     => $group['Instances'][0]['State']['Name'],
                'PubicIp'    => $group['Instances'][0]['PublicIpAddress'],
                'PrivateIp'  => $group['Instances'][0]['PrivateIpAddress'],
                'LaunchAt'   => $group['Instances'][0]['LaunchTime']->format('Y-m-d H'),
                'Tag'        => join(',', $tags)
            );
        }

        return $result;
    }

}
