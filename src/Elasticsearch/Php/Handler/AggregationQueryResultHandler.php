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

namespace Elasticsearch\Php\Handler;

use Elasticsearch\Php\Handler\BaseClass;

/**
 * Elastic Search Data Modification 
 * with the help of php
 * @package Elasticsearch\Php
 * @author Aman Jain (aman.j@solutionsinfini.com)
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */
class AggregationQueryResultHandler extends BaseClass
{
    
    public function __construct($data)
    {
        $this->data = $data;
        $this->data['aggregations'] = $this->removeNestedAndFilters($this->data['aggregations']);
        $this->flattenBuckets = $this->getRows('', $this->data['aggregations'], []);
    }

    public function removeNestedAndFilters ($aggs) {
        foreach($aggs as $field => $value)
        {
            if ($this->endsWith($field,"@NESTED") || $this->endsWith($field,"@FILTER") || $this->endsWith($field,"@NESTED_REVERSED") || $this->endsWith($field,"@CHILDREN")){
                unset($aggs[$field]["doc_count"]); 
                unset($aggs[$field]["key"]);
                $leftField = array_keys($aggs[$field])[0];
                $aggs[$leftField] = $aggs[$field][$leftField];
                unset($aggs[$field]);
                $this->removeNestedAndFilters($aggs);
            }
            if(is_array($aggs[$field])){
                $this->removeNestedAndFilters($aggs[$field]);
            }
        }
        return $aggs;
    }

    public function endsWith($haystack, $needle)
    {
        $length = strlen($needle);
        if ($length == 0) {
            return true;
        }

        return (substr($haystack, -$length) === $needle);
    }

    public function getRows($bucketName, $bucket, $additionalColumns) {
        $rows = [];

        $subBuckets = $this->getSubBuckets($bucket);
        if(count($subBuckets) > 0) {
            for($i = 0; $i < count($subBuckets); $i++) {
                $subBucketName = $subBuckets[$i]["bucketName"];
                $subBucket = $subBuckets[$i]["bucket"];

                $newAdditionalColumns = [];
                // bucket without parents.
                if(!empty($bucketName)) {
                    $newColumn = [];
                    $newColumn[$bucketName] = $bucket['key'];
                    $newAdditionalColumns = $this->JqExtend($newColumn, $additionalColumns);
                }

                $newRows = $this->getRows($subBucketName, $subBucket, $newAdditionalColumns);
                $rows =  array_merge($rows, $newRows);
            }
        } else {
            $obj = $additionalColumns;
            if(!empty($bucketName)) {
                if(array_key_exists("key_as_string",$bucket)){
                    $obj[$bucketName] = $bucket["key_as_string"];
                }
                else {
                    $obj[$bucketName] = $bucket['key'];
                }
            }
            

            foreach($bucket as $field => $value) {

                $bucketValue = $bucket[$field];
                if(!empty($bucketValue['buckets'])){
                    $newRows = $this->getRows($subBucketName, $bucketValue, $newAdditionalColumns);
                    $rows = array_merge($rows, $newRows);
                    continue;
                }
                if(!empty($bucketValue['value'])){
                    if(array_key_exists("value_as_string",$bucket[$field])) {
                        $obj[$field] = $bucketValue["value_as_string"];
                    }
                    else {
                        $obj[$field] = $bucketValue['value'];
                    }
                }
                else {
                    if(is_array($bucketValue)){   
                        $obj = $this->fillFieldsForSpecificAggregation($obj,$bucketValue,$field);
                    }
                }
            }
            $rows[] = $obj;
        }
        return $rows;
    }

    public function fillFieldsForSpecificAggregation($obj, $value, $field)
    {   

        foreach($value as $key => $v){
            if($key == "values"){
                $this->fillFieldsForSpecificAggregation($obj, $value[$key], $field);
            }
            else {
                $obj[$field.".".$key] = $value[$key];
            }
        }
        return $obj;
    }

    public function getSubBuckets($bucket) {
        $subBuckets = [];
        foreach($bucket as $field => $value) {
            if (!empty($value['buckets'])) {
                $buckets = $value['buckets'];
                if(!empty($buckets)) {
                    for($i = 0; $i < count($buckets); $i++) {
                        $subBuckets[] = ["bucketName" =>  $field, "bucket" => $buckets[$i]];
                    }
                }
                else {
                    $innerAgg = $value;
                    foreach ($innerAgg as $innerField => $v){
                        if(is_array($innerAgg[$innerField])){
                            $innerBuckets = $this->getSubBuckets($innerAgg[$innerField]);
                            $subBuckets = array_merge($subBuckets,$innerBuckets);
                        }    
                    }
                }
            }
        }

        return $subBuckets;
    }

    public function getHead() {
        $head = [];
        for($i = 0; $i < count($this->flattenBuckets); $i++) {
            $keys = array_keys($this->flattenBuckets[$i]);
            for($j = 0; $j < count($keys); $j++) {
                if(!in_array($keys[$j], $head)) {
                    $head[] = $keys[$j];
                }
            }
        }
        return $head;
    }

    public function getBody() {
        return $this->flattenBuckets;
    }

    public function getTotal() {
        return '';
    }

    public function getCurrentHitsSize() {
        return count($this->flattenBuckets);
    }
}