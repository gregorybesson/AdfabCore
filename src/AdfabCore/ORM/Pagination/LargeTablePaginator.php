<?php
namespace AdfabCore\ORM\Pagination;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;

class LargeTablePaginator extends DoctrinePaginator
{
    private $largeTableComputedCount;

    public function __construct($query, $fetchJoinCollection = false)
    {
        return parent::__construct($query, $fetchJoinCollection);
    }

    public function count()
    {
        if ($this->largeTableComputedCount === null) {
            $query = $this->getQuery();
            $sql = $query->getSQL();
            if (
                ( substr_count($sql, 'SELECT') == 1 ) &&
                ( strpos($sql,'GROUP BY') === false ) &&
                ( strpos($sql,'SUM(') === false ) &&
                ( strpos($sql,'COUNT(') === false )
            ) {
                $sql = preg_replace(array(
                    '/^SELECT ((?! FROM).+) FROM/ims',
                    '/ORDER BY .*$/ims',
                    '/LIMIT BY .*$/ims'
                ),array(
                    'SELECT COUNT(*) AS count_for_paginator FROM',
                    '',
                    ''
                ),$sql);
                $em = $query->getEntityManager();
                $rsm = new \Doctrine\ORM\Query\ResultSetMapping();
                $rsm->addScalarResult('count_for_paginator', 'count_for_paginator');
                $countQuery = $em->createNativeQuery($sql, $rsm);
                $i = 0;
                foreach( $query->getParameters() as $parameter ) {
                    $v = $parameter->getValue();
                    $countQuery->setParameter($i++, is_object($v) ? $v->getId() : $v);
                }
                $result = $countQuery->getSingleScalarResult();
                $this->largeTableComputedCount = (int) $result;
            }
            else {
                $this->largeTableComputedCount = $this->count();
            }
        }
        return $this->largeTableComputedCount;
    }
}
