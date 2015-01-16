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
                 * Specify the host(s) where elasticsearch is running
                 */
                'hosts' =>
                    [
                        'localhost:9200',
                    ],

                /*
                 * specify the path where elasticsearch will write it's logs
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
                 * The name of the index elasticsearch will write to
                 */
                'defaultIndexName' => 'main'
            ]
    ];
