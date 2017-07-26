<?php

namespace Salamek\Tempnam;

use Nette\Caching\Cache;
use Nette\Caching\IStorage;
use Nette\Object;
use Nette\Utils\Strings;

/**
 * Class Tempnam
 * @package Salamek\Tempnam
 */
class Tempnam extends Object
{
    /** @var string */
    private $tempDir;

    /** @var Cache */
    private $cache;

    /**
     * Tempnam constructor.
     * @param $tempDir
     * @param IStorage $storage
     */
    public function __construct($tempDir, IStorage $storage)
    {
        $this->tempDir = $tempDir;
        $this->cache = new Cache($storage, __CLASS__);
    }

    /**
     * @return string
     */
    public function getTempDir()
    {
        return $this->tempDir;
    }

    /**
     * @param $path
     * @param $path2
     * @return string
     */
    private function joinPaths($path, $path2)
    {
        if (!Strings::endsWith($path, '/'))
        {
            $path = $path.'/';
        }

        if (Strings::startsWith($path2, '/'))
        {
            $path2 = ltrim($path2, '/');
        }

        return $path.$path2;
    }

    /**
     * @param $filename
     * @return string
     */
    public function getFilePath($filename)
    {
        return $this->joinPaths($this->tempDir, $filename);
    }

    /**
     * @param $filename
     * @param $data
     * @return string
     */
    public function putFile($filename, $data)
    {
        $path = $this->getFilePath($filename);
        file_put_contents($path, $data);
        return $path;
    }

    /**
     * @param $filename
     * @return mixed
     */
    public function getFile($filename)
    {
        return file_get_contents($this->getFilePath($filename));
    }

    /**
     * @param $key
     * @param \DateTimeInterface|null $updatedAt
     * @return null|string
     */
    public function load($key, \DateTimeInterface $updatedAt = null)
    {
        $updateDate = $this->cache->load($key);

        if ($updateDate === null || $updateDate != $updatedAt)
        {
            return null;
        }

        return $this->getFilePath($key);
    }

    /**
     * @param $key
     * @param $data
     * @param \DateTimeInterface|null $updatedAt
     * @return string
     */
    public function save($key, $data, \DateTimeInterface $updatedAt = null)
    {
        $path = $this->putFile($key, $data);
        $this->cache->save($key, $updatedAt);
        return $path;
    }
}
