<?php
/**
*
* @package phpBB Extension - FoFa - Posting News
* @copyright (c) 2015 FoFa (http://forums.phpbb-fr.com/fofa-u89565/)
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
**/

if (!defined('IN_PHPBB'))
{
    exit;
}

if (empty($lang) || !is_array($lang))
{
    $lang = array();
}

// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine
//
// Some characters you may want to copy&paste:
// ’ « » “ ” …
//

$lang = array_merge($lang, array(
	/* Welcome Message */
	'EXTENSION_TITLE'	=> '« Back-up des images depuis HostingPics »',
	'WELCOME_MSG'	=> 'Bienvenue sur cette Extension phpBB destinée à récupérer les images que vous avez hébergées sur <a style="border-bottom:1px dashed grey;" href="http://www.hostingpics.net">HostingPics</a>. Afin d’aller à l’essentiel, nous partirons sur le principe que vous connaissez les bases de gestion d’un forum phpBB. Si tel ne devait pas être le cas, n’hésitez pas à ouvrir un ticket sur <a href="http://forums.phpbb-fr.com" style="border-bottom:1px dashed grey;">phpBB-fr.com</a>, le forum français d’assistance pour phpBB.<br /><br />Avant toute chose, fermez votre forum. Procédez ensuite à une sauvegarde de <strong>toutes les données de votre forum</strong> (fichiers ET base de données).<br /><br />Assurez-vous, enfin, d’avoir modifié les quelques lignes dans le fichier <strong>ext/fofa/backuphostingpics/controller/main_controller.php</strong> avec <u>VOS</u> informations personnelles.',
	'START_EXTENSION'	=> 'Si vous êtes prêt, vous pouvez démarrer le %s<u>processus de récupération des images depuis HostingPics</u>%s.',
	
	/* Counting Posts */
	'COUNT_POSTS_MSG'		=> array(
		0	=> 'L’analyseur n’a trouvé <strong>aucun message(s)</strong> contenant des liens vers <a style="border-bottom:1px dashed grey;" href="http://www.hostingpics.net">HostingPics</a>.',
		1	=> 'L’analyseur a trouvé <strong>un seul message(s)</strong> contenant des liens vers <a style="border-bottom:1px dashed grey;" href="http://www.hostingpics.net">HostingPics</a>.',
		2	=> 'L’analyseur a trouvé <strong>%d messages</strong> contenant des liens vers <a style="border-bottom:1px dashed grey;" href="http://www.hostingpics.net">HostingPics</a>.',
	),
	

	'CREATE_POSTS_LIST'	=> 'A présent, vous pouvez démarrer la %s<u>pré-sélection des messages depuis la base de données</u>%s',
	
	'CREATE_POSTS_LIST_DONE'		=> array(
		0	=> '<strong>Aucun message(s)</strong> n’a été trouvé pour la pré-sélection.',
		1	=> '<strong>Un seul message(s)</strong> a été pré-sélectionné.',
		2	=> '<strong>%d messages</strong> ont été pré-sélectionnnés.',
	),

	'SELECT_IMAGES_LINKS'	=> 'Vous pouvez maintenant %s<u>extraire les liens des images depuis la base de données</u>%s afin de pouvoir les enregistrer sur votre serveur.',
	
	'SELECT_IMAGES_LINKS_WORK'	=> 'Veuillez patienter pendant que le script enregistre les images de chaque message dans la base de données.',
	'SELECT_IMAGES_LINKS_PROGRESS'	=> 'Il y a eu actuellement %1$d messages traités sur un total de %2$d.',
	'SELECT_IMAGES_LINKS_END'	=> 'Le script a terminé l’enregistrement des images contenues dans les messages dans la base de données. Il y a eu au total %1$d messages traités sur un total de %2$d.',
	
	'BACKUP_IMAGES_TO_SERVER'	=> 'Vous pouvez dès à présent procéder au %s<u>transfert des images depuis HostingPics vers votre propre hébergement</u>%s.',

	'BACKUP_IMAGES_WORK'	=> 'Veuillez patienter pendant que le script copie les images sur votre serveur depuis HostingPics.',
	'BACKUP_IMAGES_PROGRESS'	=> 'Il y a eu actuellement %1$d liens traités sur un total de %2$d.',
	'BACKUP_IMAGES_END'	=> 'Le script a terminé la copie des images sur votre serveur. Il y a eu au total %1$d liens traités sur un total de %2$d.',
	
	'MODIFY_DATABASE_LINKS'	=> 'Vous pouvez dès à présent procéder à %s<u>la modification des liens dans la base de données</u>%s.',	
/**/
	'BACKUP_IMAGES_TO_SERVER'	=> '%sEnregistrer les images sur le serveur%s',
	
	'MODIFY_DATABASE_LINKS_WORK'	=> 'Veuillez patienter pendant que le script met à jour les liens dans la base de données.',
	'MODIFY_DATABASE_LINKS_PROGRESS'	=> 'Il y a eu actuellement %1$d liens traités sur un total de %2$d.',
	'MODIFY_DATABASE_LINKS_END'	=> 'Le script a terminé la modification des liens dans la base de données. Il y a eu au total %1$d liens traités sur un total de %2$d.',

	'END_WORK'	=> '%sFinaliser le travail%s',
	

)); 