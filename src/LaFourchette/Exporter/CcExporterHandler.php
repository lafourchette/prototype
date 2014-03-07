<?php

namespace LaFourchette\Exporter;

use LaFourchette\Formatter\CcXmlExporterFormatter;
use LaFourchette\Manager\VmManager;
use LaFourchette\Model\Prototype;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer;

class CcExporterHandler
{
    /**
    * $vms
    * @var Doctrine\Common\Collections\Collection
    */
    protected $vms = null;

    public function __construct(VmManager $vmManager)
    {
        $this->vms = $vmManager->loadVm();
    }

    /**
     * export
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function export($type = 'xml')
    {

        if(!in_array($type, array('xml', 'json'))) {
            throw new InvalidArgumentException(sprintf('this type "%s" does not exist', $type));
        }

        $encoders = array(new XmlEncoder('Projects'), new JsonEncoder());
        $normalizers = array(new GetSetMethodNormalizer());
        $serializer = new Serializer($normalizers, $encoders);

        $data = array();
        foreach($this->vms as $vm) {
            $prototype = $this->createPrototype($vm);
            $data['Project'] = array(
                    '@activity' => $prototype->getActivity(),
                    '@lastBuildTime' => $prototype->getLastBuildTime(),
                    '@lastBuildLabel' => $prototype->getLastBuildLabel(),
                    '@lastBuildStatus' => $prototype->getLastBuildStatus(),
                    '@name' => $prototype->getName(),
                    '@webUrl' => $prototype->getWebUrl()
                );
        }

        $response = new Response($serializer->serialize($data, $type)
            ,200
            ,array('Content-Type' => sprintf('application/%s;charset=UTF-8', $type))
        );

        return $response;
    }

    private function createPrototype($vm)
    {
        $prototype = new Prototype();
        $prototype
                  ->setActivity($vm->getCcActivity())
                  ->setLastBuildTime($vm->getUpdateDt()->format('Y-m-d\TH\\:i\\:s\Z'))
                  ->setLastBuildLabel((string) $vm->getIdVm())
                  ->setLastBuildStatus($vm->getCcStatus())
                  ->setName((string) $vm)
                  ->setWebUrl('');

        return $prototype;
    }
}
