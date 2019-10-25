<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Shopware\AutoUpdate;

use Humbug\SelfUpdate\Updater;
use Shopware\AutoUpdate\Command\RollbackCommand;
use Shopware\AutoUpdate\Command\SelfUpdateCommand;
use ShopwareCli\Application\ConsoleAwareExtension;
use ShopwareCli\Application\ContainerAwareExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Provides self update capability
 *
 * Class Bootstrap
 */
class Bootstrap implements ConsoleAwareExtension, ContainerAwareExtension
{
    /** @var ContainerBuilder */
    protected $container;

    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerBuilder $container)
    {
        $this->container = $container;

        if (!$this->isPharFile()) {
            return;
        }

        $this->populateContainer($container);
    }

    /**
     * {@inheritdoc}
     */
    public function getConsoleCommands()
    {
        if (!$this->isPharFile()) {
            return [];
        }

        if ($this->checkUpdateOnRun()) {
            $this->runUpdate();
        }

        return [
            new SelfUpdateCommand($this->container->get('updater')),
            new RollbackCommand($this->container->get('updater')),
        ];
    }

    /**
     * Checks if script is run as phar archive and manifestUrl is available
     *
     * @return bool
     */
    public function isPharFile()
    {
        $toolPath = $this->container->get('path_provider')->getCliToolPath();

        return strpos($toolPath, 'phar:') !== false;
    }

    /**
     * @param ContainerBuilder $container
     */
    private function populateContainer($container)
    {
        $container->set('updater', $this->createUpdater());
    }

    /**
     * @return Updater
     */
    private function createUpdater()
    {
        $config = $this->container->get('config');
        $pharUrl = $config['update']['pharUrl'];
        $versionUrl = $config['update']['vesionUrl'];
        $verifyKey = (bool) $config['update']['verifyPublicKey'];

        $updater = new Updater(null, $verifyKey);
        $updater->getStrategy()->setPharUrl($pharUrl);
        $updater->getStrategy()->setVersionUrl($versionUrl);

        return $updater;
    }

    /**
     * perform update on the fly
     */
    private function runUpdate()
    {
        $updater = $this->container->get('updater');

        try {
            $result = $updater->update();
            if (!$result) {
                return;
            }

            $new = $updater->getNewVersion();
            $old = $updater->getOldVersion();

            exit(sprintf(
                "Updated from SHA-1 %s to SHA-1 %s. Please run again\n", $old, $new
            ));
        } catch (\Exception $e) {
            echo "\nCheck your connection\n";
            exit(1);
        }
    }

    /**
     * @return bool
     */
    private function checkUpdateOnRun()
    {
        $config = $this->container->get('config');
        if (!isset($config['update']['checkOnStartup'])) {
            return false;
        }

        return $config['update']['checkOnStartup'];
    }
}
