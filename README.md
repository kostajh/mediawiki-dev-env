# MediaWiki DevEnv

Experimental project to combine best parts of various MediaWiki development environments.

tl;dr: PHP process on the host, containers for everything else.

Use the host machine for PHP (speed and ease of use with configuring XDebug, IDE and tools), and containers for heavy lifting of more complicated services (MySQL, Redis, ElasticSearch etc).

## Install 

Clone MediaWiki core. Create a `composer.local.json` file with these contents:

``` json
{
  "extra": {
    "merge-plugin": {
      "include": [
        "extensions/*/composer.json",
        "skins/*/composer.json"
      ]
    },
    "installer-paths": {
      "skins/{$name}/": [
        "type:mediawiki-skin"
      ]
    }
  },
  "require": {
    "mediawiki/vector-skin": "dev-master"
  },
  "require-dev": {
	"kostajh/mediawiki-dev-env": "dev-master"
  }
}
```

Run `composer update`.

## Usage

Install MediaWiki: `vendor/bin/mwdev install` 

Serve MediaWiki: `vendor/bin/mwdev serve`. Run `vendor/bin/mwdev serve --help` to see how to run with additional services.

The local site URL is `http://127.0.0.1:9412`

## Design goals

- Discoverability / ease of use: Project could theoretically become part of require-dev in upstream MediaWiki core's composer.json, but in the meantime it is straightforward to instruct users how to include it as part of their existing Composer setup
- Performance: By not using a container for PHP we sacrifice some ability to reproduce environments in the interest of speed
- Extensible: It should be easy for developers to add additional services using docker-compose to this repo, and for end users to override provided services (via docker-compose.override.yml)
- Ease of use: Abstract away the tedious bits of `maintenance/install.php` and craft a `LocalSettings.php` file when possible
- Reuse in CI: The same install/serve commands should be usable in CI for running integration and end-to-end tests

### Inspiration

- ["QuickWiki"](https://wikitech.wikimedia.org/wiki/Performance/Fresnel#Quick_MediaWiki)
- [Symfony local server](https://symfony.com/doc/current/setup/symfony_server.html)
- [MediaWiki-Docker-Dev](https://www.mediawiki.org/wiki/MediaWiki-Docker-Dev)
- [local-charts](https://gerrit.wikimedia.org/r/plugins/gitiles/releng/local-charts/)
- [Wikimedia TechConf discussion](https://phabricator.wikimedia.org/T238224)

## Limitations / Caveats

- PHP's built-in server is single threaded. That means if you do things like issue a curl request from within MediaWiki to the same MediaWiki instance, that request will timeout.

## Roadmap

- [ ] Fix the crufty integration with `docker-compose` commands.
- [ ] MySQL container
- [ ] Replicated MySQL containers (1 master, one replica)
- [X] ElasticSearch container
- [ ] Lightweight proxy for nicer domain names and/or port 80
