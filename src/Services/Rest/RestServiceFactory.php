<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ShopwareCli\Services\Rest;

use ShopwareCli\Services\Rest\Curl\RestClient;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Factory for cache decorated rest services
 *
 * Class RestServiceFactory
 */
class RestServiceFactory
{
    /**
     * @var
     */
    protected $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * @param string $baseUrl
     * @param string $username
     * @param string $password
     * @param int    $cacheTime
     *
     * @return RestInterface
     */
    public function factory($baseUrl, $username = null, $password = null, $cacheTime = 3600)
    {
        return new CacheDecorator(new RestClient($baseUrl, $username, $password), $this->container->get('cache'), $cacheTime);
    }
}
