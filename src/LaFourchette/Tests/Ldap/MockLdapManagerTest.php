<?php

namespace LaFourchette\Tests\Ldap;

use LaFourchette\Ldap\MockLdapManager;

class MockLdapManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testConnect() {
        $dut = new MockLdapManager();
        $isConnected = $dut->connect();

        $r = new \ReflectionObject($dut);
        $p = $r->getProperty('ldapRes');
        $p->setAccessible(true);

        $this->assertSame(true, $p->getValue($dut));
    }

    public function testBind() {
        $dut = new MockLdapManager();
        $isBinded = $dut->bind('', '');

        $this->assertSame(true, $isBinded);
    }

    public function testGetUserInfo() {
        $dut = new MockLdapManager();
        $user = $dut->getUserInfo('foobar');

        $this->assertInstanceOf('LaFourchette\Entity\User', $user);
        $this->assertSame('Anonymous', $user->getUsername());
    }

    public function testGestUserInfo() {
        $dut = new MockLdapManager('foo', 'bar@lafourchette.com');
        $user = $dut->getUserInfo('foobar');

        $this->assertInstanceOf('LaFourchette\Entity\User', $user);
        $this->assertSame('foo', $user->getUsername());
        $this->assertSame('bar@lafourchette.com', $user->getEmail());
    }

    public function testListUsers() {
        $user = 'foo';
        $email = 'bar@lafourchette.com';
        $dut = new MockLdapManager($user, $email);
        $list = $dut->listUsers();

        $this->assertSame(1, count($list));
        $this->assertSame(0, count(array_diff_assoc($list[0], array('username' => $user, 'email' => $email))));
    }
} 