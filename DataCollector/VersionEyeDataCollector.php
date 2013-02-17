<?php

namespace Mattsches\VersionEyeBundle\DataCollector;

use Guzzle\Http\Message\RequestInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Doctrine\Common\Cache\Cache;
use Guzzle\Cache\DoctrineCacheAdapter;
use Guzzle\Http\Message\EntityEnclosingRequest;
use Guzzle\Plugin\Cache\CachePlugin;
use Guzzle\Plugin\Cache\CallbackCanCacheStrategy;
use Mattsches\VersionEyeBundle\Client\VersionEyeClient;
use Mattsches\VersionEyeBundle\Service\ComposerLoader;

/**
 * Class VersionEyeDataCollector
 *
 * @author Matthias Gutjahr <mail@matthias-gutjahr.de>
 * @package Mattsches\VersionEyeBundle\DataCollector
 */
class VersionEyeDataCollector extends DataCollector
{
    /**
     * @var \Mattsches\VersionEyeBundle\Client\VersionEyeClient
     */
    protected $client;

    /**
     * @var string
     */
    protected $apiKey;

    /**
     * @var \Mattsches\VersionEyeBundle\Service\ComposerLoader
     */
    protected $loader;

    /**
     * @param \Mattsches\VersionEyeBundle\Client\VersionEyeClient $client
     * @param string $apiKey
     * @param \Mattsches\VersionEyeBundle\Service\ComposerLoader $loader
     * @param \Doctrine\Common\Cache\Cache $cache
     */
    public function __construct(VersionEyeClient $client, $apiKey, ComposerLoader $loader, Cache $cache = null)
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
        $this->loader = $loader;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Symfony\Component\HttpFoundation\Response $response
     * @param \Exception $exception
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $this->data = array(
            $this->call(),
        );
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data[0];
    }

    /**
     * @return mixed
     */
    protected function call()
    {
        /* @var RequestInterface $request */
        $request = $this->client->post('projects.json?api_key=' . $this->apiKey)->addPostFiles(array(
                'upload' => $this->loader->getComposerJson()
            )
        );
        $response = $request->send();
        return $response->json();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'version_eye';
    }
}
