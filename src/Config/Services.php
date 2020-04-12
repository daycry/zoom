<?php namespace Daycry\Zoom\Config;

use CodeIgniter\Config\BaseService;
use Daycry\Zoom\Zoom;

class Services extends BaseService
{
    public static function zoom( bool $getShared = true )
    {
		if ( $getShared )
		{
			return static::getSharedInstance( 'zoom' );
		}

		$config = config( 'Zoom' );

		return new Zoom( $config );
	}
}