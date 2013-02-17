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
}
