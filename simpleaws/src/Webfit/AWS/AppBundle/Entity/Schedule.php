<?php

namespace Webfit\AWS\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Schedule
 *
 * @ORM\Table(name="schedule",
 *     indexes={
 *         @ORM\Index(name="schedule_idx", columns={"schedule_at"})},
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="unique_idx", columns={"schedule_at"})})
 * @ORM\Entity
 *
 */
class Schedule
{
    /**
     * @var integer
     *
     * @ORM\Column(name="rowid", type="integer")
     * @ORM\Id
     */
    private $rowid;

    /**
     * Quantity
     *
     * @ORM\Column(type="integer", nullable=false)
     * @Assert\Range(
     *      min = 0,
     *      max = 255,
     *      minMessage = "Desired number of instance should not be negative",
     *      maxMessage = "No more than 255 instances is allowed"
     * )
     */
    private $quantity;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="create_at", type="datetime", nullable=false)
     */
    private $createAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="schedule_at", type="datetime", nullable=false)
     */
    private $scheduleAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="run_at", type="datetime", nullable=true, options={"default" = null})
     */
    protected $run_at;

}
