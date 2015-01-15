<?php

namespace spec\Spatie\SearchIndex\SearchIndexHandlers;

use Elasticsearch\Client;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Spatie\SearchIndex\Searchable;

class ElasticsearchSpec extends ObjectBehavior
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

    function let(Client $elasticsearch, Searchable $searchableObject)
    {
        $searchableObject->getSearchableBody()->willReturn($this->searchableBody);
        $searchableObject->getSearchableType()->willReturn($this->searchableType);
        $searchableObject->getSearchableId()->willReturn($this->searchableId);

        $this->beConstructedWith($elasticsearch);

        $this->setIndexName($this->indexName);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Spatie\SearchIndex\SearchIndexHandlers\Elasticsearch');
    }

    function it_adds_a_searchable_object_to_the_search_index(Client $elasticsearch, Searchable $searchableObject)
    {
        $elasticsearch->index(
            [
                'index' => $this->indexName,
                'type' => $this->searchableType,
                'id' => $this->searchableId,
                'body' => $this->searchableBody
            ]
        )->shouldBeCalled();

        $this->upsertToIndex($searchableObject);
    }

    function it_removes_a_searchable_object_from_the_index(Client $elasticsearch, Searchable $searchableObject)
    {
        $elasticsearch->delete(
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
        function it_can_clear_the_index(Client $elasticsearch)
        {

            $elasticsearch->indices()->delete(['index' => $this->indexName]);

            $this->clearIndex();
        }
    */


    function it_can_get_search_results(Client $elasticsearch)
    {
        $query = 'this is a testquery';

        $elasticsearch->search($query)->shouldBeCalled();

        $this->getResults($query);
    }





}
