<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Shopware\Install;

use Shopware\Install\Command\ShopwareClearCacheCommand;
use Shopware\Install\Command\ShopwareInstallReleaseCommand;
use Shopware\Install\Command\ShopwareInstallVcsCommand;
use ShopwareCli\Application\ConsoleAwareExtension;
use ShopwareCli\Application\ContainerAwareExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * This plugin will install/setup shopware in a development version
 *
 * Class Bootstrap
 */
class Bootstrap implements ContainerAwareExtension, ConsoleAwareExtension
{
    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerBuilder $container)
    {
        $this->populateContainer($container);
    }

    /**
     * {@inheritdoc}
     */
    public function getConsoleCommands()
    {
        return [
            new ShopwareInstallVcsCommand(),
            new ShopwareInstallReleaseCommand(),
            new ShopwareClearCacheCommand(),
        ];
    }

    /**
     * @param ContainerBuilder $container
     */
    private function populateContainer(ContainerBuilder $container)
    {
        $container->register('shopware_checkout_service', 'Shopware\Install\Services\Checkout')
            ->addArgument(new Reference('git_util'))
            ->addArgument(new Reference('io_service'));

        $container->register('post_install', 'Shopware\Install\Services\PostInstall')
            ->addArgument(new Reference('process_executor'))
            ->addArgument(new Reference('shopware-install.owner'))
            ->addArgument(new Reference('config'))
            ->addArgument(new Reference('shopware_info'));

        $container->register('shopware_release_download_service', 'Shopware\Install\Services\ReleaseDownloader')
            ->addArgument(new Reference('process_executor'))
            ->addArgument(new Reference('io_service'))
            ->addArgument(new Reference('file_downloader'))
            ->addArgument(new Reference('openssl_verifier'))
            ->addArgument($container->get('path_provider')->getCachePath());

        $container->register('shopware-install.vcs_generator', 'Shopware\Install\Services\VcsGenerator')
            ->addArgument(new Reference('io_service'));

        $container->register('shopware-install.config_writer', 'Shopware\Install\Services\ConfigWriter')
            ->addArgument(new Reference('io_service'));

        $container->register('shopware-install.owner', 'Shopware\Install\Services\Owner');

        $container->register('shopware-install.database', 'Shopware\Install\Services\Database')
            ->addArgument(new Reference('utilities'))
            ->addArgument(new Reference('io_service'))
            ->addArgument(new Reference('process_executor'));

        $container->register('shopware-install.demodata', 'Shopware\Install\Services\Demodata')
            ->addArgument(new Reference('path_provider'))
            ->addArgument(new Reference('io_service'))
            ->addArgument(new Reference('shopware_info'))
            ->addArgument(new Reference('process_executor'));

        $container->register('shopware_vcs_install_service', 'Shopware\Install\Services\Install\Vcs')
            ->addArgument(new Reference('shopware_checkout_service'))
            ->addArgument(new Reference('config'))
            ->addArgument(new Reference('shopware-install.vcs_generator'))
            ->addArgument(new Reference('shopware-install.config_writer'))
            ->addArgument(new Reference('shopware-install.database'))
            ->addArgument(new Reference('shopware-install.demodata'))
            ->addArgument(new Reference('io_service'))
            ->addArgument(new Reference('post_install'));

        $container->register('shopware_release_install_service', 'Shopware\Install\Services\Install\Release')
            ->addArgument(new Reference('shopware_release_download_service'))
            ->addArgument(new Reference('config'))
            ->addArgument(new Reference('shopware-install.vcs_generator'))
            ->addArgument(new Reference('shopware-install.config_writer'))
            ->addArgument(new Reference('shopware-install.database'))
            ->addArgument(new Reference('shopware-install.demodata'))
            ->addArgument(new Reference('io_service'))
            ->addArgument(new Reference('post_install'))
            ->addArgument(new Reference('process_executor'));
    }
}
