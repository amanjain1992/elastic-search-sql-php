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
class DefaultQueryResultHandler extends BaseClass
{
    
    public function __construct($data, $isFlat, $showScore, $showType)
    {
        $this->data = $data;
        $this->isFlat = $isFlat;
        $this->showScore = $showScore;
        $this->showType = $showType;
        $this->head = $this->createScheme();
        $this->scrollId = $data["_scroll_id"];
        $this->isScroll = !empty($this->scrollId);
    }

    public function createScheme()
    {
        $hits = $this->data['hits']['hits'];
        if (count($hits) > 0){
            $scheme = [];

            for($i=0; $i<count($hits); $i++) {
                $hit = $hits[$i];
                $header = $this->JqExtend($hit['_source'],$hit['fields']);
                if($this->isFlat){
                    $this->findKeysRecursive($scheme,$header,"");
                }

                else {
                    foreach($header as $key) {

                        if(strrpos($scheme, $key)) {
                            $scheme[] = $key;
                        }
                    }       
                }
                
            }
        }

        if($this->showType){
            $scheme[] = "_type";
        }

        if($this->showScore){
            $scheme[] = "_score";
        }
        return $scheme;
    }

    public function findKeysRecursive ($scheme, $keys, $prefix) {
        foreach($keys as $key){
            if(is_array($keys[$key])) {
                $this->findKeysRecursive( $scheme, $keys[$key], $prefix+$key+".");
            } else {
                if(strrpos($scheme, $prefix+$key) == -1){
                    $scheme[] = $prefix+$key;
                }
            }
        }
    }

    public function isScroll() {
         return $this->isScroll;
    }

    public function getScrollId() {
         return $this->scrollId;
    }

    public function getHead() {
         return $this->head;
    }

    public function getBody() {
        $hits = $this->data['hits']['hits'];
        $body = [];
        for($i = 0; $i < count($hits); $i++) {
            $row = $hits[$i]['_source'];
            if(array_key_exists("fields" , $hits[$i])){
                $this->addFieldsToRow($row,$hits[$i]);
            }
            if($this->isFlat){
                $row = $this->flatRow($this->head, $row);
            }
            if($this->showType){
                $row["_type"] = $hits[$i]['_type'];
            }
            if($this->showScore){
                $row["_score"] = $hits[$i].['_score'];
            }
            $body[] = $row;
        }
        return $body;
    }
    
    public function addFieldsToRow ($row, $hit) {
        foreach($hit['fields'] as $field){
            $fieldValue = $hit['fields'][$field];
            if( is_array($fieldValue)){
                if(count($fieldValue) > 1)
                    $row[$field] = $fieldValue;
                else $row[$field] = $fieldValue[0];
            }
            else {
                $row[$field] = $fieldValue;
            }
        }
    }

    public function flatRow ($keys,$row) {
        $flattenRow = [];
        for( $i = 0 ; $i< count($keys) ; $i++ ){
            $key = $keys[$i];
            $splittedKey = explode('.',$key);
            $found = true;
            $currentObj = $row;
            for( $j = 0 ; $j < count($splittedKey) ; $j++){
                if(!empty($currentObj[$splittedKey[$j]])){
                    $found = false;
                    break;
                }
                else {
                    $currentObj = $currentObj[$splittedKey[j]];
                }
            }
            if($found){
                $flattenRow[$key] = $currentObj;
            }
        }
        return $flattenRow;
    }

    public function getTotal() {
        return $this->data['hits']['total'];
    }

    public function getCurrentHitsSize() {
        return $this->data['hits']['length'];
    }
}