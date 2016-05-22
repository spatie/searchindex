<?php

namespace Spatie\SearchIndex\Test\Query\Algolia;

use DateTime;
use Spatie\SearchIndex\Query\Algolia\SearchQuery;

class SearchQueryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SearchQuery
     */
    protected $query;

    /**
     * @var array
     */
    protected $defaultResult;

    public function setUp()
    {
        $this->query = new SearchQuery();
        $this->defaultResult = [
            'numericFilters' => '',
            'facetFilters' => '',
            'hitsPerPage' => 10000,
        ];
    }

    /**
     * @test
     */
    public function it_can_handle_an_empty_query()
    {
        $this->assertEquals($this->defaultResult, $this->query->toArray());
    }

    /**
     * @test
     */
    public function it_can_handle_a_query_string()
    {
        $this->query
            ->searchFor('hello');

        $this->assertEquals($this->expectedResult(['query' => 'hello']), $this->query->toArray());
    }

    /**
     * @test
     */
    public function it_can_handle_a_location_aware_query()
    {
        $lat = 1.234567890;
        $lng = 7.891012345;

        $this->query->aroundLocation($lat, $lng);

        $this->assertEquals($this->expectedResult(
            [
                'aroundLatLng' => $lat.','.$lng,
                'aroundRadius' => 30000,
            ]
        ), $this->query->toArray());
    }

    /**
     * @test
     */
    public function it_can_handle_a_location_aware_query_around_a_radius()
    {
        $lat = 1.234567890;
        $lng = 7.891012345;
        $radius = 12345;

        $this->query->aroundLocation($lat, $lng, $radius);

        $this->assertEquals($this->expectedResult(
            [
                'aroundLatLng' => $lat.','.$lng,
                'aroundRadius' => $radius,
            ]
        ), $this->query->toArray());
    }

    /**
     * @test
     */
    public function it_can_handle_date_restrictions()
    {
        $dateFieldName = 'myDate';
        $operation = '>';
        $date = new DateTime();

        $this->query->withDateRestriction($dateFieldName, $operation, $date);

        $this->assertEquals($this->expectedResult(
            [
                'numericFilters' => ",{$dateFieldName}{$operation}{$date->getTimestamp()}",
            ]
        ), $this->query->toArray());
    }

    /**
     * @test
     */
    public function it_can_handle_multiple_date_restrictions()
    {
        $dateFieldName = 'myDate';
        $operation = '>';
        $date = new DateTime();

        $otherDateFieldName = 'otherDate';
        $otherOperation = '<';
        $otherDate = new DateTime();

        $this->query->withDateRestriction($dateFieldName, $operation, $date);
        $this->query->withDateRestriction($otherDateFieldName, $otherOperation, $otherDate);

        $this->assertEquals($this->expectedResult(
            [
                'numericFilters' => ",{$dateFieldName}{$operation}{$date->getTimestamp()},{$otherDateFieldName}{$otherOperation}{$otherDate->getTimestamp()}",
            ]
        ), $this->query->toArray());
    }

    /**
     * @test
     */
    public function it_can_handle_numeric_filters()
    {
        $name = 'myFilter';
        $myValues = [1, 2, 3];
        $logicalOperator = SearchQuery::LOGICAL_OPERATOR_OR;

        $this->query->withNumericFilter($name, $myValues, $logicalOperator);

        $this->assertEquals($this->expectedResult(
            [
                'numericFilters' => ",({$name}={$myValues[0]},{$name}={$myValues[1]},{$name}={$myValues[2]})",
            ]
        ), $this->query->toArray());
    }

    /**
     * @test
     */
    public function it_can_handle_numeric_filters_with_an_and_relation()
    {
        $name = 'myFilter';
        $myValues = [1, 2, 3];
        $logicalOperator = SearchQuery::LOGICAL_OPERATOR_AND;

        $this->query->withNumericFilter($name, $myValues, $logicalOperator);

        $this->assertEquals($this->expectedResult(
            [
                'numericFilters' => ",{$name}={$myValues[0]},{$name}={$myValues[1]},{$name}={$myValues[2]}",
            ]
        ), $this->query->toArray());
    }

    /**
     * @test
     */
    public function it_can_handle_a_search_facet()
    {
        $names = ['facet1', 'facet2'];
        $values = ['value1', 'value2'];

        $this->query->withFacet($names[0], $values[0]);
        $this->query->withFacet($names[1], $values[1]);

        $this->assertEquals($this->expectedResult(
            [
                'facetFilters' => ",{$names[0]}:{$values[0]},{$names[1]}:{$values[1]}",
            ]
        ), $this->query->toArray());
    }

    /**
     * @test
     */
    public function it_allows_hits_per_page_to_be_set()
    {
        $hitsPerPage = 12345;

        $this->query->setHitsPerPage($hitsPerPage);

        $this->assertEquals($this->expectedResult(['hitsPerPage' => $hitsPerPage]), $this->query->toArray());
    }

    /**
     * @test
     */
    public function it_allows_method_chaining_for_multiple_filters()
    {
        $facetFilter = ['name', 'value'];

        $numericFilterName = 'myFilter';
        $numericFilterValues = [1, 2, 3];
        $logicalOperator = SearchQuery::LOGICAL_OPERATOR_OR;

        $dateFieldName = 'myDate';
        $operation = '>';
        $date = new DateTime();

        $this->query->withFacet('name', 'value')
                    ->withDateRestriction($dateFieldName, $operation, $date)
                    ->withNumericFilter($numericFilterName, $numericFilterValues, $logicalOperator);

        $this->assertEquals($this->expectedResult(
            [
                'facetFilters' => ",{$facetFilter[0]}:{$facetFilter[1]}",
                'numericFilters' => ",{$dateFieldName}{$operation}{$date->getTimestamp()},({$numericFilterName}={$numericFilterValues[0]},{$numericFilterName}={$numericFilterValues[1]},{$numericFilterName}={$numericFilterValues[2]})",
            ]
        ), $this->query->toArray());
    }

    protected function expectedResult(array $expectedResult)
    {
        return array_merge($this->defaultResult, $expectedResult);
    }
}
