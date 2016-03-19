<?php

namespace Mattsches\VersionEyeBundle\Tests\Service;

use Mattsches\VersionEyeBundle\Service\ComposerLoader;

/**
 * Class ComposerLoaderTest
 *
 * @author Matthias Gutjahr <mail@matthias-gutjahr.de>
 * @package Mattsches\VersionEyeBundle\Tests\Service
 */
class ComposerLoaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ComposerLoader
     */
    protected $object;

    /**
     * Setup
     */
    public function setUp()
    {
        $this->object = new ComposerLoader(__DIR__ . '/../DataCollector/Fixtures/composer.json');
    }

    /**
     * @test
     */
    public function testGetTempFileAndContents()
    {
        $contents = file_get_contents(__DIR__ . '/../DataCollector/Fixtures/composer.json');
        $this->assertSame($contents, file_get_contents($this->object->getComposerJson()));
    }

    /**
     * @test
     */
    public function testGetProjectName()
    {
        $projectName = $this->object->getProjectName();
        $this->assertEquals('mattsches/version-eye-bundle', $projectName);
    }

    /**
     * @test
     */
    public function testGetProjectNameNoName()
    {
        $this->object->setComposerJson(__DIR__ . '/../DataCollector/Fixtures/composer_noname.json');
        $projectName = $this->object->getProjectName();
        $this->assertNull($projectName);
    }

    /**
     * @test
     */
    public function testGetProjectNameNoFile()
    {
        $this->object = new ComposerLoader('foobar_notexists');
        $projectName = $this->object->getProjectName();
        $this->assertNull($projectName);
    }
}
