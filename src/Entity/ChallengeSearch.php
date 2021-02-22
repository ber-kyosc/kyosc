<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;

class ChallengeSearch
{
    /**
     * @var string|null
     * @Assert\Regex("/^([0-2][0-9]|(3)[0-1])(\/)(((0)[0-9])|((1)[0-2]))(\/)\d{4}( - )([0-2][0-9]|(3)[0-1])(\/)(((0)[0-9])|((1)[0-2]))(\/)\d{4}$/")
     */
    private $dateStart;

    /**
     * @var object|null
     */
    private $sport;

    /**
     * @var string|null
     * @Assert\Regex("/\d+\ +\-\ +\d+/")
     */
    private $distance;

    /**
     * @var string|null
     * @Assert\Regex("/\d+\ +\-\ +\d+/")
     */
    private $participants;

    /**
     * @return string|null
     */
    public function getDateStart(): ?string
    {
        return $this->dateStart;
    }

    /**
     * @param string|null $dateStart
     */
    public function setDateStart(?string $dateStart): void
    {
        $this->dateStart = $dateStart;
    }

    /**
     * @return object|null
     */
    public function getSport(): ?object
    {
        return $this->sport;
    }

    /**
     * @param object|null $sport
     */
    public function setSport(?object $sport): void
    {
        $this->sport = $sport;
    }

    /**
     * @return string|null
     */
    public function getDistance(): ?string
    {
        return $this->distance;
    }

    /**
     * @param string|null $distance
     */
    public function setDistance(?string $distance): void
    {
        $this->distance = $distance;
    }

    /**
     * @return string|null
     */
    public function getParticipants(): ?string
    {
        return $this->participants;
    }

    /**
     * @param string|null $participants
     */
    public function setParticipants(?string $participants): void
    {
        $this->participants = $participants;
    }
}
