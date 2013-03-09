<?php

namespace Mattsches\VersionEyeBundle\DataCollector;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Guzzle\Http\Message\EntityEnclosingRequest;
use Mattsches\VersionEyeBundle\Service\VersionEyeApi;
use Mattsches\VersionEyeBundle\Service\ComposerLoader;
use Mattsches\VersionEyeBundle\Util\VersionEyeResult;

/**
 * Class VersionEyeDataCollector
 *
 * @author Matthias Gutjahr <mail@matthias-gutjahr.de>
 * @package Mattsches\VersionEyeBundle\DataCollector
 */
class VersionEyeDataCollector extends DataCollector
{
    /**
     * @var \Mattsches\VersionEyeBundle\Service\VersionEyeApi
     */
    protected $api;

    /**
     * @var \Mattsches\VersionEyeBundle\Service\ComposerLoader
     */
    protected $loader;

    /**
     * @param \Mattsches\VersionEyeBundle\Service\VersionEyeApi $api
     * @param \Mattsches\VersionEyeBundle\Service\ComposerLoader $loader
     */
    public function __construct(VersionEyeApi $api, ComposerLoader $loader)
    {
        $this->api = $api;
        $this->loader = $loader;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Symfony\Component\HttpFoundation\Response $response
     * @param \Exception $exception
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $this->data = $this->call();
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return mixed
     */
    protected function call()
    {
        $projectName = $this->loader->getProjectName();
        $projectKey = null;
        try {
            $projects = $this->api->getProjects()->json();
            foreach ($projects as $project) {
                if ($project['name'] == $projectName) {
                    $projectKey = $project['project_key'];
                    break;
                }
            }
            if ($projectKey === null) {
                $response = $this->api->postProject($this->loader->getComposerJson());
            } else {
                $response = $this->api->updateProject($projectKey, $this->loader->getComposerJson());
            }
        } catch (\Exception $e) {
            return new VersionEyeResult(
                VersionEyeResult::STATUS_ERR
            );
        }
        return new VersionEyeResult(
            VersionEyeResult::STATUS_OK,
            $response->json()
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'version_eye';
    }
}
