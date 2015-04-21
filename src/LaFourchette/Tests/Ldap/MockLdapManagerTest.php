<?php

namespace LaFourchette\Tests\Ldap;

use LaFourchette\Ldap\MockLdapManager;

class MockLdapManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testGetUserInfo()
    {
        $dut = new MockLdapManager();
        $user = $dut->getUserInfo('foobar');

        $this->assertInstanceOf('LaFourchette\Entity\User', $user);
        $this->assertSame('Anonymous', $user->getUsername());
    }
} 