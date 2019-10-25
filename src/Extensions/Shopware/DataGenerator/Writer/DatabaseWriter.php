<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Shopware\DataGenerator\Writer;

use PDO;

/**
 * Class ShopwareWriter.
 */
class DatabaseWriter implements WriterInterface
{
    /**
     * @var array
     */
    private $config;

    /**
     * @var array
     */
    private $data = [];

    public function __construct($config, $resourceName)
    {
        $this->config = $config;
        $this->resourceName = $resourceName;
    }

    /**
     * {@inheritdoc}
     */
    public function write($content)
    {
        if (!is_array($content)) {
            $this->data[] = $content;
        } else {
            $this->data = array_merge($this->data, $content);
        }
    }

    /**
     * Flushes all known writer at once.
     */
    public function flush()
    {
        /** @var PDO $connection */
        $connection = $this->connectToDatabase($this->config);

        $connection->beginTransaction();

        foreach ($this->data as $query) {
            if (!$connection->query($query)) {
                $info = implode(',', $connection->errorInfo());
                throw new \Exception("Failed to execute $query\n\nCode: $info");
            }
        }
        $this->data = [];

        $connection->commit();
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return -10;
    }

    /**
     * @param $config
     *
     * @return PDO
     */
    private function connectToDatabase($config)
    {
        $connection = new PDO(
            'mysql:host=' . $config['host'] . ';dbname=' . $config['dbname'], $config['username'], $config['password'],
            [
                PDO::MYSQL_ATTR_LOCAL_INFILE => true, // if this still does not work, php5-mysqnd might work
                PDO::ERRMODE_EXCEPTION => 1,
            ]
        );

        return $connection;
    }
}
