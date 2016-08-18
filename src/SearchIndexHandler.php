<?php

namespace Spatie\SearchIndex;

interface SearchIndexHandler
{
    /**
     * Get the results for the given query.
     *
     * @param $query
     *
     * @return mixed
     */
    public function getResults($query);

    /**
     * Add or update the given searchable subject or array of subjects or Traversable object containing subjects.
     *
     * @param Searchable|array|Traversable $subject
     */
    public function upsertToIndex($subject);

    /**
     * Remove the given subject from the search index.
     *
     * @param Searchable $subject
     */
    public function removeFromIndex(Searchable $subject);

    /**
     * Remove an item from the search index by type and id.
     *
     * @param string $type
     * @param int    $id
     */
    public function removeFromIndexByTypeAndId($type, $id);

    /**
     * Remove everything from the index.
     *
     * @return mixed
     */
    public function clearIndex();

    /**
     * Get the underlying client.
     *
     * @return mixed
     */
    public function getClient();
}
