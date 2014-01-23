<?php

return array(

    /*
    |--------------------------------------------------------------------------
    | Files to watch
    |--------------------------------------------------------------------------
    |
    | For each key contained in the array 'files_to_watch' an anonymous
    | function must be declared. This function is performed by sending the
    | filename as a parameter whenever a file that matches the specified
    | key is modified.
    |
    */
    'files_to_watch' => array(

        /** Examples:

        '../*.less' => function($file) {
            // Using node.js less compiler
            echo "A LESS file has been modified! Compiling 'main.less'.\n";
            exec('lessc my/assets/path/main.less > .my/assets/path/main.css');
        },
        '../*.js' => function($file) {
            // Using a random class that minifies a file
            $minifier = new CrazyJsMinifier;
            $minifier->minifies($file);
        },
        'models/*.php' => function($file) {
            // Run model tests when a model class is modified
            exec('phpunit app/tests/models');
        },
        'views/*blade.php' => function($file) {
            // Flushs application cache when a blade file has been modified
            echo "A view file ($file) has been modified!\nCleaning cache\n";
            Artisan::call('cache:clear');
        },

        **/
    )
);
