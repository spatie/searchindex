<?php

namespace spec\Spatie\SearchIndex\SearchIndexHandlers;

use AlgoliaSearch\Client;
use PhpSpec\ObjectBehavior;
use Spatie\SearchIndex\Query\Algolia\SearchQuery;
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

    public function let(Client $algoliaClient, Searchable $searchableObject, \AlgoliaSearch\Index $index)
    {
        $searchableObject->getSearchableBody()->willReturn($this->searchableBody);
        $searchableObject->getSearchableType()->willReturn($this->searchableType);
        $searchableObject->getSearchableId()->willReturn($this->searchableId);

        $this->beConstructedWith($algoliaClient);

        $this->index = $index;
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(Algolia::class);
    }

    public function it_adds_a_searchable_object_to_the_search_index(\AlgoliaSearch\Index $index, Searchable $searchableObject)
    {
        $index->saveObject(
            array_merge(
                $this->searchableBody,
                ['objectID' => $this->searchableType.'-'.$this->searchableId]
            )
        )->shouldBeCalled();

        $this->upsertToIndex($searchableObject);
    }

    public function it_removes_a_searchable_object_from_the_index(\AlgoliaSearch\Index $index, Searchable $searchableObject)
    {
        $index->deleteObject($this->searchableType.'-'.$this->searchableId)->shouldBeCalled();

        $this->removeFromIndex($searchableObject);
    }

    public function it_removes_an_object_from_the_index_by_type_and_id(\AlgoliaSearch\Index $index)
    {
        $index->deleteObject($this->searchableType.'-'.$this->searchableId)->shouldBeCalled();

        $this->removeFromIndexByTypeAndId($this->searchableType, $this->searchableId);
    }

    public function it_can_clear_the_index(\AlgoliaSearch\Index $index)
    {
        $index->clearIndex()->shouldBeCalled();

        $this->clearIndex();
    }

    public function it_can_get_search_results(\AlgoliaSearch\Index $index)
    {
        $query = 'this is a testquery';

        $index->search($query, [])->shouldBeCalled();

        $this->getResults($query);
    }

    public function it_can_get_search_results_using_an_array(\AlgoliaSearch\Index $index)
    {
        $query = [
            'query' => 'raw query',
            'param1' => 'yes',
            'param2' => 'no',
        ];

        $index->search('raw query', ['param1' => 'yes', 'param2' => 'no'])->shouldBeCalled();

        $this->getResults($query);
    }

    public function it_can_get_search_results_using_a_search_object(\AlgoliaSearch\Index $index)
    {
        $searchQuery = new SearchQuery();

        $searchQuery->searchFor('my query');

        $index->search('my query', ["numericFilters" => "", "facetFilters" => "", "hitsPerPage" => 10000])->shouldBeCalled();

        $this->getResults($searchQuery);
    }

    public function it_can_get_search_results_using_an_array_without_a_query_key(\AlgoliaSearch\Index $index)
    {
        $query = [
            'param1' => 'yes',
            'param2' => 'no',
        ];

        $index->search('', ['param1' => 'yes', 'param2' => 'no'])->shouldBeCalled();

        $this->getResults($query);
    }
}
