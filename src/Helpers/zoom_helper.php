<?php

if( !function_exists( 'zoom_instance' ) )
{
	/**
	 * load twig
	 *
	 * @return class
	 */
	function zoom_instance()
	{
		return \CodeIgniter\Config\Services::zoom();
	}
}