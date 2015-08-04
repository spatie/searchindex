<?php

return
    [
        /*
         * The engine behind the search index. Currently the only valid option is elasticsearch
         */
        'engine' => 'elasticsearch',

        'elasticsearch' =>
            [
                /*
                 * Specify the host(s) where elasticsearch is running.
                 */
                'hosts' =>
                    [
                        'localhost:9200',
                    ],

                /*
                 * Specify the path where Elasticsearch will write it's logs.
                 */
                'logPath' =>  storage_path() . '/logs/elasticsearch.log',

                /*
                 * Specify how verbose the logging must be
                 * Possible values are listed here
                 * https://github.com/Seldaek/monolog/blob/master/src/Monolog/Logger.php
                 *
                 */
                'logLevel' => 200,

                /*
                 * The name of the index elasticsearch will write to.
                 */
                'defaultIndexName' => 'main'
            ],

        'algolia' =>
            [
                /*
                 * This index will be used whenever you don't explicitly
                 * set one yourself.
                 */
                'defaultIndexName' => 'main',

                /*
                 * You'll find the right values on the Algolia credentials page.
                 */
                'application-id' => env('ALGOLIA_APPLICATION_ID'),

                'api-key' => env('ALGOLIA_ADMIN_API_KEY'),

                'search-only-api-key' => env('ALGOLIA_SEARCH_ONLY_API_KEY'),
            ],
    ];
