# Contributing

Contributions (bug reports, feature requests, patches) are very welcome!

## Hacking

Clone MediaWiki core somewhere.

Clone this repository somewhere else.

Create a `composer.local.json` file with these contents:

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
  "repositories": {
    "mediawiki-dev-env": {
      "type": "path",
      "url": "/path/to/where/you/cloned/mediawiki-dev-env",
      "options": {
        "symlink": true
      }
    }
  }
}
```

Then run `composer update` and `composer require --dev kostajh/mediawiki-dev-env @dev` inside MediaWiki core. You can now make changes to `mediawiki-dev-env` and they'll show up when you run `vendor/bin/mwdev` inside the MediaWiki core repo.
