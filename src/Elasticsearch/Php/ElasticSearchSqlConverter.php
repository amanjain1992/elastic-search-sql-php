<?php
/**
 * This file is part of the Elasticsearch application.
 *
 * @license http://opensource.org/licenses/MIT
 *
 * @link https://github.com/amanjain1992/elastic-search-sql-php
 *
 * @version 0.0.1
 */
namespace Elasticsearch\Php;
use Elasticsearch\Php\Handler;

/**
 * Elastic Search Data Modification 
 * with the help of php
 * @package Elasticsearch\Php
 * @author Aman Jain (aman.j@solutionsinfini.com)
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */
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
     * Returns the right Result Handler depend
     *   on the results
     * @return handler object 
     */
    public function create($data, $isFlat, $showScore, $showType) {

        if ($this->isJson($data)){
            $data = json_decode($data,true);
        }
        if($this->isSearch($data)){
            return $this->isAggregation($data) ? new \Elasticsearch\Php\Handler\AggregationQueryResultHandler($data) : new \Elasticsearch\Php\Handler\DefaultQueryResultHandler($data, $isFlat, $showScore, $showType);
        }

        if($this->isDelete($data)){
            return new \Elasticsearch\Php\Handler\DeleteQueryResultHandler($data);
        }

        return new \Elasticsearch\Php\Handler\ShowQueryResultHandler($data);
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

    /**
     * Function to check if data is JSON format.
     */
    public function isJson($data) {
        json_decode($data);
        return (json_last_error() == JSON_ERROR_NONE);
    }
}
