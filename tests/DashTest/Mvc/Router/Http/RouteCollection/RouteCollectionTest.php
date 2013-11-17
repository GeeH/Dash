<?php
/**
 * Dash
 *
 * @link      http://github.com/DASPRiD/Dash For the canonical source repository
 * @copyright 2013 Ben Scholzen 'DASPRiD'
 * @license   http://opensource.org/licenses/BSD-2-Clause Simplified BSD License
 */

namespace DashTest\Mvc\Router\Http\RouteCollection;

use Dash\Mvc\Router\Http\RouteCollection\RouteCollection;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @covers Dash\Mvc\Router\Http\RouteCollection\RouteCollection
 */
class RouteCollectionTest extends TestCase
{
    /**
     * @var ServiceLocatorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $serviceLocator;

    /**
     * @var RouteCollection
     */
    protected $collection;

    /**
     * @var \Dash\Mvc\Router\Http\Route\RouteInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockRoute;

    public function setUp()
    {
        $this->serviceLocator = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');
        $this->collection     = new RouteCollection($this->serviceLocator);
        $this->mockRoute      = $this->getMock('Dash\Mvc\Router\Http\Route\RouteInterface');
    }

    public function testInsertWithRoute()
    {
        $this->collection->insert('foo', $this->mockRoute, 0);

        $this->assertEquals(1, count($this->collection));

        foreach ($this->collection as $key => $value) {
            $this->assertEquals('foo', $key);
        }
    }

    public function testInsertWithArray()
    {
        $this
            ->serviceLocator
            ->expects($this->once())
            ->method('get')
            ->with($this->equalTo('foo'), $this->equalTo(['type' => 'foo']))
            ->will($this->returnValue($this->mockRoute));

        $this->collection->insert('foo', ['type' => 'foo'], 0);
        $this->collection->insert('bar', ['type' => 'bar'], 0);

        $this->collection->get('foo');
    }

    public function testInsertWithArrayWithoutType()
    {
        $this
            ->serviceLocator
            ->expects($this->once())
            ->method('get')
            ->with($this->equalTo('generic'), $this->equalTo(['foo']))
            ->will($this->returnValue($this->mockRoute));

        $this->collection->insert('foo', ['foo'], 0);
        $this->collection->insert('bar', ['bar'], 0);

        $this->collection->get('foo');
    }

    public function testInsertInvalidRoute()
    {
        $this->setExpectedException(
            'Dash\Mvc\Router\Exception\InvalidArgumentException',
            '$route must either be an array or implement Dash\Mvc\Router\Http\Route\RouteInterface, string given'
        );
        $this->collection->insert('foo', 'bar', 0);
    }

    public function testRemove()
    {
        $this->collection->insert('foo', $this->mockRoute, 0);
        $this->collection->insert('bar', $this->mockRoute, 0);

        $this->assertEquals(2, count($this->collection));
        $this->collection->remove('foo');
        $this->assertEquals(1, count($this->collection));
    }

    public function testRemovingNonExistentRouteDoesNotYieldError()
    {
        $this->collection->remove('foo');
    }

    public function testClear()
    {
        $this->collection->insert('foo', $this->mockRoute, 0);
        $this->collection->insert('bar', $this->mockRoute, 0);

        $this->assertEquals(2, count($this->collection));
        $this->collection->clear();
        $this->assertEquals(0, count($this->collection));
        $this->assertSame(false, $this->collection->current());
    }

    public function testGet()
    {
        $this->collection->insert('foo', $this->mockRoute, 0);

        $this->assertSame($this->mockRoute, $this->collection->get('foo'));
        $this->assertNull($this->collection->get('bar'));
    }

    public function testLIFOOnly()
    {
        $this->collection->insert('foo', $this->mockRoute, 0);
        $this->collection->insert('bar', $this->mockRoute, 0);
        $this->collection->insert('baz', $this->mockRoute, 0);

        $order = [];

        foreach ($this->collection as $key => $value) {
            $orders[] = $key;
        }

        $this->assertEquals(['baz', 'bar', 'foo'], $orders);
    }

    public function testPriorityOnly()
    {
        $this->collection->insert('foo', $this->mockRoute, 1);
        $this->collection->insert('bar', $this->mockRoute, 0);
        $this->collection->insert('baz', $this->mockRoute, 2);

        $order = [];

        foreach ($this->collection as $key => $value) {
            $orders[] = $key;
        }

        $this->assertEquals(['baz', 'foo', 'bar'], $orders);
    }

    public function testLIFOWithPriority()
    {
        $this->collection->insert('foo', $this->mockRoute, 0);
        $this->collection->insert('bar', $this->mockRoute, 0);
        $this->collection->insert('baz', $this->mockRoute, 1);

        $order = [];

        foreach ($this->collection as $key => $value) {
            $orders[] = $key;
        }

        $this->assertEquals(['baz', 'bar', 'foo'], $orders);
    }

    public function testPriorityWithNegativesAndNull()
    {
        $this->collection->insert('foo', $this->mockRoute, null);
        $this->collection->insert('bar', $this->mockRoute, 1);
        $this->collection->insert('baz', $this->mockRoute, -1);

        $order = [];

        foreach ($this->collection as $key => $value) {
            $orders[] = $key;
        }

        $this->assertEquals(['bar', 'foo', 'baz'], $orders);
    }
}
