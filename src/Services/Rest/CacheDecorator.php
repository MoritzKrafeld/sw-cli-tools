<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ShopwareCli\Services\Rest;

use ShopwareCli\Cache\CacheInterface;

/**
 * Decorated a RestInterface in order to implement a simple cache layer for GET requests
 */
class CacheDecorator implements RestInterface
{
    /**
     * @var RestInterface
     */
    protected $decorate;

    /**
     * @var CacheInterface
     */
    protected $cacheProvider;

    /**
     * @var int
     */
    protected $cacheTime;

    public function __construct(RestInterface $restService, CacheInterface $cacheProvider, int $cacheTime = 1)
    {
        $this->decorate = $restService;
        $this->cacheProvider = $cacheProvider;
        $this->cacheTime = $cacheTime;
    }

    /**
     * {@inheritdoc}
     */
    public function get($url, $parameters = [], $headers = [])
    {
        $cacheKey = $url . \json_encode($parameters) . \json_encode($headers);

        return $this->callCached('get', \sha1($cacheKey), $url, $parameters = [], $headers = []);
    }

    /**
     * {@inheritdoc}
     */
    public function post($url, $parameters = [], $headers = [])
    {
        return $this->decorate->post($url, $parameters, $headers);
    }

    /**
     * {@inheritdoc}
     */
    public function put($url, $parameters = [], $headers = [])
    {
        return $this->decorate->put($url, $parameters, $headers);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($url, $parameters = [], $headers = [])
    {
        return $this->decorate->delete($url, $parameters, $headers);
    }

    /**
     * @param string $call
     * @param string $key
     * @param string $url
     * @param array  $parameters
     * @param array  $headers
     *
     * @return bool|mixed
     */
    public function callCached($call, $key, $url, $parameters = [], $headers = [])
    {
        /* @var ResponseInterface $response */
        if (!$this->cacheProvider || $this->cacheTime === 0) {
            $response = \call_user_func([$this->decorate, $call], $url, $parameters, $headers);
        } else {
            $response = $this->cacheProvider->read($key);
            if ($response === false) {
                $response = \call_user_func([$this->decorate, $call], $url, $parameters, $headers);
                if ($response === false) {
                    return false;
                }
                // Don't cache errors
                if (!$response->getErrorMessage()) {
                    $this->cacheProvider->write($key, \serialize($response), $this->cacheTime);
                }
            } else {
                $response = \unserialize($response);
            }
        }

        return $response;
    }
}
