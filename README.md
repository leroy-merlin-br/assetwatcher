# AssetWatcher (Laravel Package)
Trigger specific commands when specified files are modified. _I.E.:
You can easly configure an asset pipeline (compilation, concatenation,
minification), run tests, etc._

## Quick start

### Required setup

In the `require` key of `composer.json` file add the following

    "leroy-merlin-br/assetwatcher": "dev-master"

Run the Composer update comand

    $ composer update

In your `config/app.php` add `'LeroyMerlin\AssetWatcher\AssetWatcherServiceProvider'` to the end of the `$providers` array

    'providers' => array(

        'Illuminate\Foundation\Providers\ArtisanServiceProvider',
        'Illuminate\Auth\AuthServiceProvider',
        ...
        'LeroyMerlin\AssetWatcher\AssetWatcherServiceProvider',

    ),

Publish the AssetWatcher configuration file:

    php artisan config:publish leroy-merlin-br/assetwatcher

Edit the configuration file `app/config/packages/leroy-merlin-br/assetwatcher/config.php` adding the behavior you want.

## Examples of use

### LESS compilation

First, you need the **LESS compiler** installed:

    $ sudo npm install less

Then, in order to compile the `app/assets/less/main.less` file if **any** LESS file within `assets/less` has been modified. The config file should be like:

```php
    'files_to_watch' => array(

        'assets/less/*.less' => function($file) {
            // Using node.js less compiler
            echo "A LESS file has been modified! Compiling 'main.less'.\n";
            exec('lessc app/assets/less/main.less > public/assets/css/main.css');
        },
```

## License

AssetWatcher is free software distributed under the terms of the MIT license

## Aditional information

Any issues, please [report here](https://github.com/Zizaco/pulse/issues)
