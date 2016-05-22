<?php

namespace Spatie\SearchIndex\Query\Algolia;

class SearchQuery
{
    const LOGICAL_OPERATOR_AND = 'and';
    const LOGICAL_OPERATOR_OR = 'or';

    protected $query = '';

    /**
     * @var bool
     */
    protected $useLocationAwareSearch = false;

    /**
     * @var int
     */
    protected $lat;

    /**
     * @var int
     */
    protected $lng;

    /**
     * @var int
     */
    protected $aroundRadius;

    /**
     * @var array
     */
    protected $dateRestrictions = [];

    /**
     * @var array
     */
    protected $numericFilters = [];

    /**
     * @var array
     */
    protected $facets = [];

    /**
     * @var int
     */
    protected $hitsPerPage = 10000;

    /**
     * Set the query to search for.
     *
     * @param mixed $query
     *
     * @return JobQuery
     */
    public function searchFor($query)
    {
        $this->query = $query;

        return $this;
    }

    /**
     * Create a location aware search.
     *
     * @param int $lat
     * @param int $lng
     * @param int $aroundRadius
     *
     * @return $this
     */
    public function aroundLocation($lat, $lng, $aroundRadius = 30000)
    {
        $this->lat = $lat;
        $this->lng = $lng;
        $this->aroundRadius = $aroundRadius;
        $this->useLocationAwareSearch = true;

        return $this;
    }

    /**
     * Set a date restriction.
     *
     * @param string $dateFieldName
     * @param string $operation
     * @param \DateTime $date
     * @return $this
     */
    public function withDateRestriction($dateFieldName, $operation, \DateTime $date)
    {
        $this->dateRestrictions[] = compact('dateFieldName', 'operation', 'date');

        return $this;
    }

    /**
     * Set a facet.
     *
     * @param string $name
     * @param string $value
     * @return $this
     */
    public function withFacet($name, $value)
    {
        $this->facets[] = $name.':'.$value;

        return $this;
    }

    /**
     * @param int $hitsPerPage
     *
     * @return SearchQuery
     */
    public function setHitsPerPage($hitsPerPage)
    {
        $this->hitsPerPage = $hitsPerPage;

        return $this;
    }

    /**
     * Set a numeric filter.
     *
     * @param string $name
     * @param string|array $values
     * @param string $logicalOperator
     * @return $this
     */
    public function withNumericFilter($name, $values, $logicalOperator = 'and')
    {
        if (!is_array($values)) {
            $values = [$values];
        }

        $numericalFilterArray = array_map(function ($value) use ($name) {
            return "{$name}={$value}";
        }, $values);

        $numericalFilter = implode(',', $numericalFilterArray);

        if ($logicalOperator == self::LOGICAL_OPERATOR_OR) {
            $numericalFilter = "({$numericalFilter})";
        }

        $this->numericFilters[] = $numericalFilter;

        return $this;
    }

    /**
     * Get the query as an array.
     *
     * @return array
     */
    public function toArray()
    {
        $query = [
            'numericFilters' => '',
            'facetFilters' => '',
        ];

        if ($this->query != '') {
            $query['query'] = $this->query;
        }

        if ($this->useLocationAwareSearch) {
            $query['aroundLatLng'] = $this->lat.','.$this->lng;
            $query['aroundRadius'] = $this->aroundRadius;
        }

        foreach ($this->dateRestrictions as $dateRestriction) {
            $query['numericFilters'] .= ','.
                $dateRestriction['dateFieldName'].
                $dateRestriction['operation'].
                $dateRestriction['date']->getTimeStamp();
        }

        foreach ($this->numericFilters as $filter) {
            $query['numericFilters'] .= ','.$filter;
        }

        foreach ($this->facets as $facet) {
            $query['facetFilters'] .= ','.$facet;
        }

        $query['hitsPerPage'] = $this->hitsPerPage;

        return $query;
    }
}
