<?php

namespace Spatie\SearchIndex\SearchIndexHandlers;

use AlgoliaSearch\Client;
use Spatie\SearchIndex\Searchable;
use Spatie\SearchIndex\SearchIndexHandler;

class Algolia implements SearchIndexHandler
{
    /**
     * @var \AlgoliaSearch\Client
     */
    protected $algolia;

    /**
     * @var \AlgoliaSearch\Index
     */
    protected $index;

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
        $this->index = $this->algolia->setIndexName($indexName);

        return $this;
    }

    /**
     * Add or update the given searchable subject to the index.
     *
     * @param Searchable $subject
     */
    public function upsertToIndex(Searchable $subject)
    {
        $this->index->saveObject(
            array_merge(
                $subject->getSearchableBody(),
                $this->getIdArray($subject)
            )
        );
    }

    /**
     * Remove the given subject from the search index.
     *
     * @param Searchable $subject
     */
    public function removeFromIndex(Searchable $subject)
    {
        $this->index->deleteObject($this->getIdArray($subject));
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
     * @param array $query
     *
     * @return mixed
     */
    public function getResults($query)
    {
        return $this->algolia->search($query);
    }

    /**
     * Get the id parameter that is used by Algolia.
     *
     * @param Searchable $subject
     *
     * @return array
     */
    protected function getIdArray(Searchable $subject)
    {
        return ['objectID' => $subject->getSearchableType().'-'.$subject->getSearchableId()];
    }
}
