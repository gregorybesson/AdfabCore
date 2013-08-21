<?php
namespace AdfabCore\Stdlib\Hydrator\Strategy;

use Zend\Stdlib\Hydrator\Strategy\StrategyInterface;
use DoctrineModule\Stdlib\Hydrator\Strategy\AbstractCollectionStrategy;

// This class fill a gap in Doctrine Hydrator : When the attribute is an object, we have to call getId()
class ObjectStrategy extends AbstractCollectionStrategy implements StrategyInterface
{
    public function extract($value)
    {
        if (is_numeric($value) || $value === null) {
            return $value;
        }

        return $value->getId();
    }

    public function hydrate($value)
    {
        return $value;
    }
}
