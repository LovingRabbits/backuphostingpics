<?php
/**
*
* @package phpBB Extension - FoFa - Backup Hosting Images
* @copyright (c) 2015 FoFa (http://forums.phpbb-fr.com/fofa-u89565/)
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
**/

namespace fofa\backuphostingpics;

/**
* @ignore
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

/**
* Main class for « Backup Hosting Images » Extension.
*/
class ext extends \phpbb\extension\base
{
	/**
	* Enable extension if pre-requis ok
	*
	* @return bool
	* @aceess public
	*/
	public function is_enableable()
	{
		// Configuration parameters
		$config = $this->container->get('config');

		// phpBB minima version required !
		if (!version_compare($config['version'], '3.1.6', '>='))
		{
			return false;
		}
		
		return true;
	}
}