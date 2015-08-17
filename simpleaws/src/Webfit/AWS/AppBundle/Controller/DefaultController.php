<?php

namespace Webfit\AWS\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\DBAL\Query;
use Doctrine\ORM\EntityManagerRepository;
use Webfit\AWS\AppBundle\Entity\Schedule;

class DefaultController extends Controller
{

    protected $container;
    protected $aws = array();
    private   $config = array();
    private   $now;
    private   $em;

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

    public function showScheduleAction(Request $request)
    {
        $result = array();

        $this->init();

        $query = $this->em->createQueryBuilder()
            ->select('s')
            ->from('WebfitAWSAppBundle:Schedule', 's')
            ->where('s.scheduleAt >= :currentTime')
            ->setParameter('currentTime', $this->now->modify('-10 minutes'))
            ->getQuery();

        $schedules = $query->getResult();
        foreach ($schedules as $schedule) {
            $result[] = array(
                'rowId'      => $schedule->getRowId(),
                'asGroup'    => $schedule->getAsGroup(),
                'quantity'   => $schedule->getQuantity(),
                'createAt'   => $schedule->getCreateAt(),
                'scheduleAt' => $schedule->getScheduleAt(),
                'runAt'      => $schedule->getRunAt()
            );
        }

        return new JsonResponse($result);
    }

    /**
     * No 2 schedules should be less than 5 minutes apart
     */
    public function scheduleAtAction(Request $request)
    {
        $this->init();

        $asGroup      = $request->get('asGroup');
        $quantity     = $request->get('quantity');
        $scheduleDate = $request->get('date');
        $scheduleTime = ($request->get('time') == null) ? "0:00am" : $request->get('time');

        if (! $this->checkDuplicateSchedule($asGroup, $scheduleDate, $scheduleTime)) {

            $this->saveSchedule($asGroup, $quantity, $scheduleOn);

            $result = array(
                'returnCode' => 0,
                'message'    => sprintf('Schedule saved (%s %s %s %s)', $asGroup, $quantity, $scheduleDate, $scheduleTime)
            );
        } else {
            $result = array(
                'returnCode' => 1,
                'message'    => 'Requested schedule is too near to existing schedules'
            );
        }

        return new JsonResponse($result);
    }

    public function scheduleNowAction(Request $request)
    {
        $this->init();

        $asGroup    = $request->get('asGroup');
        $quantity   = $request->get('quantity');
        $scheduleOn = clone $this->now;
        $scheduleDate = $scheduleOn->format('Y-m-d');
        $scheduleTime = $scheduleOn->format('g:ia');

        if (! $this->checkDuplicateSchedule($asGroup, $scheduleDate, $scheduleTime)) {
            $this->saveSchedule($asGroup, $quantity, $scheduleOn);
            if ($this->updateAutoScalingGroup()) {
                $result = array(
                    'returnCode' => 0,
                    'message'    => 'Desired number of EC2 intances has been set.'
                );
            } else {
                $result = array(
                    'returnCode' => 2,
                    'message'    => 'Problem encountered in chaning auto-scaling group.'
                );
            }
        } else {
            $result = array(
                'returnCode' => 1,
                'message'    => 'Requested schedule is too near to existing schedules'
            );
        }

        return new JsonResponse($result);
    }

    private function updateAutoScalingGroup()
    {
        return true;
    }

    private function checkDuplicateSchedule($asGroup, $scheduleDate, $scheduleTime)
    {

        $scheduleOn = new \DateTime(sprintf("%s %s", $scheduleDate, $scheduleTime));

        $lowerBound = clone $scheduleOn;
        $upperBound = clone $scheduleOn;
        $lowerBound->modify('-5 minutes');
        $upperBound->modify('+5 minutes');

        $query = $this->em->createQueryBuilder()
            ->select('s')
            ->from('WebfitAWSAppBundle:Schedule', 's')
            ->where('s.scheduleAt >= :lowerBound AND s.scheduleAt <= :upperBound AND s.asGroup = :asGroup')
            ->setParameter('lowerBound', $lowerBound)
            ->setParameter('upperBound', $upperBound)
            ->setParameter('asGroup',    $asGroup)
            ->getQuery();

        $schedules = $query->getResult();

        return (count($schedules) > 0) ? true : false;
    }

    private function saveSchedule($asGroup, $quantity, $scheduleOn)
    {
        $runAt = clone $scheduleOn;
        $runAt->modify(Schedule::ADVANCED_BY);

        $schedule = new Schedule;
        $schedule->setRowId(uniqid());
        $schedule->setAsGroup($asGroup);
        $schedule->setQuantity($quantity);
        $schedule->setCreateAt($this->now);
        $schedule->setScheduleAt($scheduleOn);
        $schedule->setRunAt($runAt);

        $this->em->persist($schedule);
        $this->em->flush();
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

    private function init() {
        $this->now = new \DateTime;
        $this->em = $this->getDoctrine()->getEntityManager();
        return true;
    }
}
