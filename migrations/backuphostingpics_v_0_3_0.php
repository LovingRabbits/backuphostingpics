<?php
/**
*
* @package phpBB Extension - FoFa - Posting News
* @copyright (c) 2015 FoFa (http://forums.phpbb-fr.com/fofa-u89565/)
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
**/

namespace fofa\backuphostingpics\migrations;

class backuphostingpics_v_0_3_0 extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return isset($this->config['backuphostingpics_version']) && version_compare($this->config['backuphostingpics_version'], '0.3.0', '>=');
	}

	static public function depends_on()
	{
		return array('\phpbb\db\migration\data\v310\dev');
	}

	public function update_data()
	{
		return array(
			// Current version
			array('config.add', array('backuphostingpics_version', '0.3.0')),
		);
	}

	public function revert_data()
	{
		// Remove
		return array(
			array('config.remove', array('backuphostingpics_version')),
		);
	}
}