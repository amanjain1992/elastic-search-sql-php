<?php

namespace Elasticsearch\php\Handler;

use Elasticsearch\php\Handler\BaseClass;

/**
* 
*/
class ShowQueryResultHandler extends BaseClass
{
    
    public function __construct($data)
    {
        $this->data = $data;
        $this->init();
    }

    public function init()
    {
        $mappingParser = new MappingParser($this->data);
        $indices = $mappingParser->getIndices();
        $body = [];
        if(count($indices) > 1){
            $this->head = ["index","types"];
            foreach($indices as $indexOfIndex){
                $indexToTypes = [];
                $index = $indices[$indexOfIndex];
                $indexToTypes["index"] = $index;
                $indexToTypes["types"] = $mappingParser->getTypes($index);
                $body[] = $indexToTypes;
            }
        }
        else {
            $index  = $indices[0];
            $types = $mappingParser.getTypes($index);
            if(count(types) > 1) {
                $this->head = ["type","fields"];
                foreach($types as $typeIndex){
                    $typeToFields = [];
                    $type = $types[$typeIndex];
                    $typeToFields["type"] = $type;
                    $typeToFields["fields"] = $mappingParser->getFieldsForType($index,$type);
                    $body[]  =$typeToFields;
                }
            }
            else {
                $this->head = ["field","type"];
                $anyFieldContainsMore = false;
                $fieldsWithMapping = $mappingParser->getFieldsForTypeWithMapping($index,$types[0]);
                foreach($fieldsWithMapping as $field){
                    $fieldRow = [];
                    $fieldMapping = $fieldsWithMapping[$field];
                    $fieldRow["field"] = $field;
                    $fieldRow["type"] = $fieldMapping["type"];
                    unset($fieldMapping["type"]);
                    if(!empty($fieldMapping)){
                        $anyFieldContainsMore = true;
                        $fieldRow["more"] = $fieldMapping;
                    }
                    $body[] = $fieldRow;
                }
                if($anyFieldContainsMore){
                     $this->head[] = "more";
                }

            }
        }
    }

    public function getHead()
    {
        return $this->head;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function getTotal()
    {
        return count($this->body);
    }

    public function getCurrentHitsSize(){
        return count($this->body);
    }
}