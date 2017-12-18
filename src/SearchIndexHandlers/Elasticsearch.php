<?php

namespace Spatie\SearchIndex\SearchIndexHandlers;

use Elasticsearch\Client;
use InvalidArgumentException;
use Spatie\SearchIndex\Searchable;
use Spatie\SearchIndex\SearchIndexHandler;
use Traversable;

class Elasticsearch implements SearchIndexHandler
{
    /** @var Elasticsearch */
    protected $elasticsearch;

    /** @var string */
    protected $indexName;

    /**
     * @param Client $elasticsearch
     */
    public function __construct(Client $elasticsearch)
    {
        $this->elasticsearch = $elasticsearch;
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
        $this->indexName = $indexName;

        return $this;
    }

    /**
     * Add or update the given searchable subject or array of subjects or Traversable object containing subjects.
     *
     * @param Searchable|array|Traversable $subject
     */
    public function upsertToIndex($subject, $indexName = null)
    {
        $indexName = $this->resolveIndexName($indexName);

        if ($subject instanceof Searchable) {
            $subject = [$subject];
        }

        if (is_array($subject) || $subject instanceof Traversable) {
            $searchableItems = collect($subject)
                ->each(function ($item) {
                    if (!$item instanceof Searchable) {
                        throw new InvalidArgumentException();
                    }
                })
                ->flatMap(function ($item) use($indexName) {
                    return
                        [
                            [
                                'index' => [
                                    '_id'    => $item->getSearchableId(),
                                    '_index' => $indexName,
                                    '_type'  => $item->getSearchableType(),
                                ],
                            ],

                            $item->getSearchableBody(),
                        ];
                })
                ->toArray();

            $payload['body'] = $searchableItems;

            $this->elasticsearch->bulk($payload);

            return;
        }

        throw new InvalidArgumentException('Subject must be a searchable or array of searchables');
    }

    /**
     * Remove the given subject from the search index.
     *
     * @param Searchable $subject
     */
    public function removeFromIndex(Searchable $subject, $indexName = null)
    {
        $indexName = $this->resolveIndexName($indexName);

        $this->elasticsearch->delete(
            [
                'index' => $indexName,
                'type'  => $subject->getSearchableType(),
                'id'    => $subject->getSearchableId(),
            ]
        );
    }

    /**
     * Remove an item from the search index by type and id.
     *
     * @param string $type
     * @param int    $id
     */
    public function removeFromIndexByTypeAndId($type, $id, $indexName = null)
    {
        $indexName = $this->resolveIndexName($indexName);

        $this->elasticsearch->delete(
            [
                'index' => $indexName,
                'type'  => $type,
                'id'    => $id,
            ]
        );
    }

    /**
     * Remove everything from the index.
     *
     * @return mixed
     */
    public function clearIndex($indexName = null)
    {
        $indexName = $this->resolveIndexName($indexName);

        $this->elasticsearch->indices()->delete(['index' => $indexName]);
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
        return $this->elasticsearch->search($query);
    }

    /**
     * Get the underlying client.
     *
     * @return Elasticsearch
     */
    public function getClient()
    {
        return $this->elasticsearch;
    }

    protected function resolveIndexName($indexName = null)
    {
        if (!$indexName) {
            $indexName = $this->indexName;
        }

        return $indexName;
    }
}
