# Nette tempnam

This is a simple tempnam extension for [Nette Framework](http://nette.org/)

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
