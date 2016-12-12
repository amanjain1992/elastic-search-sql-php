<?php

namespace Elasticsearch\Php\Handler;

use Elasticsearch\Php\Handler\BaseClass;

/**
* 
*/
class MappingParser extends BaseClass
{
    
    public function __construct($data)
    {
        $parsedMapping = $this->parseMapping($data);
        $this->mapping = $parsedMapping;
    }

    public function parseMapping($data){
        $indexToTypeToFields = [];
        foreach($mapping as $index){
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