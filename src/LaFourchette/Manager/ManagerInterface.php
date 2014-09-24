<?php

namespace LaFourchette\Manager;

/**
 * Description of ManagerInterface
 *
 * @author gcavana
 */
interface ManagerInterface
{

    /**
     * Finds one object by id
     *
     * @param int $id The id of the object
     *
     * @return object|null
     */
    public function load($id);

    /**
     * Finds one object by an array of criteria
     *
     * @param array $criteria The array of criteria
     *
     * @return object|null
     */
    public function loadOneBy(array $criteria);

    /**
     * Finds many objects by an array of criteria
     *
     * @param array $criteria The array of criteria
     * @param array $order Array of order (ex : array('myField' => 'DESC'))
     *
     * @return array
     */
    public function loadBy(array $criteria, array $order = null);

    /**
     * Finds all objects
     *
     * @return array
     */
    public function loadAll();

    /**
     * This method can be useful in some case to save data without persist it (Doctrine by example)
     *
     * return void
     */
    public function flush($entity);
}
