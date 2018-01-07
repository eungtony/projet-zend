<?php

declare(strict_types=1);

namespace Meetup\Repository;

use Meetup\Entity\Meetup;
use Doctrine\ORM\EntityRepository;

final class MeetupRepository extends EntityRepository
{

    public function persist($meetup) : void
    {
        $this->getEntityManager()->persist($meetup);
        $this->getEntityManager()->flush($meetup);
    }

    public function remove($meetup) : void
    {
        $this->getEntityManager()->remove($meetup);
        $this->getEntityManager()->flush($meetup);
    }

    public function createMeetup(string $name, string $description, string $beginningDate, string $endDate)
    {
        return new Meetup($name, $description, $beginningDate, $endDate);
    }
}
