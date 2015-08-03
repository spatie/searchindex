<?php namespace Spatie\SearchIndex;

interface Searchable {

    /**
     * Returns an array with properties which must be indexed
     *
     * @return array
     */
    public function getSearchableBody();

    /**
     * Return the type of the searchable subject
     *
     * @return string
     */
    public function getSearchableType();

    /**
     * Return the id of the searchable subject
     *
     * @return string
     */
    public function getSearchableId();

}
