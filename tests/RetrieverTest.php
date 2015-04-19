<?php

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
class RetrieverTest extends TestCase
{

    /**
     * @var \App\Components\Retriever
     */
    protected $retriever;

    /**
     * @var \GuzzleHttp\Client | \Mockery\MockInterface
     */
    protected $guzzle;

    /**
     * @var \App\Components\StorageAdapter | PHPUnit_Framework_MockObject_MockObject
     */
    protected $storage;

    /**
     * @var GuzzleHttp\Message\Response
     */
    protected $responsePrototype;

    public function setUp()
    {
        parent::setUp();
        $this->guzzle    = \Mockery::mock(\GuzzleHttp\Client::class . '[get]');
        $this->storage   = $this->getMockBuilder(\App\Components\StorageAdapter::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->retriever = new \App\Components\Retriever($this->guzzle, $this->storage);

        $stream = new \GuzzleHttp\Stream\BufferStream();
        $stream->write(file_get_contents(__DIR__ . '/fixtures/fixture.pdf'));
        $this->responsePrototype = new GuzzleHttp\Message\Response(200, ['X-Foo' => 'Bar'], $stream);
    }

    public function testInit()
    {
        $this->assertEmpty($this->retriever->getResults());

    }

    public function testRetrieve()
    {
        $this->guzzle->shouldReceive('get')->times(1)->andReturn(clone $this->responsePrototype);
        $results = $this->retriever->retrieve()->getResults();
        $this->assertNotEmpty($results);
        $this->assertEquals(133, count($results));
        foreach ($results as $key => $line) {
            $this->assertContains((string) $key, $line);
        }

        $this->storage->expects($this->any())->method('set')->willReturnCallback(function ($key, $value) {
            $this->assertContains((string) $key, $value);
        });
        $this->retriever->save();
    }

    public function testLookForId()
    {
        $this->storage->expects($this->once())->method('get')->willReturnCallback(function($key){
           $this->assertEquals(1234567, $key);
            return "1234567  VM  31.02.14";
        });
        $this->guzzle->shouldNotReceive('get');
        $result = $this->retriever->lookForId(1234567);
        $this->assertEquals("1234567  VM  31.02.14", $result);
    }

    public function testLookForIdForce()
    {
        $this->storage->expects($this->once())->method('get')->willReturnCallback(function($key){
           $this->assertEquals(1234567, $key);
            return null;
        });
        $count = 0;
        $this->storage->expects($this->any())->method('set')->willReturnCallback(function($key, $value)use(&$count){
            $this->assertContains((string) $key, $value);
            $count++;
        });
        $this->guzzle->shouldReceive('get')->atLeast()->times(1)->andReturn(clone $this->responsePrototype);

        $result = $this->retriever->lookForId(1234567, true);
        $this->assertNull($result);
        $results = $this->retriever->getResults();
        $this->assertEquals(133, count($results));
        $this->assertEquals(133, $count);
    }


}