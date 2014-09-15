<?php

namespace Mattsches\VersionEyeBundle\Tests\DataCollector;

use Doctrine\Common\Cache\ArrayCache;
use Rs\VersionEye\Client;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Guzzle\Http\Message\Response as GuzzleResponse;
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
     * @test
     */
    public function testCollect()
    {
        $http = $this->getMock('Rs\VersionEye\Http\HttpClient');

        $http->expects($this->atLeastOnce())->method('request')->will($this->returnValueMap([
            ['GET', 'projects', [], json_decode(file_get_contents(__DIR__.'/Fixtures/response_projects.json'), true)],
            ['POST', 'projects', ['upload' => realpath(__DIR__ . '/Fixtures/composer.json')], json_decode(file_get_contents(__DIR__.'/Fixtures/response.json'), true)],
            ['POST', 'projects/composer_sf2_demo_project_1', ['project_file' => realpath(__DIR__ . '/Fixtures/composer.json')], json_decode(file_get_contents(__DIR__.'/Fixtures/response.json'), true)]
        ]));

        $loader = new ComposerLoader(__DIR__ . '/Fixtures/composer.json');

        $collector = new VersionEyeDataCollector(new Client($http), $loader, new ArrayCache());
        $collector->collect(new Request(), new Response());

        /* @var VersionEyeResult $data */
        $data = $collector->getData();

        $this->assertInstanceOf('Mattsches\VersionEyeBundle\Util\VersionEyeResult', $data);
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
    public function testCollectCached()
    {
        $http = $this->getMock('Rs\VersionEye\Http\HttpClient');
        $cache = $this->getMock('Doctrine\Common\Cache\Cache');

        $cache->expects($this->atLeastOnce())->method('fetch')->will($this->returnValueMap([
            ['versioneye.project', 'composer_sf2_demo_project_1'],
            ['versioneye.data', json_decode(file_get_contents(__DIR__.'/Fixtures/response.json'), true)]
        ]));

        $http->expects($this->never())->method('request');

        $loader = new ComposerLoader(__DIR__ . '/Fixtures/composer.json');

        $collector = new VersionEyeDataCollector(new Client($http), $loader, $cache);
        $collector->collect(new Request(), new Response());

        /* @var VersionEyeResult $data */
        $data = $collector->getData();

        $this->assertInstanceOf('Mattsches\VersionEyeBundle\Util\VersionEyeResult', $data);
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
}
