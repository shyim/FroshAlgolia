<?php

namespace SwAlgolia\Models;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="swalgolia_backlog")
 */
class Backlog
{

    /**
     * @var integer
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \String
     * @ORM\Column(name="event", type="string", length=255, nullable=false)
     */
    private $event = null;

    /**
     * @var \String
     * @ORM\Column(name="payload", type="text", nullable=false)
     */
    private $payload = null;

    /**
     * @var \DateTime
     * @ORM\Column(name="time", type="datetime", nullable=false)
     */
    private $time = null;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return String
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * @param String $event
     */
    public function setEvent($event)
    {
        $this->event = $event;
    }

    /**
     * @return String
     */
    public function getPayload()
    {
        return $this->payload;
    }

    /**
     * @param String $payload
     */
    public function setPayload($payload)
    {
        $this->payload = $payload;
    }

    /**
     * @return \DateTime
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * @param \DateTime $time
     */
    public function setTime(\DateTime $time)
    {
        $this->time = $time;
    }
}
