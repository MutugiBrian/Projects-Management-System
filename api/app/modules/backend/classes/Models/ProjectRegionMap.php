<?php

namespace Chemicals\Backend\Models;

use \Phalcon\Mvc\Model\Resultset\Simple as Resultset;
use \Phalcon\Mvc\Model\Query;
use \Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Behavior\SoftDelete;

class ProjectRegionMap extends Model
{
   
    static public  $di ;
	static public  $db ;

	const DELETED     = 1;
    const NOT_DELETED = 0;
	
	public function getSource()
    {
        return 'project_region_map'; 
    }

	public function initialize()
    {
        /**$this->addBehavior(
            new SoftDelete(
                [
                    'field' => 'delete_flag',
                    'value' => Project::DELETED,
                ]
            )
        );**/
    }

	public static function getDb()
	{
		$di = \Phalcon\DI::getDefault();

		if ( is_null(self::$db) )
		{
			self::$db = $di->get('db');
		}
		return self::$db ;
	}

	public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
