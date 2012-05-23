<?php if (!defined(JZ_SECURE_ACCESS)) die ('Security breach detected.');
/**
 * - JINZORA | Web-based Media Streamer -  
 * 
 * Jinzora is a Web-based media streamer, primarily desgined to stream MP3s 
 * (but can be used for any media file that can stream from HTTP). 
 * Jinzora can be integrated into a CMS site, run as a standalone application, 
 * or integrated into any PHP website.  It is released under the GNU GPL.
 * 
 * - Resources -
 * - Jinzora Author: Ross Carlson <ross@jasbone.com>
 * - Web: http://www.jinzora.org
 * - Documentation: http://www.jinzora.org/docs	
 * - Support: http://www.jinzora.org/forum
 * - Downloads: http://www.jinzora.org/downloads
 * - License: GNU GPL <http://www.gnu.org/copyleft/gpl.html>
 * 
 * - Contributors -
 * Please see http://www.jinzora.org/team.html
 * 
 * - Code Purpose -
 * - This is the leo's lyrics service.
 *
 * @since 01.14.05
 * @author Ross Carlson <ross@jinzora.org>
 * @author Ben Dodson <ben@jinzora.org>
 */

$jzSERVICE_INFO = array();
$jzSERVICE_INFO['name'] = "Jinzora Lyrics";
$jzSERVICE_INFO['url'] = "http://www.jinzora.com";

define('SERVICE_LYRICS_jinzora','true');

/*
 * Gets the lyrics via Leo's Lyrics
 * 
 * @author Ross Carlson
 * @version 1/15/05
 * @since 1/15/05
 * @param $track a jzMediaTrack
 **/

function SERVICE_GETLYRICS_jinzora($track) {
	global $include_path; 
	
	include_once($include_path. "lib/snoopy.class.php");
	
	$parent = $track->getAncestor("album");
	if (is_object($parent)){
		$album = str_replace("'","",$parent->getName());
		$gparent = $parent->getAncestor("artist");
		$artist = str_replace("'","",$gparent->getName());
	}
	$name = $track->getName();
	if (is_numeric(substr($name,0,2))){
		$name = trim(substr($name,2));
	}
	if (substr($name,0,1) == "-" or substr($name,0,1) == "_"){
		$name = trim(substr($name,1));
	}
	if (stristr($name,".")){
		$nArr = explode(".",$name);
		unset($nArr[count($nArr)-1]);
		$name = implode(".",$nArr);
	}
		
	// Let's up the max execution time here
	ini_set('max_execution_time','60000');

	// Now let's see if we can get close...
	$snoopy = new Snoopy;
	$snoopy->fetch("http://www.jinzora.com/lyrics.php?artist=". urlencode($artist). '&album='. urlencode($album). '&track='. urlencode($name));
	$contents = $snoopy->results;
	unset($snoopy);
				
	if ($contents == "false") {
		return false;
	}  
	
	return $contents;
}

?>