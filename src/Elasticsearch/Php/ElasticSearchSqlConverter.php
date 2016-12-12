<?php

namespace Elasticsearch\php;

class ElasticSearchSqlConverter  
{   
    /**
     * Constructor.
     */
    public function __construct() {

    }

    /**
     * Old syntax of class constructor. Deprecated in PHP7.
     *
     * @deprecated since PHP7
     */
    public function ElasticSearchSqlConverter () {
        echo('Use of class name as constructor is deprecated');
        self::__construct();
    }

    /**
     * Create Function to format data
     */
    public function create($data, $isFlat, $showScore, $showType) {

        if ($this->isJson($data)){
            $data = json_decode($data,true);
        }
        if($this->isSearch($data)){
            return $this->isAggregation($data) ? new AggregationQueryResultHandler($data) : new DefaultQueryResultHandler($data, $isFlat, $showScore, $showType)
        }

        if($this->isDelete($data)){
            return new DeleteQueryResultHandler($data);
        }

        return new ShowQueryResultHandler($data);
    } 

    /**
     * Function to check if data is of search type.
     */
    public function isSearch($data) {
        return array_key_exists('hits', $data);
    }

     /**
     * Function to check if data is of Aggregation type.
     */
    public function isAggregation($data) {
        return array_key_exists('aggregations', $data);
    }

    /**
     * Function to check if data is of Delete type.
     */
    public function isDelete($data) {
        return array_key_exists('_indices', $data);
    }

    public function isJson($data) {
        json_decode($data);
        return (json_last_error() == JSON_ERROR_NONE);
    }
}
