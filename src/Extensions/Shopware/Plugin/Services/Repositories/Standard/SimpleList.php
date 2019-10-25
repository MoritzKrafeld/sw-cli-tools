<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Shopware\Plugin\Services\Repositories\Standard;

use Shopware\Plugin\Services\Repositories\BaseRepository;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * The simple list provides a "virtual repository" from multiple git repos which also might come from different
 * servers (e.g. local, github, bitbucket, stash…)
 *
 * Class SimpleList
 */
class SimpleList extends BaseRepository implements ContainerAwareInterface
{
    /** @var ContainerInterface */
    protected $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function getPluginByName($name, $exact = false)
    {
        $plugins = $this->getPlugins();
        foreach ($plugins as $key => $plugin) {
            if (!$this->doesMatch($plugin->name, $name, $exact)) {
                unset($plugins[$key]);
            }
        }

        return $plugins;
    }

    /**
     * {@inheritdoc}
     */
    public function getPlugins()
    {
        $config = $this->container->get('config');
        if (!isset($config['repositories']['SimpleList'])) {
            return [];
        }

        $plugins = [];
        foreach ($config['repositories']['SimpleList']['repositories'] as $repositoryName => $repository) {
            foreach ($repository['plugins'] as $name => $cloneUrls) {
                $plugins[] = $this->createPlugin($cloneUrls['ssh'], $cloneUrls['http'], $name);
            }
        }

        return $plugins;
    }
}
