<?php

declare(strict_types=1);

namespace Meetup\Entity;

use Ramsey\Uuid\Uuid;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Meetup
 *
 * Attention : Doctrine génère des classes proxy qui étendent les entités, celles-ci ne peuvent donc pas être finales !
 *
 * @package Application\Entity
 * @ORM\Entity(repositoryClass="\Meetup\Repository\MeetupRepository")
 * @ORM\Table(name="meetups")
 */
class Meetup
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=36)
     **/
    private $id;

    /**
     * @ORM\Column(type="string", length=50, nullable=false)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=2000, nullable=false)
     */
    private $description = '';

    /**
     * @ORM\Column(type="string", length=10, nullable=false)
     */
    private $beginningDate;

    /**
     * @ORM\Column(type="string", length=10, nullable=false)
     */
    private $endDate;

    public function __construct(string $title, string $description, string $beginningDate, string $endDate)
    {
        $this->id = Uuid::uuid4()->toString();
        $this->title = $title;
        $this->description = $description;
        $this->beginningDate = $beginningDate;
        $this->endDate = $endDate;
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTitle() : string
    {
        return $this->title;
    }

    public function getDescription() : string
    {
        return $this->description;
    }

    public function getBeginningDate(): string
    {
        return $this->beginningDate;
    }

    public function getEndDate(): string
    {
        return $this->endDate;
    }

    public function setDescription(string $description) : void
    {
        $this->description = $description;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    public function exchangeArray($data)
    {
        $this->id     = (!empty($data['id'])) ? $data['id'] : null;
        $this->title = (!empty($data['title'])) ? $data['title'] : null;
        $this->description  = (!empty($data['description'])) ? $data['description'] : null;
        $this->beginningDate  = (!empty($data['beginningDate'])) ? $data['beginningDate'] : null;
        $this->endDate  = (!empty($data['endDate'])) ? $data['endDate'] : null;
    }
}
