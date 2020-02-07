<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Shopware\PluginCreator\Services;

use Shopware\PluginCreator\Struct\Configuration;

/**
 * The name generator generates useful names for controller, variables and views depending on the configuration object
 */
class NameGenerator
{
    // e.g. swag
    public $developerPrefix;

    // e.g. swag-promotion
    public $dash_js;

    // e.g. swag_promotion
    public $under_score_js;

    // e.g. promotion_test
    public $under_score_model;

    // e.g. PromotionTest
    public $camelCaseModel;

    // e.g.promotion
    public $backendModelAlias;

    // e.g. SwagTestWidget
    public $backendWidgetController;

    /**
     * @var Configuration
     */
    private $configuration;

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;

        $this->generateNames();
    }

    /**
     * Generate all needed names
     */
    public function generateNames(): void
    {
        $parts = $this->upperToArray($this->configuration->name);

        $backendModelParts = $this->upperToArray($this->getModelName());

        $this->backendModelAlias = $this->getBackendModelAlias();
        $this->camelCaseModel = implode('', $backendModelParts);
        $this->under_score_model = strtolower(implode('_', $backendModelParts));
        $this->under_score_js = strtolower(implode('_', $parts));
        $this->dash_js = strtolower(implode('-', $parts));
        $this->developerPrefix = $parts[0];
        $this->backendWidgetController = $this->configuration->name . 'Widget';
    }

    /**
     * Return a proper model name
     *
     * @return mixed|string
     */
    public function getModelName()
    {
        if (!$this->configuration->backendModel) {
            return implode('', \array_slice($this->upperToArray($this->configuration->name), 1));
        }

        $parts = explode('\\', $this->configuration->backendModel);

        return array_pop($parts);
    }

    /**
     * Determine the alias for the backend model
     */
    public function getBackendModelAlias(): string
    {
        if (!$this->configuration->backendModel) {
            return 'alias';
        }

        $parts = explode('\\', $this->configuration->backendModel);

        return strtolower(array_pop($parts));
    }

    /**
     * Splits a given string by upper case characters
     */
    public function upperToArray($input): array
    {
        return preg_split('/(?=[A-Z])/', $input, -1, PREG_SPLIT_NO_EMPTY);
    }
}
