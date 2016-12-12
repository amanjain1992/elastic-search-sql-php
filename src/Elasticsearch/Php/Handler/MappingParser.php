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
class MappingParser extends BaseClass
{
    
    public function __construct($data)
    {
        $parsedMapping = $this->parseMapping($data);
        $this->mapping = $parsedMapping;
    }

    public function parseMapping($mapping){
        $indexToTypeToFields = [];
        foreach($mapping as $index => $v){
            $types = $mapping[$index]["mappings"];
            $typeToFields = [];
            foreach($types as $type){
                $fields = $types[$type]["properties"];
                $fieldsFlatten = [];
                $this->getFieldsRecursive($fields,$fieldsFlatten,"");
                $typeToFields[$type] = $fieldsFlatten;
            }

            $indexToTypeToFields[$index] = $typeToFields;
        }
        return $indexToTypeToFields;    
    }

    public function getFieldsRecursive($fields, $fieldsFlatten, $prefix){
        foreach($fields as $field){
            $fieldMapping = $fields[$field];
            if(array_key_exists("type", $fieldMapping)){
                $fieldsFlatten[$prefix+$field] = $fieldMapping;
            }
            if(!array_key_exists("type", $fieldMapping) || $fieldMapping['type'] == "nested") {
                $this->getFieldsRecursive($fieldMapping["properties"],$fieldsFlatten,$prefix+$field+".");
            }
        }
    }

    public function getIndices() {
        return array_keys($this->mapping);
    }
    public function getTypes($index) {
        return array_keys($this->mapping[$index]);
    }
    public function getFieldsForType($index, $type) {
        return array_keys($this->mapping[$index][$type]);
    }
    public function getFieldsForTypeWithMapping($index, $type) {
        return $this->mapping[$index][$type];
    }
}