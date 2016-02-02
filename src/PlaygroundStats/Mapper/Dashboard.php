<?php

namespace PlaygroundStats\Mapper;

class Dashboard extends AbstractMapper
{

    public function getEntityRepository()
    {
        if (null === $this->er) {
            $this->er = $this->em->getRepository('PlaygroundStats\Entity\Dashboard');
        }

        return $this->er;
    }
}
