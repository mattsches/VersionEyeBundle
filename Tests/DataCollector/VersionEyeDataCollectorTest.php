<?php

namespace Mattsches\VersionEyeBundle\Tests\DataCollector;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Guzzle\Plugin\Mock\MockPlugin;
use Guzzle\Http\Message\Response as GuzzleResponse;
use Mattsches\VersionEyeBundle\Client\VersionEyeClient;
use Mattsches\VersionEyeBundle\DataCollector\VersionEyeDataCollector;
use Mattsches\VersionEyeBundle\Service\ComposerLoader;
use Mattsches\VersionEyeBundle\Util\VersionEyeResult;

/**
 * Class VersionEyeDataCollectorTest
 *
 * @author Matthias Gutjahr <mail@matthias-gutjahr.de>
 * @package Mattsches\VersionEyeBundle\Tests\DataCollector
 */
class VersionEyeDataCollectorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var VersionEyeDataCollector
     */
    protected $object;

    /**
     * Setup
     */
    protected function setUp()
    {
        if (!class_exists('Symfony\Component\HttpFoundation\Request')) {
            $this->markTestSkipped('The "HttpFoundation" component is not available');
        }
    }

    /**
     * @test
     */
    public function testCollect()
    {
        $plugin = new MockPlugin();
        $response = new GuzzleResponse(200);
        $response->setBody(file_get_contents(__DIR__ . '/Fixtures/response.json'));
        $plugin->addResponse($response);
        $client = new VersionEyeClient('foo');
        $client->addSubscriber($plugin);
        $loader = new ComposerLoader(__DIR__ . '/Fixtures/composer.json');
        $this->object = new VersionEyeDataCollector($client, 'foo', $loader);
        $this->object->collect(new Request(), new Response());
        /* @var VersionEyeResult $data */
        $data = $this->object->getData();
        $this->assertInstanceOf('Mattsches\VersionEyeBundle\Util\VersionEyeResult', $data);
        $this->assertSame('version_eye', $this->object->getName());
        $this->assertObjectHasAttribute('dependencies', $data);
        $this->assertObjectHasAttribute('status', $data);
        $this->assertObjectHasAttribute('outNumber', $data);
        $this->assertObjectHasAttribute('depNumber', $data);
        $this->assertObjectNotHasAttribute('foobar', $data);
        $this->assertEquals(1, $data->getStatus());
        $this->assertEquals(24, $data->getDepNumber());
        $this->assertEquals(18, $data->getOutNumber());
        $this->assertInternalType('array', $data->getDependencies());
        $this->assertArrayHasKey('0', $data->getDependencies());
        $this->assertArrayHasKey('1', $data->getDependencies());
        $dependencies = $data->getDependencies();
        $this->assertArrayHasKey('name', $dependencies[0]);
        $this->assertEquals('doctrine/doctrine-bundle', $dependencies[0]['name']);
    }

    /**
     * @test
     */
    public function testCollectOffline()
    {
        $plugin = new MockPlugin();
        $response = new GuzzleResponse(404);
        $response->setBody(file_get_contents(__DIR__ . '/Fixtures/response.json'));
        $plugin->addResponse($response);
        $client = new VersionEyeClient('foo');
        $client->addSubscriber($plugin);
        $loader = new ComposerLoader(__DIR__ . '/Fixtures/composer.json');
        $this->object = new VersionEyeDataCollector($client, 'foo', $loader);
        $this->object->collect(new Request(), new Response());
        /* @var VersionEyeResult $data */
        $data = $this->object->getData();
        $this->assertInstanceOf('Mattsches\VersionEyeBundle\Util\VersionEyeResult', $data);
        $this->assertSame('version_eye', $this->object->getName());
        $this->assertObjectHasAttribute('dependencies', $data);
        $this->assertObjectHasAttribute('status', $data);
        $this->assertObjectHasAttribute('outNumber', $data);
        $this->assertObjectHasAttribute('depNumber', $data);
        $this->assertObjectNotHasAttribute('foobar', $data);
        $this->assertEquals(0, $data->getStatus());
        $this->assertNull($data->getDepNumber());
        $this->assertNull($data->getOutNumber());
    }
}
