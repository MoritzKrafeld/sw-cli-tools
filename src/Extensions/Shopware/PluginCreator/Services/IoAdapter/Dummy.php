<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Shopware\PluginCreator\Services\IoAdapter;

/**
 * Dummy IoAdapter will collect all files in memory
 *
 * Class Dummy
 */
class Dummy implements IoAdapter
{
    protected $files = [];

    /**
     * @param $path
     *
     * @return bool
     */
    public function exists($path)
    {
        return false;
    }

    public function createDirectory($dir)
    {
        return true;
    }

    public function createFile($file, $content)
    {
        $this->files[$file] = $content;
    }

    /**
     * @return array
     */
    public function getFiles()
    {
        return $this->files;
    }
}
