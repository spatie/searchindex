<?php namespace Spatie\SearchIndex;

use Elasticsearch\Client as ElasticsearchClient;
use Exception;
use Illuminate\Support\ServiceProvider;
use Spatie\SearchIndex\SearchIndexHandlers\Elasticsearch as SearchHandler;

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
	 *
	 * @return void
	 */

	public function boot()
	{
		$this->publishes([
			__DIR__.'/../../config/searchindex.php' => config_path('searchindex.php'),
		], 'config');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{

		$this->app->singleton('searchIndex', function($app)
		{
			switch($app['config']->get('searchindex.engine'))
			{
				case 'elasticsearch':

					$config = $app['config']->get('searchindex.elasticsearch');

					$elasticSearchClient = new ElasticsearchClient(
						[
							'hosts' => $config['hosts'],
							'logPath' => $config['logPath'],
							'logLevel' => $config['logLevel']
						]
					);

					$searchHandler = new SearchHandler($elasticSearchClient);

					$searchHandler->setIndexName($config['defaultIndexName']);

					return $searchHandler;
			}

			throw new Exception($app['config']->get('searchindexvend.engine') . ' is not a valid search engine');

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
