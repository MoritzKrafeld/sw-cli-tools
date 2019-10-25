<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ShopwareCli\Application;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\DependencyInjection\Reference;

class DependencyInjection
{
    const DEFAULT_PROCESS_TIMEOUT = 180;

    /**
     * @param $rootDir
     *
     * @return ContainerBuilder
     */
    public static function createContainer($rootDir)
    {
        $container = new ContainerBuilder(
            new ParameterBag(['kernel.root_dir' => $rootDir])
        );

        // synthetic services
        $container->setDefinition('autoloader', new Definition('Composer\Autoload\ClassLoader'))->setSynthetic(true);
        $container->setDefinition('input_interface', new Definition('Symfony\Component\Console\Input\InputInterface'))->setSynthetic(true);
        $container->setDefinition('output_interface', new Definition('Symfony\Component\Console\Input\InputInterface'))->setSynthetic(true);
        $container->setDefinition('question_helper', new Definition('Symfony\Component\Console\Helper\QuestionHelper'))->setSynthetic(true);

        $container->register('io_service', 'ShopwareCli\Services\IoService')
            ->addArgument(new Reference('input_interface'))
            ->addArgument(new Reference('output_interface'))
            ->addArgument(new Reference('question_helper'));

        $container->register('process_executor', 'ShopwareCli\Services\ProcessExecutor')
            ->addArgument(new Reference('output_interface'))
            ->addArgument(getenv('SW_TIMEOUT') ?: self::DEFAULT_PROCESS_TIMEOUT);

        $container->register('git_identity_environment', 'ShopwareCli\Services\GitIdentityEnvironment')
            ->addArgument(new Reference('path_provider'))
            ->addArgument(new Reference('config'));

        $container->register('git_util', 'ShopwareCli\Services\GitUtil')
                ->addArgument(new Reference('output_interface'))
                ->addArgument(new Reference('git_identity_environment'))
                ->addArgument(getenv('SW_TIMEOUT') ?: self::DEFAULT_PROCESS_TIMEOUT);

        $container->register('utilities', 'ShopwareCli\Utilities')
            ->addArgument(new Reference('io_service'));

        $container->register('xdg', '\XdgBaseDir\Xdg');

        $container->register('plugin_info', '\Shopware\PluginInfo\PluginInfo');

        $container->register('directory_gateway', 'ShopwareCli\Services\PathProvider\DirectoryGateway\XdgGateway')
            ->addArgument(new Reference('xdg'));

        $container->register('file_downloader', 'ShopwareCli\Services\StreamFileDownloader')
                ->addArgument(new Reference('io_service'));

        $container->register('path_provider', 'ShopwareCli\Services\PathProvider\PathProvider')
            ->addArgument(new Reference('directory_gateway'));

        $container->register('cache', 'ShopwareCli\Cache\File')
            ->addArgument($container->get('path_provider'));

        $container->register('rest_service_factory', 'ShopwareCli\Services\Rest\RestServiceFactory')
            ->addArgument(new Reference('service_container'));

        $container->register('config_file_collector', 'ShopwareCli\ConfigFileCollector')
            ->addArgument(new Reference('path_provider'));

        $container->register('config', 'ShopwareCli\Config')
            ->addArgument(new Reference('config_file_collector'));

        $container->register('extension_manager', 'ShopwareCli\Application\ExtensionManager')
            ->addArgument(new Reference('autoloader'));

        $container->register('command_manager', 'ShopwareCli\Application\CommandManager')
            ->addArgument(new Reference('extension_manager'))
            ->addArgument(new Reference('service_container'));

        $container->register('openssl_verifier', 'ShopwareCli\Services\OpenSSLVerifier')
            ->addArgument('%kernel.root_dir%/Resources/public.key');

        $container->register('shopware_info', 'ShopwareCli\Services\ShopwareInfo');

        return $container;
    }
}
