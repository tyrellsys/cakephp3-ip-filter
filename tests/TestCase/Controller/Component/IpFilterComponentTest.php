<?php
declare(strict_types=1);

namespace Tyrellsys\CakePHP3IpFilter\Test\TestCase\Controller\Component;

use Cake\Http\Exception\ForbiddenException;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;

/**
 * IpFilterComponentTest class
 */
class IpFilterComponentTest extends TestCase
{
    /**
     * @var \Tyrellsys\CakePHP3IpFilter\Test\TestCase\Controller\Component\IpFilterComponent
     */
    protected $IpFilter;

    protected $_oSERVER;

    /**
     * start
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->_oSERVER = $_SERVER;

        $controller = $this->getMockBuilder('Cake\Controller\Controller')
            ->onlyMethods(['redirect'])
            ->setConstructorArgs([new ServerRequest(), new Response()])
            ->getMock();

        $controller->loadComponent('Tyrellsys/CakePHP3IpFilter.IpFilter', [
            'whitelist' => [
                '192.168.0.1',
                '172.16.0.0/24',
                '10.0.0.0/24',
            ],
        ]);

        $this->Controller = $controller;
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();

        unset($this->Controller);

        $_SERVER = $this->_oSERVER;
    }

    /**
     * provide data
     * Instance of ServerRequest caches environment variables,
     * create an instance of Controller for each test
     *
     * @return array
     */
    public static function provideData()
    {
        return [
            ['192.168.0.1', true],
            ['192.168.0.2', false],
            ['172.16.0.1', true],
            ['172.16.1.1', false],
            ['10.0.0.1', true],
            ['10.0.1.1', false],
        ];
    }

    /**
     * test check method with argument
     *
     * @dataProvider provideData
     * @return void
     */
    public function testCheckWithArgument($ip, $expected)
    {
        $actual = $this->Controller->IpFilter->check($ip);
        $this->assertEquals($expected, $actual, $ip);
    }

    /**
     * test check method without argument
     *
     * @dataProvider provideData
     * @return void
     */
    public function testCheckWithoutArgument($ip, $expected)
    {
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
        $_SERVER['HTTP_X_FORWARDED_FOR'] = '192.168.0.2,172.16.1.2,' . $ip;
        $actual = $this->Controller->IpFilter->check();
        $this->assertEquals($expected, $actual, $ip);
    }

    /**
     * test check method for no TrustProxy
     *
     * @dataProvider provideData
     * @return void
     */
    public function testCheckNoTrustProxy($ip)
    {
        $this->Controller->IpFilter->setConfig('trustProxy', false);
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
        $_SERVER['HTTP_X_FORWARDED_FOR'] = '192.168.0.2,172.16.1.2,' . $ip;
        $actual = $this->Controller->IpFilter->check();
        $this->assertFalse($actual, $ip);
    }

    /**
     * test checkOrFail method
     *
     * @return void
     */
    public function testCheckOrFail()
    {
        $this->expectException(ForbiddenException::class);
        $this->Controller->IpFilter->checkOrFail('127.0.0.1');
    }
}
