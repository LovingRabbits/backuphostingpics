<?php
/**
*
* @package phpBB Extension - FoFa - Posting News
* @copyright (c) 2015 FoFa (http://forums.phpbb-fr.com/fofa-u89565/)
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
**/

namespace fofa\backuphostingpics\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
* Event listener
*/
class listener implements EventSubscriberInterface
{
/**
* Assign functions defined in this class to event listeners in the core
*
* @return array
* @static
* @access public
*/
    static public function getSubscribedEvents()
    {
        return array(
            'core.user_setup'                        => 'load_language_on_setup',
        );
    }
 
    public function load_language_on_setup($event)
    {
        $lang_set_ext = $event['lang_set_ext'];
        $lang_set_ext[] = array(
            'ext_name' => 'fofa/backuphostingpics',
            'lang_set' => 'common',
        );
        $event['lang_set_ext'] = $lang_set_ext;
    }
	
	public function add_page_header_link($event)
	{
		$this->template->assign_vars(array(
			'U_POSTINGNEWS'			=> $this->helper->route('fofa_backuphostingpics'),
		));
	}
	
}