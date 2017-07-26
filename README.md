# Nette tempnam

This is a simple tempnam extension for [Nette Framework](http://nette.org/)
It creates temp files in %tempDir%/tempnam

## Why ?
Sometines you just need to create tempfile such as PDF, Latte templates etc... and you dont want to put them into `sys_get_temp_dir()` (/tmp) and keep them in your Nette project %tempDir%... or just acces same temp file multiple times

## Instalation

The best way to install salamek/nette-tempnam is using  [Composer](http://getcomposer.org/):


```sh
$ composer require salamek/nette-tempnam:@dev
```

Then you have to register extension in `config.neon`.

```yaml
extensions:
    tempnam: Salamek\Tempnam\DI\TempnamExtension
```

If you wish to change tempnam path just add this to your config:

```yaml
tempnam:
   tempDir: %tempDir%/tempnam
```


## Usage example

```php

/** @var Salamek\Tempnam\Tempnam @inject */
public $tempnam;

$key = 'my_tempnam_key_1'; // Lets say ID of record in database
$data = 'My File COntent'; // Lets say record from database we want as file
$updatedAt = new \DateTime('YYYY-mm-dd'); // Lets say updatedAt column from database to expire tempnam file when record in database is changed

// Load temFile Path or null
$tempFile = $this->tempnam->load($key, $updatedAt);


if ($tempFile === null)
{
    $tempFile = $this->tempnam->save($key, $data, $updatedAt);
}

echo file_get_contents($tempFile); // My File COntent

```

## Methods

```php 
$this->tempnam->getTempDir(); //Returns tempDir
$this->tempnam->remove($key); //Removes tempnam file by its $key
$this->tempnam->load($key, \DateTimeInterface $updatedAt = null); //Returns tempnam file path by its key if updatedAt matches or returns null
$this->tempnam->save($key, $data, \DateTimeInterface $updatedAt = null) //Saves tempnam file by $key with $data content, returns tempnam path
```


