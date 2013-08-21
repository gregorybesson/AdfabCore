<?php
/**
 * @package     AdfabCore
 */
 
namespace AdfabCore\Analytics;

class CustomVar
{
    protected $id;
    protected $name;
    protected $value;
    protected $optScope;

    public function __construct ($id, $name, $value, $optScope = null)
    {
        $this->setId($id);
        $this->setName($name);
        $this->setValue($value);
		
		if (null !== $optScope) {
            $this->setOptScope($optScope);
        }
    }

    public function getId ()
    {
        return $this->id;
    }

    public function setId ($id)
    {
        $this->id = $id;
    }

    public function getName ()
    {
        return $this->name;
    }

    public function setName ($name)
    {
        $this->name = $name;
    }

    public function getValue ()
    {
        return $this->value;
    }

    public function setValue ($value)
    {
        $this->value = $value;
    }

    public function getOptScope ()
    {
        return $this->optScope;
    }

    public function setOptScope ($optScope)
    {
        $this->optScope = $optScope;
    }
}
