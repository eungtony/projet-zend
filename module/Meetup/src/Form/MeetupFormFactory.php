<?php

declare(strict_types=1);

namespace Meetup\Controller;

use Doctrine\ORM\EntityManager;
use Meetup\Entity\Organization;
use Meetup\Form\MeetupForm;
use Psr\Container\ContainerInterface;

final class MeetupFormFactory
{
    public function __invoke(ContainerInterface $container)
    {

    }
}
