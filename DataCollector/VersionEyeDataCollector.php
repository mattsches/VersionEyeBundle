<?php

namespace Mattsches\VersionEyeBundle\DataCollector;

use Doctrine\Common\Cache\Cache;
use Rs\VersionEye\Client;
use Rs\VersionEye\Http\CommunicationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;
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
     * @var Client
     */
    protected $client;

    /**
     * @var \Mattsches\VersionEyeBundle\Service\ComposerLoader
     */
    protected $loader;
    /**
     * @var Cache
     */
    private $cache;

    /**
     * @param Client $client
     * @param \Mattsches\VersionEyeBundle\Service\ComposerLoader $loader
     * @param Cache $cache
     */
    public function __construct(Client $client, ComposerLoader $loader, Cache $cache = null)
    {
        $this->client = $client;
        $this->loader = $loader;
        $this->cache = $cache;
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
        try {
            $projectKey = $this->cache ? $this->cache->fetch('versioneye.project') : null;

            if (!$projectKey) {
                $projectName = $this->loader->getProjectName();
                $projects = $this->client->api('projects')->all();
                foreach ($projects as $project) {
                    if ($project['name'] === $projectName) {
                        $projectKey = $project['id'];
                        $this->cache->save('versioneye.project', $projectKey);
                        break;
                    }
                }
            }

            $response = $this->cache ? $this->cache->fetch('versioneye.data') : null;

            if (!$response) {
                if ($projectKey === null) {
                    $response = $this->client->api('projects')->create($this->loader->getComposerJson());
                } else {
                    try {
                        $response = $this->client->api('projects')->update($projectKey, $this->loader->getComposerJson());
                    } catch (CommunicationException $e) {
                        //fails when project was deleted on versioneye
                        $response = $this->client->api('projects')->create($this->loader->getComposerJson());
                    }
                }
                $this->cache->save('versioneye.data', $response, 600);
            }
        } catch (\Exception $e) {
            return new VersionEyeResult(
                VersionEyeResult::STATUS_ERR
            );
        }
        return new VersionEyeResult(
            VersionEyeResult::STATUS_OK,
            $response
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
