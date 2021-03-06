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

namespace Elasticsearch\php\Handler;

use Elasticsearch\Php\Handler\BaseClass;

/**
 *  ShowQueryResultHandler object
 *  for showing mapping in some levels (cluster, index and types)
 * @package Elasticsearch\Php
 * @author Aman Jain (aman.j@solutionsinfini.com)
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
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
            foreach($indices as $indexOfIndex => $v){
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
                foreach($types as $typeIndex => $ti){
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
                foreach($fieldsWithMapping as $field => $fwm){
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