<?php

namespace Mattsches\VersionEyeBundle\Tests\Service;

use Mattsches\VersionEyeBundle\Service\VersionEyeApi;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Guzzle\Plugin\Mock\MockPlugin;
use Guzzle\Http\Message\Response as GuzzleResponse;
use Mattsches\VersionEyeBundle\Client\VersionEyeClient;

/**
 * Class VersionEyeApiTest
 *
 * @author Matthias Gutjahr <mail@matthias-gutjahr.de>
 * @package Mattsches\VersionEyeBundle\Tests\Service
 */
class VersionEyeApiTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var VersionEyeApi
     */
    protected $object;

    /**
     * Setup
     */
    public function setUp()
    {
        $plugin = new MockPlugin();
        $response = new GuzzleResponse(200);
        $response->setBody(file_get_contents(__DIR__ . '/Fixtures/response_projects.json'));
        $plugin->addResponse($response);
        $client = new VersionEyeClient('foo');
        $client->addSubscriber($plugin);
        $this->object = new VersionEyeApi($client, 'foo');
    }

    /**
     * @test
     */
    public function testGetProjects()
    {
        $projects = $this->object->getProjects();
        $this->assertInstanceOf('Guzzle\Http\Message\Response', $projects);
        $this->assertEquals(file_get_contents(__DIR__ . '/Fixtures/response_projects.json'), $projects->getBody());
    }

    /**
     * @test
     */
    public function testPostProject()
    {
        $result = $this->object->postProject(__DIR__ . '/Fixtures/composer.json');
        $this->assertInstanceOf('Guzzle\Http\Message\Response', $result);
    }

    /**
     * @test
     */
    public function testUpdateProject()
    {
        $projectKey = 'foo_1';
        $result = $this->object->updateProject($projectKey, __DIR__ . '/Fixtures/composer.json');
        $this->assertInstanceOf('Guzzle\Http\Message\Response', $result);
    }
}
