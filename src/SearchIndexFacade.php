<?php namespace Spatie\SearchIndex;

use Illuminate\Support\Facades\Facade;

class SearchIndexFacade extends Facade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'searchIndex';

    }

}
