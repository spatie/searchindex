<?php namespace Spatie\SearchIndex;

interface SearchIndexHandler {

    /**
     * Get the results for the given query
     *
     * @param $query
     * @return mixed
     */
    public function getResults($query);

    /**
     * Add or update the given searchable subject to the index
     *
     * @param Searchable $subject
     */
    public function upsertToIndex(Searchable $subject);


    /**
     * Remove the given subject from the search index
     *
     * @param Searchable $subject
     */
    public function removeFromIndex(Searchable $subject);


    /**
     * Remove everything from the index
     *
     * @return mixed
     */
    public function clearIndex();

}
