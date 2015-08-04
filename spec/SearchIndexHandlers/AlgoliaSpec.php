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

        $this->beConstructedWith($algoliaClient);

        $this->index = $index;
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Algolia::class);
    }

    function it_adds_a_searchable_object_to_the_search_index(\AlgoliaSearch\Index $index, Searchable $searchableObject)
    {
        $index->saveObject(
            array_merge(
                $this->searchableBody,
                ['objectID' => $this->searchableType.'-'.$this->searchableId]
            )
        )->shouldBeCalled();

        $this->upsertToIndex($searchableObject);
    }

    function it_removes_a_searchable_object_from_the_index(\AlgoliaSearch\Index $index, Searchable $searchableObject)
    {
        $index->deleteObject($this->searchableType.'-'.$this->searchableId)->shouldBeCalled();

        $this->removeFromIndex($searchableObject);
    }


        function it_can_clear_the_index(\AlgoliaSearch\Index $index)
        {

            $index->clearIndex()->shouldBeCalled();

            $this->clearIndex();
        }
    


    function it_can_get_search_results(\AlgoliaSearch\Index $index)
    {
        $query = 'this is a testquery';

        $index->search($query)->shouldBeCalled();

        $this->getResults($query);
    }

}
