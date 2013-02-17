<?php

namespace Mattsches\VersionEyeBundle\Service;

/**
 * Class ComposerLoader
 *
 * @author Matthias Gutjahr <mail@matthias-gutjahr.de>
 * @package Mattsches\VersionEyeBundle\Service
 */
class ComposerLoader
{
    /**
     * @var string
     */
    protected $composerJson;

    /**
     * @param $composerJson
     */
    public function __construct($composerJson)
    {
        if (file_exists($composerJson)) {
            $this->composerJson = $composerJson;
        }
    }

    /**
     * @param string $composerJson
     */
    public function setComposerJson($composerJson)
    {
        $this->composerJson = $composerJson;
    }

    /**
     * @return string
     */
    public function getComposerJson()
    {
        return $this->composerJson;
    }
}
