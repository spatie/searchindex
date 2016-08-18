<?php

namespace Spatie\SearchIndex\SearchIndexHandlers;

use AlgoliaSearch\Client;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use Spatie\SearchIndex\Query\Algolia\SearchQuery;
use Spatie\SearchIndex\Searchable;
use Spatie\SearchIndex\SearchIndexHandler;
use Traversable;

class Algolia implements SearchIndexHandler
{
    /** @var \AlgoliaSearch\Client */
    protected $algolia;

    /** @var \AlgoliaSearch\Index */
    public $index;

    public function __construct(Client $algolia)
    {
        $this->algolia = $algolia;
    }

    /**
     * Set the name of the index that should be used by default.
     *
     * @param $indexName
     *
     * @return $this
     */
    public function setIndexName($indexName)
    {
        $this->index = $this->algolia->initIndex($indexName);

        return $this;
    }

    /**
     * Add or update the given searchable subject or array of subjects or Traversable object containing subjects.
     *
     * @param Searchable|array|Traversable $subject
     */
    public function upsertToIndex($subject)
    {
        if ($subject instanceof Searchable) {

            $subject = [$subject];
        }

        if (is_array($subject) || $subject instanceof Traversable) {
            $searchIndexPayload = collect($subject)
                ->each(function ($item) {
                    if (!$item instanceof Searchable) {
                        throw new InvalidArgumentException;
                    }
                })
                ->map(function ($item) {
                    return array_merge(
                        $item->getSearchableBody(),
                        ['objectID' => $this->getAlgoliaId($item)]
                    );
                })
                ->toArray();

            $this->index->saveObjects($searchIndexPayload);

            return;
        }

        throw new InvalidArgumentException('Subject must be a searchable or array of searchables');

    }

    /**
     * Remove the given subject from the search index.
     *
     * @param Searchable $subject
     */
    public function removeFromIndex(Searchable $subject)
    {
        $this->index->deleteObject($this->getAlgoliaId($subject));
    }

    /**
     * Remove an item from the search index by type and id.
     *
     * @param string $type
     * @param int $id
     */
    public function removeFromIndexByTypeAndId($type, $id)
    {
        $this->index->deleteObject($type . '-' . $id);
    }

    /**
     * Remove everything from the index.
     *
     * @return mixed
     */
    public function clearIndex()
    {
        $this->index->clearIndex();
    }

    /**
     * Get the results for the given query.
     *
     * @param string|array|\Spatie\SearchIndex\Query\Algolia\SearchQuery $query
     *
     * @return mixed
     */
    public function getResults($query)
    {
        $parameters = [];

        if (is_object($query) && $query instanceof SearchQuery) {
            $query = $query->toArray();
        }

        if (is_array($query)) {
            $collection = new Collection($query);

            $query = $collection->pull('query', '');

            $parameters = $collection->toArray();
        }

        return $this->index->search($query, $parameters);
    }

    /**
     * Get the id parameter that is used by Algolia as an array.
     *
     * @param Searchable $subject
     *
     * @return string
     */
    protected function getAlgoliaId($subject)
    {
        return $subject->getSearchableType() . '-' . $subject->getSearchableId();
    }

    /**
     * Get the underlying client.
     *
     * @return \AlgoliaSearch\Client
     */
    public function getClient()
    {
        return $this->algolia;
    }
}
