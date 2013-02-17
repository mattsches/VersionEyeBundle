<?php

namespace Mattsches\VersionEyeBundle\Tests\DataCollector;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Guzzle\Plugin\Mock\MockPlugin;
use Guzzle\Http\Message\Response as GuzzleResponse;
use Guzzle\Tests\GuzzleTestCase;
use Mattsches\VersionEyeBundle\Client\VersionEyeClient;
use Mattsches\VersionEyeBundle\DataCollector\VersionEyeDataCollector;
use Mattsches\VersionEyeBundle\Service\ComposerLoader;

/**
 * Class VersionEyeDataCollectorTest
 *
 * @author Matthias Gutjahr <mail@matthias-gutjahr.de>
 * @package Mattsches\VersionEyeBundle\Tests\DataCollector
 */
class VersionEyeDataCollectorTest extends GuzzleTestCase
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
        $plugin = new MockPlugin();
        $response = new GuzzleResponse(200);
        $response->setBody(file_get_contents(__DIR__ . '/Fixtures/response.json'));
        $plugin->addResponse($response);
        $client = new VersionEyeClient('foo');
        $client->addSubscriber($plugin);
        $loader = new ComposerLoader(__DIR__ . '/Fixtures/composer.json');
        $this->object = new VersionEyeDataCollector($client, 'foo', $loader);
    }

    /**
     * @test
     */
    public function testCollect()
    {
        $this->object->collect(new Request(), new Response());
        $data = $this->object->getData();
        $this->assertInternalType('array' ,$data);
        $this->assertSame('version_eye', $this->object->getName());
        $this->assertArrayHasKey('dependencies', $data);
        $this->assertArrayNotHasKey('foobar', $data);
    }
}
