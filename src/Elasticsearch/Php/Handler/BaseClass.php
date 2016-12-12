<?php

namespace Elasticsearch\php\Handler;

/**
 * Elastic Search converting json object 
 * to get proper array
 *
 * @package elastic_search
 * @author Aman Jain (aman.j@solutionsinfini.com)
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */
class BaseClass
{
    
    public function __construct(argument)
    {
    }
    /**
    *  Here we '$.extend()' the provided array aganist;
    *  the user's provided options,
    *
    *  This will effectively 'array_merge' the user
    *  & the system settings.
    *
    *  Note, it uses a union merge, not the
    *  php's Array Merge to merge them.
   **/
    private function JqExtend($Defaults, $Opt ) {
           //For each Option, their Key => Value
           foreach ( $Opt as $k => $v ){
               if ( !array_key_exists( $k, $Defaults ))
                   unset( $Opt[$k] );
           }

           $return = $Opt + $Defaults;
           //Return the stripped Array
           return $return;
   }
}