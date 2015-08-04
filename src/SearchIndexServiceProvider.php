<?php

namespace Spatie\SearchIndex;

use Elasticsearch\Client as ElasticsearchClient;
use Exception;
use Illuminate\Support\ServiceProvider;
use Spatie\SearchIndex\SearchIndexHandlers\Algolia;
use Spatie\SearchIndex\SearchIndexHandlers\Elasticsearch as ElasticSearchHandler;

class SearchIndexServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../resources/config/searchindex.php' => $this->app->configPath().'/'.'searchindex.php',
        ], 'config');
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->app->singleton('searchIndex', function ($app) {
            switch ($app['config']->get('searchindex.engine')) {
                case 'elasticsearch':

                    $config = $app['config']->get('searchindex.elasticsearch');

                    $elasticSearchClient = new ElasticsearchClient(
                        [
                            'hosts' => $config['hosts'],
                            'logPath' => $config['logPath'],
                            'logLevel' => $config['logLevel'],
                        ]
                    );

                    $searchHandler = new ElasticSearchHandler($elasticSearchClient);

                    $searchHandler->setIndexName($config['defaultIndexName']);

                    return $searchHandler;

                    break;

                case 'algolia':

                    $config = $app['config']->get('searchindex.algolia');

                    $algoliaClient = new \AlgoliaSearch\Client(
                        $config['application-id'],
                        $config['api-key']
                    );

                    $searchHandler = new Algolia($algoliaClient);

                    $searchHandler->setIndexName($config['defaultIndexName']);

                    return $searchHandler;

                    break;
            }

            throw new Exception($app['config']->get('searchindex.engine').' is not a valid search engine');

        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('searchindex');
    }
}
