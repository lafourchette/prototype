<?php
namespace LaFourchette\Service;

use LaFourchette\Entity\User;
use LaFourchette\Entity\UserNotify;
use LaFourchette\Entity\Vm;
use LaFourchette\Manager\VmManager;
use LaFourchette\Provider\DataProvider;
use LaFourchette\Writer\FileWriter;
use LaFourchette\Writer\FileWriterFactory;

/**
 * Class used to persist data
 *
 * @author Mickael
 */
class DataAccessService
{
    /** @var DataProvider */
    private $dataProvider;

    /** @var FileWriter */
    private $fileWriter;

    public function __construct(DataProvider $dataProvider, FileWriterFactory $fileWriterFactory, $serializerFormat)
    {
        $this->dataProvider = $dataProvider;
        $this->fileWriter = $fileWriterFactory->getWriter($serializerFormat);
    }

    /**
     * Loads an object by its ID
     *
     * @param $object
     * @param $id
     *
     * @return User|UserNotify|Vm|null
     */
    public function load($object, $id)
    {
        $data = $this->dataProvider->getData($object);
        $getterId = $this->getGetterId($object);
        foreach ($data as $obj) {
            if ($id == $obj->$getterId()) {
                return $obj;
            }
        }

        return null;
    }

    /**
     * @param $object
     * @param array $criteria
     *
     * @return \LaFourchette\Entity\User|\LaFourchette\Entity\UserNotify|\LaFourchette\Entity\Vm|null
     */
    public function loadOneBy($object, array $criteria = [])
    {
        $data = $this->dataProvider->getData($object);
        foreach ($data as $dataObject) {
            $return = true;
            foreach ($criteria as $property => $value) {
                $getter = 'get' . ucfirst($property);
                if (!method_exists($dataObject, $getter) || $dataObject->$getter() != $value) {
                    $return = false;
                }
            }

            if ($return) {
                return $dataObject;
            }
        }

        return null;
    }

    public function loadBy($object, array $criteria = [], array $order = null)
    {
        $data = $this->dataProvider->getData($object);
        $returnData = [];
        foreach ($data as $dataObject) {
            foreach ($criteria as $property => $values) {
                $getter = 'get' . ucfirst($property);
                if (method_exists($dataObject, $getter) && in_array($dataObject->$getter(), $values)) {
                    $returnData[] = $dataObject;
                }
            }
        }

        return $returnData;
    }

    public function findAll($object)
    {
        return $this->dataProvider->getData($object);
    }

    public function save($object)
    {
        $getterId = $this->getGetterId($object);
        $fileName = $this->dataProvider->getFileName($object);

        $orgData = $this->dataProvider->getData($object);
        $data = array_filter($orgData, function($obj) use ($getterId, $object) {
                return $obj->$getterId() != $object->$getterId();
            });
        $data[] = $object;

        $this->fileWriter->write($fileName, $data);

        $this->dataProvider->loadData();
    }

    /**
     * Returns Vms and exclude the ones with the given status(es)
     *
     * @param array $statuses
     * @param bool $getOnlyIds
     *
     * @return array
     */
    public function getVmsWhereStatusNotIn(array $statuses, $getOnlyIds = false)
    {
        /** @var Vm[] $vms */
        $vms = $this->dataProvider->getVms();
        $returnVms = [];
        foreach ($vms as $vm) {
            if (!in_array($vm->getStatus(), $statuses)) {
                $returnVms[] = $getOnlyIds ? $vm->getIdVm() : $vm;
            }
        }

        return array_unique($returnVms);
    }

    public function getNextId($type)
    {
        $data = [];
        $getterId = $this->getGetterId($type);
        if ('user' == $type) {
            $data = $this->dataProvider->getUsers();
        }

        if ('vm' == $type) {
            $data = $this->dataProvider->getVms();
        }

        if (count($data) > 0) {
            usort($data, function($a, $b) use ($getterId) {
                return strcmp($a->$getterId(), $b->$getterId());
            });
            $lastData = end($data);

            return $lastData->$getterId() + 1;
        }

        return 1;
    }

    private function getGetterId($data)
    {
        if ('vm' == $data || $data instanceof Vm || $data instanceof VmManager) {
            return 'getIdVm';
        }

        return 'getIdUser';
    }
}
