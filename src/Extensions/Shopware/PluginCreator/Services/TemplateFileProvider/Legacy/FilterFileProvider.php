<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Shopware\PluginCreator\Services\TemplateFileProvider\Legacy;

use Shopware\PluginCreator\Services\NameGenerator;
use Shopware\PluginCreator\Services\TemplateFileProvider\FileProviderInterface;
use Shopware\PluginCreator\Struct\Configuration;

/**
 * Class FilterFileProvider returns files needed for the frontend filter
 */
class FilterFileProvider implements FileProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getFiles(Configuration $configuration, NameGenerator $nameGenerator)
    {
        if (!$configuration->hasFilter) {
            return [];
        }

        return [
            self::LEGACY_DIR . 'Components/SearchBundleDBAL/Condition/Condition.tpl' => "Components/SearchBundleDBAL/Condition/{$configuration->name}Condition.php",
            self::LEGACY_DIR . 'Components/SearchBundleDBAL/Condition/ConditionHandler.tpl' => "Components/SearchBundleDBAL/Condition/{$configuration->name}ConditionHandler.php",
            self::LEGACY_DIR . 'Components/SearchBundleDBAL/Facet/Facet.tpl' => "Components/SearchBundleDBAL/Facet/{$configuration->name}Facet.php",
            self::LEGACY_DIR . 'Components/SearchBundleDBAL/Facet/FacetHandler.tpl' => "Components/SearchBundleDBAL/Facet/{$configuration->name}FacetHandler.php",
            self::LEGACY_DIR . 'Components/SearchBundleDBAL/CriteriaRequestHandler.tpl' => "Components/SearchBundleDBAL/{$configuration->name}CriteriaRequestHandler.php",
            self::LEGACY_DIR . 'Subscriber/SearchBundle.tpl' => 'Subscriber/SearchBundle.php',
        ];
    }
}
