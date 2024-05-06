<?php

namespace App\Service;

use Doctrine\Persistence\ManagerRegistry;

class DoctrineMetadataService
{
    private ManagerRegistry $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function getFieldMaxLength(string $className, string $fieldName): int
    {
        $metadata = $this->doctrine->getManager()->getClassMetadata($className);
        return $metadata->getFieldMapping($fieldName)['length'];
    }
}
