<?php
use Illuminate\Redis\Database;

/**
 * PHP Version 5
 *
 * @category  H24
 * @package
 * @author    "Yury Kozyrev" <yury.kozyrev@home24.de>
 * @copyright 2015 Home24 GmbH
 * @license   Proprietary license.
 * @link      http://www.home24.de
 */
class StorageAdapterTest extends TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $handler;

    /**
     * @var \App\Components\StorageAdapter
     */
    protected $adapter;

    public function setUp()
    {
        parent::setUp();
        $this->handler = $this->getMockBuilder('Illuminate\Redis\Database')
            ->disableOriginalConstructor()
            ->setMethods(['__call'])
            ->getMock();
        $this->adapter = new \App\Components\StorageAdapter($this->handler);

    }

    public function testSetGet()
    {
        $cache = new ArrayObject();
        $this->handler->expects($this->any())->method('__call')->willReturnCallback(function ($name, array $args) use ($cache) {
            if ('set' === $name) {
                $cache[reset($args)] = end($args);
                return true;
            }
            if ('get' === $name) {
                return $cache[reset($args)];
            }

        });
        $this->assertEquals(0, $cache->count());

        $this->adapter->set('key1', 'value1');
        $this->assertEquals(1, $cache->count());
        foreach ($cache as $value) {
            $this->assertEquals('value1', $value);
        }
        $res = $this->adapter->get('key1');

        $this->assertEquals('value1', $res);
    }
}