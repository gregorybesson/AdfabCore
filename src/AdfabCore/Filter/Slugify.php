<?php
namespace AdfabCore\Filter;

use Traversable;

/**
 * @category   Zend
 * @package    Zend_Filter
 */
class Slugify extends \Zend\Filter\AbstractUnicode
{
    /**
     * @var array
     */
    protected $options = array(
        'encoding' => null,
    );

    /**
     * Constructor
     *
     * @param string|array|Traversable $encodingOrOptions OPTIONAL
     */
    public function __construct($encodingOrOptions = null)
    {
        if ($encodingOrOptions !== null) {
            if (!static::isOptions($encodingOrOptions)) {
                $this->setEncoding($encodingOrOptions);
            } else {
                $this->setOptions($encodingOrOptions);
            }
        }
    }

    /**
     * Defined by Zend\Filter\FilterInterface
     *
     * Returns the string $value, converting characters to lowercase as necessary
     *
     * @param  string $value
     * @return string
     */
    public function filter($value)
    {
        // TODO : Traiter UTF8 et le remplissage depuis un autre champ si vide
        $value = strtolower($value);
        $value = str_replace("'", '', $value);
        $value = preg_replace('([^a-zA-Z0-9_-]+)', '-', $value);
        $value = preg_replace('(-{2,})', '-', $value);
        $value = trim($value, '-');

        /*if ($this->options['encoding'] !== null) {
            return mb_strtolower((string) $value,  $this->options['encoding']);
        }*/

        return $value;
    }
}
