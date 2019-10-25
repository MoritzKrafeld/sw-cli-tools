<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ShopwareCli\Services\ZipUtil\Entry;

use ZipArchive;

/**
 * Class ZipArchive
 */
class Zip
{
    /**
     * @var int
     */
    protected $position;

    /**
     * @var ZipArchive
     */
    protected $stream;

    /**
     * @var string
     */
    protected $name;

    /**
     * @param ZipArchive $stream
     * @param int        $position
     */
    public function __construct($stream, $position)
    {
        $this->position = $position;
        $this->stream = $stream;
        $this->name = $stream->getNameIndex($position);
    }

    /**
     * @return resource
     */
    public function getStream()
    {
        return $this->stream->getStream($this->name);
    }

    /**
     * @return mixed
     */
    public function getContents()
    {
        return $this->stream->getFromIndex($this->position);
    }

    /**
     * @return bool
     */
    public function isDir()
    {
        return substr($this->name, -1) === '/';
    }

    /**
     * @return bool
     */
    public function isFile()
    {
        return substr($this->name, -1) !== '/';
    }

    /**
     * @return string
     */
    public function getName()
    {
        $name = $this->name;
        if (strpos($name, './') === 0) {
            $name = substr($name, 2);
        }

        return $name;
    }
}
