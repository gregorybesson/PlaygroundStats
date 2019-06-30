<?php

namespace PlaygroundStats\Mapper;

class Card extends AbstractMapper
{

    public function getEntityRepository()
    {
        if (null === $this->er) {
            $this->er = $this->em->getRepository('PlaygroundStats\Entity\Card');
        }

        return $this->er;
    }
}
