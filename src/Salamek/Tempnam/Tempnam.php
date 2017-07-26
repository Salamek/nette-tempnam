<?php

namespace Salamek\Tempnam;

use Nette\Caching\Cache;
use Nette\Caching\IStorage;
use Nette\DirectoryNotFoundException;
use Nette\Object;
use Nette\Utils\Finder;
use Nette\Utils\Strings;

/**
 * Class Tempnam
 * @package Salamek\Tempnam
 */
class Tempnam extends Object
{
    /** @var float  probability that the clean() routine is started */
    public static $gcProbability = 0.001;

    /** @var string */
    private $tempDir;

    /**
     * @var string
     */
    private $namespace;

    /** @var Cache */
    private $cache;

    /**
     * Tempnam constructor.
     * @param $tempDir
     * @param string $namespace
     * @param IStorage $storage
     */
    public function __construct($tempDir, $namespace = '_', IStorage $storage)
    {
        if (!is_dir($tempDir)) {
            throw new DirectoryNotFoundException(sprintf('Directory %s not found.', $tempDir));
        }

        $this->tempDir = $tempDir;

        $this->namespace = $namespace;
        $this->cache = new Cache($storage, strtr(__CLASS__, ['\\' => '.']));

        if (mt_rand() / mt_getrandmax() < static::$gcProbability) {
            $this->clean();
        }
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
     * @param $key
     * @return mixed
     */
    private function generateKey($key)
    {
        return $this->namespace.md5(is_scalar($key) ? (string) $key : serialize($key));
    }
    
    /**
     * @param $filename
     * @return string
     */
    private function getFilePath($filename)
    {
        return $this->joinPaths($this->tempDir, $filename);
    }

    /**
     * @param $filename
     * @param $data
     * @return string
     */
    private function putFile($filename, $data)
    {
        $path = $this->getFilePath($filename);
        file_put_contents($path, $data);
        return $path;
    }

    /**
     * Removes tempnam file by its key
     * @param $key string tempnam key
     */
    public function remove($key)
    {
        $keyGen = $this->generateKey($key);
        $this->cache->remove($keyGen);
        @unlink($this->getFilePath($keyGen));
    }

    /**
     * Loads tempnam file path
     * @param $key string tempnam key
     * @param \DateTimeInterface|null $updatedAt When data was last updated
     * @return null|string path to tempnam
     */
    public function load($key, \DateTimeInterface $updatedAt = null)
    {
        $keyGen = $this->generateKey($key);
        $updateDate = $this->cache->load($keyGen);

        if ($updateDate === null || $updateDate != $updatedAt)
        {
            if ($updateDate)
            {
                $this->remove($key);
            }
            return null;
        }

        return $this->getFilePath($keyGen);
    }

    /**
     * Saves tempnam file and returns its path
     * @param $key string tempnam key
     * @param $data string content of file
     * @param \DateTimeInterface|null $updatedAt When data was last updated
     * @return string path to tempnam
     */
    public function save($key, $data, \DateTimeInterface $updatedAt = null)
    {
        $keyGen = $this->generateKey($key);
        $path = $this->putFile($keyGen, $data);
        $this->cache->save($keyGen, $updatedAt);
        return $path;
    }

    /**
     * Cleans unused tempnam files
     * @return void
     */
    private function clean()
    {
        foreach (Finder::find($this->namespace.'*')->from($this->tempDir)->childFirst() as $entry) {
            $path = (string) $entry;
            if ($entry->isDir()) {
                //We dont use dirs, ignore
                continue;
            }

            $updateDate = $this->cache->load($entry->getFilename());
            if ($updateDate === null)
            {
                @unlink($path);
            }
        }

        return;
    }
}
