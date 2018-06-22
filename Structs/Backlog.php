<?php declare(strict_types=1);

namespace FroshAlgolia\Structs;

/**
 * Class Backlog.
 */
class Backlog extends Struct implements StructInterface
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $event;

    /**
     * @var string
     */
    private $payload;

    /**
     * @var \DateTime
     */
    private $time;

    /**
     * @param string   $event
     * @param string   $payload
     * @param string   $time
     * @param null|int $id
     */
    public function __construct($event, $payload, $time = 'now', $id = null)
    {
        $this->id = $id;
        $this->event = $event;
        $this->payload = $payload;
        $this->time = new \DateTime($time);
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * @return string
     */
    public function getPayload()
    {
        return $this->payload;
    }

    /**
     * @return \DateTime
     */
    public function getTime()
    {
        return $this->time;
    }
}
