<?php
/**
*
* @package phpBB Extension - FoFa - Backup Images from Hosting Pics
* @copyright (c) 2015 FoFa (http://forums.phpbb-fr.com/fofa-u89565/)
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
**/

namespace fofa\backuphostingpics\controller;

use Symfony\Component\HttpFoundation\Response;

class main_controller
{
	protected $config;
	protected $db;
	protected $auth;
	protected $template;
	protected $user;
	protected $helper;
	protected $phpbb_root_path;
	protected $php_ext;

	
	/*
	*	Constructor 
	*/
	public function __construct(\phpbb\config\config $config, \phpbb\request\request_interface $request, \phpbb\pagination $pagination, \phpbb\db\driver\driver_interface $db, \phpbb\auth\auth $auth, \phpbb\template\template $template, \phpbb\user $user, \phpbb\controller\helper $helper, $phpbb_root_path, $php_ext, $table_prefix)
	{
		$this->config = $config;
		$this->request = $request;
		$this->pagination = $pagination;
		$this->db = $db;
		$this->auth = $auth;
		$this->template = $template;
		$this->user = $user;
		$this->helper = $helper;
		$this->phpbb_root_path = $phpbb_root_path;
		$this->php_ext = $php_ext;
		$this->table_prefix = $table_prefix;
	}

	/*
	*	public function
	*/
	public function main()
	{
		// Some variables
		$mode	= $this->request->variable('mode', '');
		$page	= $this->request->variable('p', 0);

		// Some important informations
		$your_board_url = 'http://www.yourdomain.tld/'; // What is your phpBB board URL?
		$table_prefix = 'phpbb_'; // Your tables prefix
		$dest_img = 'images/backuphostingpics/'; // Where do you want your images to be saved to?
		$limit   = 25; // In case of slow connexion and/or busy server, decrease this value

		/* DO NOT CHANGE THIS TWO NEXT LINES */
		$search_term = 'hostingpics.net'; // We are looking for this term
		define('BACKUPHOSTINGPICS_POSTS_TABLE',			$table_prefix . 'backup_hostingpics_posts');
		define('BACKUPHOSTINGPICS_IMAGES_TABLE',		$table_prefix . 'backup_hostingpics_images');
		
		/* LET START THE JOB */

		switch ($mode)
		{
			case 'count_posts':
				$sql = 'SELECT COUNT(post_id) AS num_post_id, post_text
						FROM ' . POSTS_TABLE . '
						WHERE post_text LIKE "%' . $search_term . '%"';
				$result = $this->db->sql_query($sql);

				// The user count is now available here:
				$num_post_id = (int) $this->db->sql_fetchfield('num_post_id');

				$this->db->sql_freeresult($result);

				$message = $this->user->lang('COUNT_POSTS_MSG', (int) $num_post_id) . '<br /><br />';
				$message .= sprintf($this->user->lang['CREATE_POSTS_LIST'], '<a href="' . append_sid("{$this->phpbb_root_path}backuphostingpics", 'mode=create_posts_list') . '">', '</a>');

				trigger_error($message);	
			break;

			case 'create_posts_list':
				$sql = 'CREATE TABLE IF NOT EXISTS ' . BACKUPHOSTINGPICS_POSTS_TABLE . ' (
					result_id int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
					post_id int(10) UNSIGNED NOT NULL DEFAULT "0",
					topic_id int(10) UNSIGNED NOT NULL DEFAULT "0",
					forum_id mediumint(8) UNSIGNED NOT NULL DEFAULT "0",
					poster_id int(10) UNSIGNED NOT NULL DEFAULT "0",
					result_analysed tinyint(1) UNSIGNED NOT NULL DEFAULT "0",
					PRIMARY KEY (result_id),
					UNIQUE(result_id)
				)';
				$this->db->sql_query($sql);

				$sql2 = 'SELECT post_id, topic_id, forum_id, poster_id, post_text
						FROM ' . POSTS_TABLE . '
						WHERE post_text LIKE "%' . $search_term . '%"
						ORDER BY post_id';
				$result = $this->db->sql_query($sql2);
				
				$posts_request = array();
				while ($row = $this->db->sql_fetchrow($result))
				{
					$result_analysed = 0;
					$sql = 'INSERT INTO ' . BACKUPHOSTINGPICS_POSTS_TABLE . ' ' . $this->db->sql_build_array('INSERT', array(
						'post_id'		=> (int) $row['post_id'],
						'topic_id'		=> (int) $row['topic_id'],
						'forum_id'		=> $row['forum_id'],
						'poster_id'		=> (int) $row['poster_id'],
						'result_analysed'		=> $result_analysed)
					);
					$this->db->sql_query($sql);
					
					$posts_request[] = $sql;
				}
				$this->db->sql_freeresult($result);
				
				$posts_request = implode('<br />', $posts_request);
				
				$total = substr_count($posts_request, 'INSERT');
				 
				$message = $this->user->lang('CREATE_POSTS_LIST_DONE', substr_count($posts_request, 'INSERT')) . '<br /><br />';
				$message .= sprintf($this->user->lang['SELECT_IMAGES_LINKS'], '<a href="' . append_sid("{$this->phpbb_root_path}backuphostingpics", 'mode=select_images_links') . '">', '</a>');

				trigger_error($message);		
			break;

		case 'select_images_links':
			/* Before we start anything, let's create our new images table k */
 		 	$sql = 'CREATE TABLE IF NOT EXISTS ' . BACKUPHOSTINGPICS_IMAGES_TABLE . ' (
				result_id int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
				post_id int(10) UNSIGNED NOT NULL DEFAULT "0",
				topic_id int(10) UNSIGNED NOT NULL DEFAULT "0",
				forum_id mediumint(8) UNSIGNED NOT NULL DEFAULT "0",
				poster_id int(10) UNSIGNED NOT NULL DEFAULT "0",
				url_link varchar(255) NOT NULL DEFAULT "",
				img_name varchar(255) NOT NULL DEFAULT "",
				images_analysed tinyint(1) UNSIGNED NOT NULL DEFAULT "0",
				PRIMARY KEY (result_id),
				UNIQUE(result_id)
			)';

			$this->db->sql_query($sql);

			$sql2 = 'SELECT COUNT(post_id) AS num_result_id
					FROM ' . BACKUPHOSTINGPICS_POSTS_TABLE;
			$result2 = $this->db->sql_query($sql2);
			
			// The posts count is now available here
			$num_result_id = (int) $this->db->sql_fetchfield('num_result_id');

			// How many pages
			$nb_pages = ceil($num_result_id/$limit);			
			
			// On which page are we?
			if (!$page)
			{
				$current_page = 1; // If no page number specified, let's take the first page
			}
			else
			{
				$current_page = $page; // If page number specified, let's take this page

				if($current_page > $nb_pages)
				{
					$current_page = $nb_pages; // In case number page bigger than total pages amount, let's take the last page
				}
			}			
			
			// Let's calculate from which point we take our datas
			$first_entry = ($current_page - 1) * $limit;

			// Let's start the job
			$sql3 = 'SELECT bhpt.post_id, pt.post_id, pt.topic_id, pt.forum_id, pt.poster_id, pt.post_text
					FROM ' . BACKUPHOSTINGPICS_POSTS_TABLE . ' bhpt
					LEFT JOIN ' . POSTS_TABLE . ' pt
					ON bhpt.post_id = pt.post_id
					ORDER BY bhpt.post_id ASC
					LIMIT ' . $first_entry . ', ' . $limit;
			$result3 = $this->db->sql_query($sql3);	

			// Let's start a nice loop
			while ($row2 = $this->db->sql_fetchrow($result3))
			{
				// Some problem with members posting a lot of wrong code...
				$our_post_text = $row2['post_text'];
 				$our_post_text = str_replace('"https://www.hostingpics.net"', '', $our_post_text);
				$our_post_text = str_replace('>https://www.hostingpics.net<', '', $our_post_text);
				$our_post_text = str_replace('[url=https://www.hostingpics.net]', '', $our_post_text); 
 				$our_post_text = str_replace('"http://www.hostingpics.net"', '', $our_post_text);
				$our_post_text = str_replace('>http://www.hostingpics.net<', '', $our_post_text);
				$our_post_text = str_replace('[url=http://www.hostingpics.net]', '', $our_post_text); 
	
			preg_match_all('#(http|https)\:\/\/(.*?)\.hostingpics\.net\/(pics\/|viewer\.php\?id\=)((.*?).jpg|(.*?).jpeg|(.*?).png|(.*?).gif)#i', $our_post_text, $current_posted_img);

			$tart = 0;
			foreach($current_posted_img[0] as $posted_img)
			{
				$tart = $tart+1;
				$image = $posted_img;
				
				// Let's prepare our image name
				$info = new SplFileInfo($image);
				$img_name = $info->getFilename();
				$img_name = str_replace("viewer.php?id=", "", $img_name);
					
				$sql = 'INSERT INTO ' . BACKUPHOSTINGPICS_IMAGES_TABLE . ' ' . $db->sql_build_array('INSERT', array(
					'result_id'		=> (int) $tart,
					'post_id'		=> (int) $row['post_id'],
					'topic_id'		=> (int) $row['topic_id'],
					'forum_id'		=> (int) $row['forum_id'],
					'poster_id'		=> (int) $row['poster_id'],
					'url_link'		=> $posted_img,
					'img_name'		=> $img_name,
					'images_analysed'	=> 0)
				);
				$db->sql_query($sql);
			}

			}
			$this->db->sql_freeresult($result3);

			/* Here we have our pagination - automatically refreshs */
			$page_next = ($page<$nb_pages) ? $page+1 : $page;
			$meta_info = append_sid("{$this->phpbb_root_path}backuphostingpics", 'mode=select_images_links&p=' . $page_next);

			($page<$nb_pages) ? meta_refresh(10, $meta_info) : '';		

			$message = ($page<$nb_pages) ? $this->user->lang['SELECT_IMAGES_LINKS_WORK'] . '<br /><br />' . $this->user->lang('SELECT_IMAGES_LINKS_PROGRESS', $page*$limit, $num_result_id) . '<br /><br />' : $this->user->lang('SELECT_IMAGES_LINKS_END', $num_result_id, $num_result_id) . '<br /><br />';
			
			$message .= ($page<$nb_pages) ? '' : sprintf($this->user->lang['BACKUP_IMAGES_TO_SERVER'], '<a href="' . append_sid("{$this->phpbb_root_path}backuphostingpics", 'mode=backup_images_to_server') . '">', '</a>');
			
			trigger_error($message);
		
		break;
	
		case 'backup_images_to_server':

			$sql_count_distinct = 'SELECT COUNT(result_id) AS count_distinct_rows
					FROM ' . BACKUPHOSTINGPICS_IMAGES_TABLE;
			$result_count_distinct = $this->db->sql_query($sql_count_distinct);
			
			// The posts count is now available here
			$count_distinct_rows = (int) $this->db->sql_fetchfield('count_distinct_rows');

			// How many pages
			$nb_pages = ceil($count_distinct_rows/$limit);			
			
			// On which page are we?
			if (!$page)
			{
				$current_page = 1; // If no page number specified, let's take the first page
			}
			else
			{
				$current_page = $page; // If page number specified, let's take this page

				if($current_page > $nb_pages)
				{
					$current_page = $nb_pages; // In case number page bigger than total pages amount, let's take the last page
				}
			}			
			
			// Let's calculate from which point we take our datas
			$first_entry = ($current_page - 1) * $limit;
		
			// Let's start the job
 			$do_we_hear_you = "SELECT result_id, url_link, img_name, images_analysed
					FROM " . BACKUPHOSTINGPICS_IMAGES_TABLE . "
					ORDER BY result_id
					LIMIT " . $first_entry . ", " . $limit;
			$yes_we_do = $this->db->sql_query($do_we_hear_you);

			// Let's start a nice loop
			$we_hear_you = array();
			$hearing_you_loop = 0;
			while ($really_you_do = $this->db->sql_fetchrow($yes_we_do))
			{
				$our_file_to_copy = $really_you_do['url_link'] . $really_you_do['img_name'];
				
				$our_final_file_name = $really_you_do['img_name'];
				$our_final_file_name = str_replace("viewer.php?id=", "", $our_final_file_name);
				
				$file_to_copy = @fopen($our_file_to_copy, 'r');
				if ($file_to_copy)
				{
					$current = file_get_contents($our_file_to_copy); // current is our image link from HostingPics++
						if (!is_dir($dest_img))
						{
							mkdir($dest_img, 0755, true);
						}					
					$file_to = $dest_img . $our_final_file_name;
					file_put_contents($file_to, $current);

					$sql_analyzed = "UPDATE " . BACKUPHOSTINGPICS_IMAGES_TABLE . " SET images_analysed = 1 WHERE post_id =" . $really_you_do['result_id']; 
					$this->db->sql_query($sql_analyzed);
				}
			}
			$this->db->sql_freeresult($yes_we_do);
			
			$we_hear_you = implode('<br />', $we_hear_you);
			
			/* Here we have our pagination - automatically refreshs **/
			$page_next = ($page<$nb_pages) ? $page+1 : $page;
			$meta_info = append_sid("{$this->phpbb_root_path}backuphostingpics", 'mode=backup_images_to_server&p=' . $page_next);

			($page<$nb_pages) ? meta_refresh(3, $meta_info) : '';		
			
			$message = ($page<$nb_pages) ? $this->user->lang['BACKUP_IMAGES_WORK'] . '<br /><br />' . $this->user->lang('BACKUP_IMAGES_PROGRESS', $page*$limit, $count_distinct_rows) . '<br /><br />' : $this->user->lang('BACKUP_IMAGES_END', $count_distinct_rows, $count_distinct_rows) . '<br /><br />';
			
			$message .= ($page<$nb_pages) ? '' : sprintf($this->user->lang['MODIFY_DATABASE_LINKS'], '<a href="' . append_sid("{$this->phpbb_root_path}backuphostingpics", 'mode=modify_database_links') . '">', '</a>');
			
			trigger_error($message);
		break;

		case 'modify_database_links':

			$sql_count_distinct = 'SELECT COUNT(result_id) AS count_distinct_rows
					FROM ' . BACKUPHOSTINGPICS_IMAGES_TABLE;
			$result_count_distinct = $this->db->sql_query($sql_count_distinct);
			
			// The posts count is now available here
			$count_distinct_rows = (int) $this->db->sql_fetchfield('count_distinct_rows');

			// How many pages
			$nb_pages = ceil($count_distinct_rows/$limit);			
			
			// On which page are we?
			if (!$page)
			{
				$current_page = 1; // If no page number specified, let's take the first page
			}
			else
			{
				$current_page = $page; // If page number specified, let's take this page

				if($current_page > $nb_pages)
				{
					$current_page = $nb_pages; // In case number page bigger than total pages amount, let's take the last page
				}
			}			
			
			// Let's calculate from which point we take our datas
			$first_entry = ($current_page - 1) * $limit;
		
			// Let's start the job
 			$do_we_hear_you = "SELECT bi.result_id, bi.post_id, bi.url_link, bi.images_analysed, pf.post_id, pf.post_text
					FROM " . BACKUPHOSTINGPICS_IMAGES_TABLE . " bi
					LEFT JOIN forumphpbb_posts_test pf
					ON bi.post_id = pf.post_id
					ORDER BY bi.result_id
					LIMIT " . $first_entry . ", " . $limit;
			$yes_we_do = $this->db->sql_query($do_we_hear_you);

			// Let's start a nice loop+
			$we_hear_you = array();
			$test_unity = array();
			$hearing_you_loop = 0;
			while ($really_you_do = $this->db->sql_fetchrow($yes_we_do))
			{
				$message_txt = $this->db->sql_escape(utf8_normalize_nfc($really_you_do['post_text']));
				
			$sql2 = "UPDATE forumphpbb_posts_test SET post_text = REPLACE('" . $message_txt . "', '" . $really_you_do['url_link'] . "', '" .  $your_board_url . $dest_img . "') WHERE post_id =" . $really_you_do['post_id']; 
			$this->db->sql_query($sql2);
				
			}
			$this->db->sql_freeresult($yes_we_do);
			
			
			/* Here we have our pagination - automatically refreshs ***/
			$page_next = ($page<$nb_pages) ? $page+1 : $page;
			$meta_info = append_sid("{$this->phpbb_root_path}backuphostingpics", 'mode=modify_database_links&p=' . $page_next);

			($page<$nb_pages) ? meta_refresh(3, $meta_info) : '';		
			
			$message = ($page<$nb_pages) ? $this->user->lang['MODIFY_DATABASE_LINKS_WORK'] . '<br /><br />' . $this->user->lang('MODIFY_DATABASE_LINKS_PROGRESS', $page*$limit, $count_distinct_rows) . '<br /><br />' : $this->user->lang('MODIFY_DATABASE_LINKS_END', $count_distinct_rows, $count_distinct_rows) . '<br /><br />';
			
			$message .= ($page<$nb_pages) ? '' : sprintf($this->user->lang['END_WORK'], '<a href="' . append_sid("{$this->phpbb_root_path}backuphostingpics", 'mode=end_work') . '">', '</a>');
			
			trigger_error($message);
		break;

		default:
			$message = $this->user->lang['WELCOME_MSG'] . '<br /><br />';
			$message .= sprintf($this->user->lang['START_EXTENSION'], '<a href="' . append_sid("{$this->phpbb_root_path}backuphostingpics", 'mode=count_posts') . '">', '</a>');

			trigger_error($message);	
		break;
		}

		/* Header */
		page_header($this->user->lang['EXTENSION_TITLE']);
		
		// Template file
		$this->template->set_filenames(array(
			'body' => 'backuphostingpics.html')
		);

		// Footer
		page_footer();

	}
}