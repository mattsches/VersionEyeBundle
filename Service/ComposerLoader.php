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
     * @var string
     */
    protected $projectName;

    /**
     * @param $composerJson
     */
    public function __construct($composerJson)
    {
        $this->setComposerJson($composerJson);
    }

    /**
     * @param string $composerJson
     */
    public function setComposerJson($composerJson)
    {
        if (file_exists($composerJson)) {
            $this->composerJson = $composerJson;
        }
    }

    /**
     * @return string
     */
    public function getComposerJson()
    {
        return realpath($this->composerJson);
    }

    /**
     * @return mixed
     */
    public function getProjectName()
    {
        try {
            $composer = json_decode(file_get_contents($this->getComposerJson()));
        } catch (\Exception $e) {
            return null;
        }
        if (is_object($composer) && property_exists($composer, 'name')) {
            $this->projectName = $composer->name;
        }
        return $this->projectName;
    }
}
