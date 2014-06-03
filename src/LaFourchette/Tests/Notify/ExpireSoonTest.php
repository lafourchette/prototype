<?php
namespace LaFourchette\Tests\Notify;

use LaFourchette\Notify\ExpireSoon;
use LaFourchette\Tests\ProphecyTestCase;

class ExpireSoonTest extends ProphecyTestCase
{
    private $vm;
    private $integ;
    private $expireSoon;

    public function setUp()
    {
        parent::setUp();
        $this->vm = $this->getProphecy('LaFourchette\Entity\Vm');

        $this->integ = $this->getProphecy('LaFourchette\Entity\Integ');
        $this->integ->getName()->willReturn('integTest');

        $this->vm->getInteg()->willReturn($this->integ->reveal());

        $this->expireSoon = new ExpireSoon('4');
    }

    public function testGetSubject()
    {
        $this->assertSame('Votre environnement de test integTest va expirer dans 4 h', $this->expireSoon->getSubject($this->vm->reveal()));
    }

    public function testGetContent()
    {
        $str = <<<EOS
Bonjour,

L'environnement de test integTest va expirer dans 4h et par conséquent va être supprimé.

Si vous en avez encore l'utilité veuillez rajouter un nouveau délais dessus.

Cordialement
EOS;
        $this->assertSame($str, $this->expireSoon->getContent($this->vm->reveal()));
    }
}