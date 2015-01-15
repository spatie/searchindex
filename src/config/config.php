<?php

return
    [
        'engine' => 'elasticsearch',

        'elasticsearch' =>
            [
                'hosts' =>
                    [
                        'localhost:9200',
                    ],

                'logPath' =>  storage_path() . '/logs/elasticsearch.log',
                'logLevel' => 200,
                'defaultIndexName' => 'main'
            ]
    ];
