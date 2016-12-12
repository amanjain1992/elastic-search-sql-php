<?php

namespace Elasticsearch\php\Handler;

use Elasticsearch\php\Handler\BaseClass;

/**
* 
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