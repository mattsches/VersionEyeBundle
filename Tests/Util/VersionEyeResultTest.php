<?php

namespace Mattsches\VersionEyeBundle\Tests\Util;

use Mattsches\VersionEyeBundle\Util\VersionEyeResult;

/**
 * Class VersionEyeResultTest
 *
 * @author Matthias Gutjahr <mail@matthias-gutjahr.de>
 * @package Mattsches\VersionEyeBundle\Tests\Util
 */
class VersionEyeResultTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var VersionEyeResult
     */
    protected $object;

    /**
     * Setup
     */
    public function setUp()
    {
        $this->object = new VersionEyeResult(VersionEyeResult::STATUS_OK, array());
    }

    /**
     * @test
     * @dataProvider dependencyDataProvider
     *
     * @param string $color
     * @param array  $dependency
     */
    public function shouldGetStabilityColor($color, $dependency)
    {
        $this->assertEquals($color, $this->object->getStabilityColor($dependency));
    }

    /**
     * Data provider for shouldGetStabilityColor test
     *
     * @return array
     */
    public function dependencyDataProvider()
    {
        return array(
            array(
                VersionEyeResult::RED,
                array(
                    'name' => 'incenteev/composer-parameter-handler',
                    'prod_key' => 'incenteev/composer-parameter-handler',
                    'group_id' => null,
                    'artifact_id' => null,
                    'version_current' => '2.1.0',
                    'version_requested' => '2.0.0',
                    'comparator' => '~',
                    'unknown' => false,
                    'outdated' => true,
                    'stable' => true,
                )
            ),
            array(
                VersionEyeResult::GREEN,
                array(
                    'name' => 'incenteev/composer-parameter-handler',
                    'prod_key' => 'incenteev/composer-parameter-handler',
                    'group_id' => null,
                    'artifact_id' => null,
                    'version_current' => '2.1.0',
                    'version_requested' => '2.0.0',
                    'comparator' => '~',
                    'unknown' => false,
                    'outdated' => false,
                    'stable' => true,
                )
            ),
            array(
                VersionEyeResult::GREY,
                array(
                    'name' => 'incenteev/composer-parameter-handler',
                    'prod_key' => 'incenteev/composer-parameter-handler',
                    'group_id' => null,
                    'artifact_id' => null,
                    'version_current' => '2.1.0',
                    'version_requested' => '2.0.0',
                    'comparator' => '~',
                    'unknown' => true,
                    'outdated' => false,
                    'stable' => true,
                )
            ),
        );
    }
}
