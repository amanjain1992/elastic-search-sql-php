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
class DeleteQueryResultHandler extends BaseClass
{
    
    public function __construct()
    {
        $this->head = ["index_deleted_from","shards_successful","shards_failed"];
        $body = [];
        $deleteData = $data["_indices"];
        foreach($deleteData as $index){
            $deleteStat = [];
            $deleteStat["index_deleted_from"] = $index;
            $shardsData = $deleteData[$index]["_shards"];
            $deleteStat["shards_successful"] = $shardsData["successful"];
            $deleteStat["shards_failed"] = $shardsData["failed"];
            $body[] = $deleteStat;
        }
        $this->body = $body;
    }

    public function getHead(){
         return $this->head;
    }

    public function getBody(){
         return $this->body;
    }

    public function getTotal(){
         return 1;
    }

    public function getCurrentHitsSize(){
        return 1;
    }
}