<?php

namespace AdfabCore\Stdlib\Hydrator;

use DateTime;
use Doctrine\Common\Persistence\ObjectManager;

/**
 *
 * @author greg besson
 *  Mapping of an Entity to get value by getId()... Should be taken in charge by Doctrine Hydrator Strategy...
 *  having to fix a DoctrineModule bug :( https://github.com/doctrine/DoctrineModule/issues/180
 *  so i've extended DoctrineHydrator ...
 */
class DoctrineObject extends \DoctrineModule\Stdlib\Hydrator\DoctrineObject
{

    /**
     * Constructor
     *
     * @param ObjectManager $objectManager The ObjectManager to use
     * @param string        $targetClass   The FQCN of the hydrated/extracted object
     * @param bool          $byValue       If set to true, hydrator will always use entity's public API
     */
    public function __construct(ObjectManager $objectManager, $targetClass, $byValue = true)
    {
        parent::__construct($objectManager, $targetClass, $byValue);
    }

    /**
     * Handle various type conversions that should be supported natively by Doctrine (like DateTime)
     *
     * @param  mixed    $value
     * @param  string   $typeOfField
     * @return DateTime
     */
    protected function handleTypeConversions($value, $typeOfField)
    {
        switch ($typeOfField) {
            case 'datetime':
            case 'time':
            case 'date':
                if (is_int($value)) {
                    $dateTime = new DateTime();
                    $dateTime->setTimestamp($value);
                    $value = $dateTime;
                } elseif (is_string($value)) {
                    if (empty($value)) {
                        $value = null;
                    } else {
                        $value = new DateTime($value);
                    }

                }

                break;
            default:
        }

        return $value;
    }
}
