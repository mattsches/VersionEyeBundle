<?php

namespace Mattsches\VersionEyeBundle\Service;

use Doctrine\Common\Cache\Cache;
use Guzzle\Cache\DoctrineCacheAdapter;
use Guzzle\Http\Message\Request;
use Guzzle\Plugin\Cache\CachePlugin;
use Guzzle\Plugin\Cache\CallbackCanCacheStrategy;
use Mattsches\VersionEyeBundle\Client\VersionEyeClient;

/**
 * Class VersionEyeApi
 *
 * @author Matthias Gutjahr <mail@matthias-gutjahr.de>
 * @package Mattsches\VersionEyeBundle\Service
 */
class VersionEyeApi
{
    /**
     * @var VersionEyeClient
     */
    protected $client;

    /**
     * @var string
     */
    protected $apiKey;

    /**
     * @param \Mattsches\VersionEyeBundle\Client\VersionEyeClient $client
     * @param string $apiKey
     * @param \Doctrine\Common\Cache\Cache $cache
     */
    public function __construct(VersionEyeClient $client, $apiKey, Cache $cache = null)
    {
        $this->client = $client;
        if ($cache !== null) {
            $adapter = new DoctrineCacheAdapter($cache);
            $options = array(
                'adapter' => $adapter,
                'can_cache' => new CallbackCanCacheStrategy(
                    function () {
                        return true;
                    },
                    function () {
                        return true;
                    }
                ),
            );
            $cachePlugin = new CachePlugin($options);
            $this->client->addSubscriber($cachePlugin);
        }
        $this->apiKey = $apiKey;
    }

    /**
     * Get list of projects
     *
     * @return \Guzzle\Http\Message\Response
     */
    public function getProjects()
    {
        $request = $this->client->get('projects.json?api_key=' . $this->apiKey);
        return $request->send();
    }

    /**
     * Creates a new project
     *
     * @param string $composerJson
     * @return \Guzzle\Http\Message\Response
     */
    public function postProject($composerJson)
    {
        /* @var \Guzzle\Http\Message\Request $request */
        $request = $this->client->post('projects.json?api_key=' . $this->apiKey)->addPostFiles(array(
                'upload' => $composerJson
            )
        );
        return $request->send();
    }

    /**
     * Updates an existing project
     *
     * @param string $projectKey
     * @param string $composerJson
     * @return \Guzzle\Http\Message\Response
     */
    public function updateProject($projectKey, $composerJson)
    {
        /* @var \Guzzle\Http\Message\Request $request */
        $request = $this->client->post('projects/' . $projectKey . '.json?api_key=' . $this->apiKey)->addPostFiles(array(
                'project_file' => $composerJson
            )
        );
        return $request->send();
    }
}
