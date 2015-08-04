<?php

namespace spec\Spatie\SearchIndex\SearchIndexHandlers;

use AlgoliaSearch\Client;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Spatie\SearchIndex\Searchable;
use Spatie\SearchIndex\SearchIndexHandlers\Algolia;

class AlgoliaSpec extends ObjectBehavior
{

    protected $indexName;

    protected $searchableBody;
    protected $searchableType;
    protected $searchableId;

    protected $searchableObject;

    public function __construct()
    {
        $this->indexName = 'indexName';

        $this->searchableBody = ['body' => 'test'];
        $this->searchableType = 'product';
        $this->searchableId = 1;
    }

    function let(Client $algoliaClient, Searchable $searchableObject, \AlgoliaSearch\Index $index)
    {
        $searchableObject->getSearchableBody()->willReturn($this->searchableBody);
        $searchableObject->getSearchableType()->willReturn($this->searchableType);
        $searchableObject->getSearchableId()->willReturn($this->searchableId);

        $this->index = $index;

        $this->beConstructedWith($algoliaClient);

        $algoliaClient->initIndex($this->indexName)->willReturn($this->index);
var_dump($algoliaClient); die();


        $this->setIndexName($this->indexName);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Algolia::class);
    }

    function it_adds_a_searchable_object_to_the_search_index(Client $algolia, Searchable $searchableObject)
    {
        $this->index->saveObject(
            array_merge(
                $this->searchableBody,
                ['objectID' => $this->searchableId.'-'.$this->searchableType]
            )
        )->shouldBeCalled();

        $this->upsertToIndex($searchableObject);
    }

    function it_removes_a_searchable_object_from_the_index(Client $algolia, Searchable $searchableObject)
    {
        $algolia->delete(
            [
                'index' => $this->indexName,
                'type' => $this->searchableType,
                'id' => $this->searchableId,
            ]
        )->shouldBeCalled();

        $this->removeFromIndex($searchableObject);
    }

    /*
     * Need to figure how to test the clearIndex function
     *
        function it_can_clear_the_index(Client $algolia)
        {

            $algolia->indices()->delete(['index' => $this->indexName]);

            $this->clearIndex();
        }
    */


    function it_can_get_search_results(Client $algolia)
    {
        $query = 'this is a testquery';

        $algolia->search($query)->shouldBeCalled();

        $this->getResults($query);
    }

}
