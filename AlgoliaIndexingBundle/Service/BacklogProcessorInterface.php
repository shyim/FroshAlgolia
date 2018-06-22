<?php

namespace FroshAlgolia\AlgoliaIndexingBundle\Service;

interface BacklogProcessorInterface
{
    /**
     * @param $backlogs
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function add($backlogs) : void;
}