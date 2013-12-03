<?php

namespace Ifgm\ACLInterfaceBundle\Repository\Helper;

use Doctrine\ORM\Query;

/**
 * IndexByTrait
 * Trait to add $this->setIndexBy($query, $fieldName) to a repository
 *
 * @author Jérémy Hubert <jeremy.hubert@ifgm.fr>
 */
trait IndexByTrait
{
    /**
     * Return users indexed by id
     *
     * @param Query  $query
     * @param string $fieldName
     *
     * @return \Doctrine\ORM\AbstractQuery
     */
    public function setIndexBy(Query $query, $fieldName)
    {
        if (!strpos($query->getDQL(), 'WHERE')) {

            return $query->setDQL($query->getDQL().' INDEX BY '.$fieldName);
        }

        return $query->setDQL(str_replace('WHERE', 'INDEX BY '.$fieldName.' WHERE', $query->getDQL()));
    }
}