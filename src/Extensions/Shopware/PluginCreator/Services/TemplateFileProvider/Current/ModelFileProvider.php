<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Shopware\PluginCreator\Services\TemplateFileProvider\Current;

use Shopware\PluginCreator\Services\NameGenerator;
use Shopware\PluginCreator\Services\TemplateFileProvider\FileProviderInterface;
use Shopware\PluginCreator\Struct\Configuration;

/**
 * Class ModelFileProvider returns model related files
 */
class ModelFileProvider implements FileProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getFiles(Configuration $configuration, NameGenerator $nameGenerator)
    {
        if (!$configuration->hasModels) {
            return [];
        }

        return [
            self::CURRENT_DIR . 'Models/Model.tpl' => "Models/{$nameGenerator->camelCaseModel}.php",
            self::CURRENT_DIR . 'Models/Repository.tpl' => 'Models/Repository.php',
        ];
    }
}
