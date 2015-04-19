<?php

class ExampleTest extends TestCase
{

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $cache;

    public function setUp()
    {
        parent::setUp();
        $this->cache = $this->getMockBuilder(Illuminate\Cache\CacheManager::class)
            ->disableOriginalConstructor()
            ->setMethods(['get', '__call'])
            ->getMock();
        $this->cache->expects($this->any())->method('get')->willReturn(null);

        \Illuminate\Container\Container::getInstance()->offsetSet('cache', $this->cache);
        $guzzle = \Mockery::mock(\GuzzleHttp\Client::class . '[get]');
        \Illuminate\Container\Container::getInstance()->offsetSet('guzzle', $guzzle);
    }

    /**
     * A basic functional test example.
     *
     * @return void
     */
    public function testBasicExample()
    {
        $response = $this->call('GET', '/');

        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * A basic functional test example.
     *
     * @return void
     */
    public function testCheckEmpty()
    {
        $response = $this->call('GET', '/asd');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("Empty id", $response->getContent());

        $response = $this->call('GET', '/000');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("Empty id", $response->getContent());
    }

    /**
     * A basic functional test example.
     *
     * @return void
     */
    public function testCheckGet()
    {
        $this->cache->expects($this->any())->method('__call')->willReturnCallback(function ($method, array $args) {
            if ('get' === $method) {
                return '123 Pass 30.04.15';
            }
        });

        $response = $this->call('GET', '/123');
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('123 Pass 30.04.15', $response->getContent());
    }

    /**
     * A basic functional test example.
     *
     * @return void
     */
    public function testCheckGetCall()
    {
        $this->cache->expects($this->any())->method('__call')->willReturnCallback(function ($method, array $args) {
            if ('forever' === $method) {
                return;
            }
            if ('get' === $method) {
                return;
            }
        });

        $stream = new \GuzzleHttp\Stream\BufferStream();
        $stream->write(file_get_contents(__DIR__ . '/fixtures/fixture.pdf'));

        app('guzzle')->shouldReceive('get')->andReturn(new GuzzleHttp\Message\Response(200, ['X-Foo' => 'Bar'], $stream));

        $response = $this->call('GET', '/2953799');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('2953799 (3086682)  RP, WS, KV  26.06.2015 ', $response->getContent());

    }

    /**
     * A basic functional test example.
     *
     * @return void
     */
    public function testCheckNotFound()
    {
        $this->cache->expects($this->any())->method('__call')->willReturnCallback(function ($method, array $args) {
            if ('forever' === $method) {
                return;
            }
            if ('get' === $method) {
                return;
            }
        });

        $stream = new \GuzzleHttp\Stream\BufferStream();
        $stream->write(file_get_contents(__DIR__ . '/fixtures/fixture.pdf'));

        app('guzzle')->shouldReceive('get')->andReturn(new GuzzleHttp\Message\Response(200, ['X-Foo' => 'Bar'], $stream));

        $response = $this->call('GET', '/1000000');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Id not found', $response->getContent());

    }

}
