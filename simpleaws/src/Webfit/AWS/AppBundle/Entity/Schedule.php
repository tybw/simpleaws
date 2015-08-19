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
    const ADVANCED_BY = '-3 hours';
    const DONE = 1;
    const NOTDONE = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="rowid", type="string")
     * @ORM\Id
     */
    private $rowid;

    /**
     * Auto-scaling group
     *
     * @var string
     *
     * @ORM\Column(name="as_group", type="string", nullable=false)
     */
    private $asGroup;

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

    /**
     * @var bool
     * 
     * @ORM\Column(name="done", type="boolean", nullable=false, options={"default" = null})
     */
    protected $done;

    /**
     * Set rowid
     *
     * @param integer $rowid
     * @return Schedule
     */
    public function setRowid($rowid)
    {
        $this->rowid = $rowid;

        return $this;
    }

    /**
     * Get rowid
     *
     * @return integer 
     */
    public function getRowid()
    {
        return $this->rowid;
    }

    /**
     * Set asGroup
     *
     * @param string $asGroup
     * @return Schedule
     */
    public function setAsGroup($asGroup)
    {
        $this->asGroup = $asGroup;

        return $this;
    }

    /**
     * Get asGroup
     *
     * @return string 
     */
    public function getAsGroup()
    {
        return $this->asGroup;
    }

    /**
     * Set quantity
     *
     * @param integer $quantity
     * @return Schedule
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return integer 
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set createAt
     *
     * @param \DateTime $createAt
     * @return Schedule
     */
    public function setCreateAt($createAt)
    {
        $this->createAt = $createAt;

        return $this;
    }

    /**
     * Get createAt
     *
     * @return \DateTime 
     */
    public function getCreateAt()
    {
        return $this->createAt;
    }

    /**
     * Set scheduleAt
     *
     * @param \DateTime $scheduleAt
     * @return Schedule
     */
    public function setScheduleAt($scheduleAt)
    {
        $this->scheduleAt = $scheduleAt;

        return $this;
    }

    /**
     * Get scheduleAt
     *
     * @return \DateTime 
     */
    public function getScheduleAt()
    {
        return $this->scheduleAt;
    }

    /**
     * Set run_at
     *
     * @param \DateTime $runAt
     * @return Schedule
     */
    public function setRunAt($runAt)
    {
        $this->run_at = $runAt;

        return $this;
    }

    /**
     * Get run_at
     *
     * @return \DateTime 
     */
    public function getRunAt()
    {
        return $this->run_at;
    }

    /**
     * Set done
     *
     * @param boolean $done
     * @return Schedule
     */
    public function setDone($done)
    {
        $this->done = $done;

        return $this;
    }

    /**
     * Get done
     *
     * @return boolean 
     */
    public function getDone()
    {
        return $this->done;
    }
}
