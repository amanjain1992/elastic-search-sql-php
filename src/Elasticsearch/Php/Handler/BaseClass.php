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
    
    public function __construct()
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
    public function JqExtend($Defaults, $Opt ) {
        if (!empty($Opt)){
          //For each Option, their Key => Value
             foreach ( $Opt as $k => $v ){
                 if ( !array_key_exists( $k, $Defaults ))
                     unset( $Opt[$k] );
             }

             $return = $Opt + $Defaults;
             //Return the stripped Array
        } else {
           $return = $Defaults;
        }
           
        return $return;
   }
}