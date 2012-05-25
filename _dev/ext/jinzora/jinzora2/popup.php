<?php define('JZ_SECURE_ACCESS','true');
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
	* - This page contains a number of tools that are used and displayed in popup boxes
	*
	* @since 02.17.04 
	* @author Ross Carlson <ross@jinzora.org>
	*/
	$include_path = '';
	include_once('jzBackend.php');
	$_GET = unurlize($_GET);
	$_POST = unpostize($_POST);
	$node = new jzMediaNode($_GET['jz_path']);
	$popup = new jzPopup();
	$popup->popupSwitch($_GET['ptype'],$node);
	exit();

	
	// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	//
	//             This section processes any form posts that may hit this page
	//
	// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	class jzPopup {	
	
  /* Constructor
   *
   **/
  function jzPopup() {
    global $jzUSER;

    // Now let's se if they selected a Genre, Artist, or Album:
    if (isset($_POST['chosenPath'])) {
      if (isset($_POST['jz_type']) && $_POST['jz_type'] == "track") {
				if (checkPermission($jzUSER,'play',$_POST['chosenPath']) === false) {
					$this->closeWindow(false);
				}
				$e = new jzMediaTrack($_POST['chosenPath']);
				$pl = new jzPlaylist();
				$pl->add($e);
				$pl->play();
				exit();
      } else {
				$return = $this->returnGoBackPage($_POST['return']);
      }
      
      //$url = $return. "&" . jz_encode("path") . "=". jz_encode(urlencode($_POST['chosenPath']));
      $link = array();
      $link['jz_path'] = $_POST['chosenPath'];
      
      // Now let's fix that if we need to
      
      // Ok, now that we've got the URL let's refresh the parent and close this window
      echo '<body onload="opener.location.href=\''. urlize($link) . '\';window.close();">';
      exit();
    }
  }

  /* The switch that controls the popup type.
   *
   * @author Ben Dodson
   * @version 1/28/05
   * @since 1/28/05
   *
   **/
  function popupSwitch($type,$node) {
    switch ($type) {
    case "genre":
      $this->displayAllGenre();
      break;
    case "artist":
      $this->displayAllArtists();
      break;
    case "album":
      $this->displayAllAlbums();
      break;
    case "track":
      $this->displayAllTrack();
      break;
    case "mozilla":
      $this->displayinstMozPlug();
      break;
    case "searchlyrics":
      $this->searchLyrics($node);
      break;
      break;
    case "scanformedia":
      $this->scanForMedia($node);
      break;
    case "getmetadata":
      $this->searchMetaData($node);
      break;
    case "readmore":
      $this->displayReadMore($node);
      break;
    case "addfeatured":
      $this->addToFeatured($node);
      break;	
    case "removefeatured":
      $this->removeFeatured($node);
      break;
    case "rateitem":
      $this->userRateItem($node);
      break;
    case "docs":
      $this->showDocs();
      break;
    case "usermanager":
      $this->userManager();
      break;
    case "trackinfo":
      $this->displayTrackInfo($_GET['jz_path']);
      break;
    case "topstuff":
      $this->displayTopStuff($node);
      break;
    case "preferences":
      $this->userPreferences();
      break;
    case "nodestats":
      $this->displayNodeStats($node);
      break;
    case "dupfinder":
      $this->displayDupFinder();
      break;
    case "sitenews":
      $this->displaySiteNews($node);
      break;
    case "jukezora":
      $this->displayJukezora();
      break;
    case "addlinktrack":
      $this->displayAddLinkTrack($node);
      break;
    case "setptype":
      $this->displaySetPType($node);
      break;
    case "iteminfo":
      $this->displayItemInfo($node);
      break;
    case "retagger":
      $this->displayReTagger($node);
      break;
    case "playlistedit":
      $this->displayPlaylistEditor();
      break;
    case "uploadmedia":
      $this->displayUploadMedia($node);
      break;
    case "showuploadstatus":
      $this->displayUploadStatus();
      break;
    case "sitesettings":
      $this->displaySiteSettings();
      break;
	case "popplayer":
      $this->displayPopPlayer();
      break;
	case "bulkedit":
      $this->displayBulkEdit($node);
      break;	
	case "autorenumber":
	  $this->displayRenumber($node);
      break;	
	case "getalbumart":
	  $this->displayGetAlbumArt($node);
      break;
	case "discussitem":
	  $this->displayDiscussion($node);
      break;
	case "requestmanager":
	  $this->displayRequestManager($node);
      break; 
	case "autopagetype":
	  $this->displayAutoPageType($node);
      break; 
	case "resizeart":
	  $this->displayArtResize($node);
      break; 
	case "viewlyricsfortrack":
	  $this->displayViewLyricsForTrack($node);
      break; 
    case "viewcurrentinfo":
      $this->viewCurrentlyPlaying($_GET['session']);
      break;
	case "artfromtags":
	  $this->displayArtFromTags($node);
	  break;
	case "importtagdata":
	  $this->displayTagDataImporter($node);
	  break;
	case "downloadtranscode":
	  $this->displayDownloadTranscode($node);
	  break;
	case "downloadtranscodedbundle":
	  $this->sendTranscodedBundle($node);
	  break;
	case "pdfcover":
	  $this->createPDFCover($node);
	  break;
	case "burncd":
	  $this->displayBurnCD($node);
	  break;
	case "mediamanager":
		$this->displayMediaManager($node);
		break;
	case "addpodcast":
		$this->displayAddPodcast($node);
		break;
	case "admintools":
		$this->displayAdminTools($_GET['jz_path']);
		break;		
	case "addtofavorites":
		$this->displayAddToFavorites($_GET['jz_path']);
		break;
	case "cachemanager":
		$this->displayCacheManager($_GET['jz_path']);
		break;	
	case "wmptrack":
		$this->displayWMPTrack();
		break;
	case "purgeShoutbox":
		$this->displayPurgeShoutbox();
		break;
	
	default:
      echo word('error: invalid ptype for popup.');
      break;
    }
  }
	// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	//
	//             This section contains all the functions
	//
	// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	
	
	
	
	/**
	* Displays the tool to purge the shoutbox
	*
	* @author Ross Carlson
	* @since 8.9.06
	* @version 8.9.06
	**/
	function displayPurgeShoutbox(){
		global $root_dir, $web_root;
		
		// Let's start the page header
		$this->displayPageTop("",word("Purge Shoutbox"));
		$this->openBlock();
		
		// Let's kill the file
		@unlink($web_root. $root_dir. "/data/yshout/logs/main.txt");
		
		echo "<center>";
		echo "<br>". word("Shoutbox Data Purged"). "!<br><br><br>";
		$this->closeButton(true);
		echo "</center>";
		
		$this->closeBlock();
	}
	
	/**
	* Displays the track details for the currently playing track in WMP
	*
	* @author Ross Carlson
	* @since 8.3.06
	* @version 8.3.06
	**/
	function displayWMPTrack(){
		global $this_site, $root_dir, $jzSERVICES, $web_root;
		
		// Let's setup our display object
		$display = new jzDisplay();
	
		// Let's create the track object
		$track = new jzMediaTrack($_GET['jz_path']);
		$meta = $track->getMeta();
		
		// Now let's get the album and artist
		$album = $track->getNaturalParent("album");
		$artist = $album->getNaturalParent("artist");
		$desc = $album->getDescription();
		while (substr($desc,0,4) == "<br>" or substr($desc,0,6) == "<br />"){
			if (substr($desc,0,4) == "<br>"){
				$desc = substr($desc,5);
			}
			if (substr($desc,0,6) == "<br />"){
				$desc = substr($desc,7);
			}
		}
		
		// Now let's get the art
		$art = $album->getMainArt("200x200");
		if ($art <> ""){
			$albumArt = $display->returnImage($art,$album->getName(),150,150,"limit",false,false,"left","3","3");
		} else {
			$art = $jzSERVICES->createImage($web_root. $root_dir. '/style/images/default.jpg', "200x200", $track->getName(), "audio", "true");
			$albumArt = '<img src="'. $this_site. $root_dir. "/". $art. '" border="0" align="left" hspace="3" vspace="3">';
		}
		
		// Now let's setup Smarty
		$smarty = smartySetup();
		
		// Let's setup the Smarty variables
		$smarty->assign('trackName', $track->getName());
		$smarty->assign('albumName', $album->getName());
		$smarty->assign('artistName', $artist->getName());
		$smarty->assign('albumArt', $albumArt);
		$smarty->assign('lyrics', $meta['lyrics']);
		$smarty->assign('trackNum', $meta['number']);
		$smarty->assign('albumDesc', $desc);
		$smarty->assign('totalTracks', $_GET['totalTracks']);
		
		// Now let's display the template
		$smarty->display(SMARTY_ROOT. 'templates/general/asx-display.tpl');
	}
	
	/**
	* Displays the cache management tools
	*
	* @author Ross Carlson
	* @since 2.22.06
	* @version 2.22.06
	* @param $path The node that we are viewing
	**/
	function displayCacheManager($path){
		global $web_root, $root_dir;
		
		// Let's start the page header
		$this->displayPageTop("",word("Cache Manager"));
		$this->openBlock();
		
		// Let's create the node
		$node = new jzMediaNode($path);
		
		// Did they want to do something?
		if (isset($_GET['subpage'])){
			switch ($_GET['subpage']){
				case "deleteall":
					$i=0;
					$d = dir($web_root. $root_dir. "/temp/cache");
					while ($entry = $d->read()) {
						if ($entry == "." || $entry == "..") {
							continue;
						}
						if (@unlink($web_root. $root_dir. "/temp/cache/". $entry)){
							$i++;
						}
					}
					echo word('%s cache files deleted.', $i);
				break;
				case "thisnode":
					$display = new jzDisplay();
					$display->purgeCachedPage($node);
					$nodes = $node->getSubNodes("nodes", -1);
					$i=1;
					foreach ($nodes as $item){
						$display->purgeCachedPage($item);
						$i++;
					}
					echo word("%s nodes purged", $i);
				break;
				case "viewsize":
					$d = dir($web_root. $root_dir. "/temp/cache");
					$size=0;
					while ($entry = $d->read()) {
						$size = $size + filesize($web_root. $root_dir. "/temp/cache/". $entry);
					}
					echo word("Total cache size: %s MB", round((($size / 1024) / 1024),2));
				break;
			}
			echo "<br><br>";
		}
		
		
		$url_array = array();
		$url_array['jz_path'] = $node->getPath("String");
		$url_array['action'] = "popup";
		$url_array['ptype'] = "cachemanager";  
		
		$url_array['subpage'] = "deleteall";  
		echo '<a href="'. urlize($url_array). '">'. word("Purge ALL caches"). '</a><br>';
		
		$url_array['subpage'] = "thisnode";  
		echo '<a href="'. urlize($url_array). '">'. word("Purge Cache for"). ": ". $node->getName(). '</a><br>';
		
		$url_array['subpage'] = "viewsize";  
		echo '<a href="'. urlize($url_array). '">'. word("View Cache Size"). '</a><br><br>';		

		$this->closeButton();
		$this->closeBlock();	
	}
	
	
	/**
	* Displays the quick box to add an item to favorites
	*
	* @author Ross Carlson
	* @since 12.17.05
	* @version 12.17.05
	* @param $path The node that we are viewing
	**/
	function displayAddToFavorites($path){
		global $include_path, $jzUSER;
		
		$node = new jzMediaNode($path);
		$display = new jzDisplay();
		$be = new jzBackend();
		
		// Let's start the page header
		$this->displayPageTop("",word("Adding to Favorites"));
		$this->openBlock();
		echo word("Adding"). ": ". $node->getName();
		
		// Now let's add it
		
		
		$this->closeBlock();		
	}
	
	/**
	* Displays the Admin Tools Section
	*
	* @author Ross Carlson
	* @since 11/28/05
	* @version 11/28/05
	* @param $node The node that we are viewing
	**/
	function displayAdminTools($path){
		global $include_path, $jzUSER, $allow_filesystem_modify, $enable_podcast_subscribe;
		
		$node = new jzMediaNode($path);		
		$display = new jzDisplay();
		
		// Let's start the page header
		$this->displayPageTop("",word("Admin Tools"));
		$this->openBlock();
		
		if ($jzUSER->getSetting('admin') <> true){
			echo "<br><br><br><center>PERMISSION DENIED!!!";
			$this->closeBlock();		
		}
		
		// Let's start our tabs
		$display->displayTabs(array("Media Management","Meta Data","System Tools"));
		
		// Let's setup our links
		$url_array = array();
		$url_array['jz_path'] = $node->getPath("String");
		$url_array['action'] = "popup";
		
		// Now let's build an array of all the values for below
		if (checkPermission($jzUSER,"upload",$node->getPath("String")) and $allow_filesystem_modify == "true") {
			$url_array['ptype'] = "uploadmedia";  
			$valArr[] = '<a href="'. urlize($url_array). '">'. word("Add Media"). '</a>'; 
		}
		$url_array['ptype'] = "addlinktrack";  
		$valArr[] = '<a href="'. urlize($url_array). '">'. word("Add Link Track"). '</a>';
		
		$url_array['ptype'] = "setptype";  
		$valArr[] = '<a href="'. urlize($url_array). '">'. word("Set Page Type"). '</a>';
		
		$url_array['ptype'] = "scanformedia";  
		$valArr[] = '<a href="'. urlize($url_array). '">'. word("Rescan Media"). '</a>';
		
		$url_array['ptype'] = "artfromtags";  
		$valArr[] = '<a href="'. urlize($url_array). '">'. word("Pull art from Tag Data"). '</a>';
		
		if ($node->getPType() == "artist" or $node->getPType() == "album"){
			// Ok, is it already featured?
			if (!$node->isFeatured()){
				$url_array['ptype'] = "addfeatured"; 
				$valArr[] = '<a href="'. urlize($url_array). '">'. word("Add to Featured"). '</a>';
			} else {
				$url_array['ptype'] = "removefeatured"; 
				$valArr[] = '<a href="'. urlize($url_array). '">'. word("Remove from Featured"). '</a>';
			}
		}
		
		if ($node->getPType() == "album"){
			$url_array['ptype'] = "bulkedit"; 
			$valArr[] = '<a href="'. urlize($url_array). '">'. word("Bulk Edit"). '</a>';
			
			$url_array['ptype'] = "getalbumart"; 
			$valArr[] = '<a href="'. urlize($url_array). '">'. word("Search for Album Art"). '</a>';
			
			$url_array['ptype'] = "pdfcover"; 
			$valArr[] = '<a href="'. urlize($url_array). '">'. word("Create PDF Cover"). '</a>';
		}
		
		if ($enable_podcast_subscribe == "true"){
			$url_array['ptype'] = "addpodcast"; 
			$valArr[] = '<a href="'. urlize($url_array). '">'. word("Podcast Manager"). '</a>';
		}
		
		// Now let's put the content into the tabs
		$i=0;
		echo '<div id="panel1" class="panel"><table width="90%" cellpadding="8" cellspacing="0" border="0">';
		foreach ($valArr as $item){
			if ($i==0){
				echo "</tr><tr>";
			}
			echo "<td>";
			echo $item;
			echo "</td>";
			$i++;
			if ($i==3){$i=0;}
		}
		echo '</table></div>';
		?>
		
		<div id="panel2" class="panel">
			<table width="90%" cellpadding="5" cellspacing="0" border="0">
				<tr>
					<td>
						<?php $url_array['ptype'] = "getmetadata";  echo '<a href="'. urlize($url_array). '">'. word("Retrieve Meta Data"). '</a>'; ?>
					</td>
					<td>
						<?php $url_array['ptype'] = "searchlyrics";  echo '<a href="'. urlize($url_array). '">'. word("Retrieve Lyrics"). '</a>'; ?>
					</td>
					<td>
						<?php $url_array['ptype'] = "resizeart";  echo '<a href="'. urlize($url_array). '">'. word("Resize All Art"). '</a>'; ?>
					</td>
				</tr>
				<tr>
					<td>
						<?php $url_array['ptype'] = "autorenumber";  echo '<a href="'. urlize($url_array). '">'. word("Auto Renumber"). '</a>'; ?>		
					</td>
					<td>
						<?php $url_array['ptype'] = "iteminfo";  echo '<a href="'. urlize($url_array). '">'. word("Item Information"). '</a>'; ?>		
					</td>
					<td>
						<?php $url_array['ptype'] = "retagger";  echo '<a href="'. urlize($url_array). '">'. word("Retag Tracks"). '</a>'; ?>		
					</td>
				</tr>
			</table>
		</div>   
		<div id="panel3" class="panel">
			<table width="90%" cellpadding="5" cellspacing="0" border="0">
				<tr>
					<td>
						<?php $url_array['ptype'] = "mediamanager";  echo '<a href="'. urlize($url_array). '">'. word("Media Manager"). '</a>'; ?>
					</td>
					<td>
						<?php $url_array['ptype'] = "usermanager";  echo '<a href="'. urlize($url_array). '">'. word("User Manager"). '</a>'; ?>
					</td>
					<td>
						<?php $url_array['ptype'] = "sitesettings";  echo '<a href="'. urlize($url_array). '">'. word("Settings Manager"). '</a>'; ?>
					</td>
				</tr>
				<tr>
					<td>
						<?php $url_array['ptype'] = "sitenews";  echo '<a href="'. urlize($url_array). '">'. word("Manage Site News"). '</a>'; ?>
					</td>
					<td>
						<?php $url_array['ptype'] = "nodestats"; unset($url_array['jz_path']); echo '<a href="'. urlize($url_array). '">'. word("Show Full Site Stats"). '</a>'; ?>
					</td>
					<td>
						<!--<?php $url_array['ptype'] = "dupfinder";  echo '<a href="'. urlize($url_array). '">'. word("Duplicate Finder"). '</a>'; ?>-->
					</td>
				</tr>
				<tr>
					<td>
						<?php $url_array['ptype'] = "cachemanager";  $url_array['jz_path'] = $node->getPath("String"); echo '<a href="'. urlize($url_array). '">'. word("Cache Manager"). '</a>'; ?>
					</td>
					<td>

					</td>
					<td>

					</td>
				</tr>
			</table>
		</div>   
		<?php
		$this->closeBlock();		
	}
	
	/**
	* Displays the Add Podcast Subscribe tool
	*
	* @author Ross Carlson
	* @since 11/02/05
	* @version 11/02/05
	* @param $node The node that we are viewing
	**/
	function displayAddPodcast($node){
		global $include_path, $podcast_folder;
		
		// Let's start the page header
		$this->displayPageTop("",word("Subscribe to Podcast"));
		$this->openBlock();
		
		$display = new jzDisplay();
		$be = new jzBackend();
		
		// Did they want to update a podcast
		if (isset($_GET['feed_path'])){
			if ($_GET['sub_action'] == "update"){
				$_POST['edit_podcast_add'] = "TRUE";
				$_POST['edit_podcast_path'] = $_GET['feed_path'];
				$_POST['edit_podcast_title'] = $_GET['feed_title'];
				$_POST['edit_podcast_url'] = $_GET['feed_url'];
				$_POST['edit_podcast_max'] = $_GET['feed_number'];
			}
		}

		// Did the subscribe?
		if (isset($_POST['edit_podcast_add'])){
			// Let's track this podcast
			$pData = $be->loadData("podcast");
			$i = count($pData)+1;
			$pArr[$i]['title'] = $_POST['edit_podcast_title'];
			$pArr[$i]['url'] = $_POST['edit_podcast_url'];
			$pArr[$i]['path'] = $_POST['edit_podcast_path'];
			$pArr[$i]['number'] = $_POST['edit_podcast_max'];		
			
			if (is_array($pData)){
				$add = true;
				foreach($pData as $data){
					if ($data['title'] == $_POST['edit_podcast_title']){
						$add = false;
					}
				}
				if ($add){
					$nArr = array_merge($pData,$pArr);
					$be->storeData("podcast",$nArr);
				}
			} else {
				$be->storeData("podcast",$pArr);
			}
			
			// Now let's get the data
			$retArray = parsePodcastXML($_POST['edit_podcast_url']);
			
			$title = $retArray['title']; unset($retArray['title']);
			$desc = $retArray['desc']; unset($retArray['desc']);
			$desc = str_replace("]]>","",str_replace("<![CDATA[","",$desc));
			$pubDate = $retArray['pubDate']; unset($retArray['pubDate']);
			$image = $retArray['image']; unset($retArray['image']);
			
			// Now let's import
			echo '<div id="track"></div>';
			echo '<div id="pbar"></div>';
			?>
			<script language="javascript">
				t = document.getElementById("track");
				p = document.getElementById("pbar");
				p.innerHTML = '<?php echo "<br><br><center><img src=${include_path}style/images/progress-bar.gif><br>". word("Please wait"). "...</center>"; ?>';									
				-->
			</SCRIPT>
			<?php 
			
			if (stristr($image,"http://")){				
				if (substr($podcast_folder,0,1) <> "/"){
					$dir = str_replace("\\","/",getcwd()). "/". $podcast_folder. "/". $title;
				} else {
					$dir = $podcast_folder. "/". $title;
				}
				// Now let's create the directory we need
				makedir($dir);				
				$imgFile = $dir. "/". $title. ".jpg";			
				$iData = file_get_contents($image);
				$handle = fopen($imgFile, "w");
				fwrite($handle,$iData);
				fclose ($handle);
			}
			
			// Now let's create the node in the backend and assign it some values
			$newNode = new jzMediaNode($node->getPath("string"). "/". $_POST['edit_podcast_path']);
			$newNode->addDescription($desc);
			$newNode->addMainArt($imgFile);

			// Now let's loop and look at each enclosure		
			$i=1;	
			foreach($retArray as $item){		
				// Let's grab it
				$track = getPodcastData($item, $title);
				
				if (stristr($track,".mp3")){
					// Now that we've got the link we need to add it to the backend
					$ext = substr($item['file'],strlen($item['file'])-3,3);
					$nTrack = trim(cleanFileName($item['title']. ".". $ext));

					$pArr = explode("/",$_POST['edit_podcast_path']);
					$path = array();
					foreach($pArr as $p){
						$path[] = $p;
					}
					$path[] = $nTrack;	
					$tr = $node->inject($path, $track);
					if ($tr !== false) {
						$meta = $tr->getMeta();
						$meta['title'] = $item['title'];
						$tr->setMeta($meta);
					}
				}
				
				// Now should we stop?
				if ($_POST['edit_podcast_max'] <> "ALL" and $_POST['edit_podcast_max'] <> ""){
					if ($_POST['edit_podcast_max'] == $i){
						break;
					}
				}
				$i++;
			}			

			?>
			<script language="javascript">
				p.innerHTML = '&nbsp;';									
				t.innerHTML = '<br><center><?php echo word("Updates Complete!"); ?></center>';									
				-->
			</SCRIPT>
			<?php 
		}
		
		$arr = array();
		$arr['action'] = "popup";
		$arr['ptype'] = "addpodcast";
		$arr['sub_action'] = "addmediadir";
		$arr['jz_path'] = $node->getPath('String');
		?>
		<form action="<?php echo urlize($arr); ?>" method="POST" name="setup8">
			<table>
				<tr>
					<td>
						Title:
					</td>
					<td>
						<input type="text" name="edit_podcast_title" class="jz_input" size="40">
					</td>
				</tr>
				<tr>
					<td>
						URL:
					</td>
					<td>
						<input type="text" name="edit_podcast_url" class="jz_input" size="40">
					</td>
				</tr>
				<tr>
					<td>
						New Path:
					</td>
					<td>
						<input type="text" name="edit_podcast_path" value="New Podcast" class="jz_input" size="40">
					</td>
				</tr>
				<tr>
					<td>
						Max Tracks:
					</td>
					<td>
						<select name="edit_podcast_max" class="jz_select">
							<option value="ALL">All</option>
							<option value="1">1</option>
							<option value="5">5</option>
							<option value="10">10</option>
							<option value="25">25</option>
						</select>
					</td>
				</tr>
			</table>
			<br>
			<center>
				<input type="submit" name="edit_podcast_add" value="<?php echo word("Subscribe"); ?>" class="jz_submit">
			</center>
		</form>		
		<br><br>
		<strong><?php echo word("Managing Existing Podcasts"); ?></strong><br>
		<?php
			$pData = $be->loadData("podcast");
			if (is_array($pData)){
				?>
				<table>
					<tr>
						<td>
							<strong><?php echo word("Title"); ?></strong>
						</td>
						<td>
							<strong><?php echo word("Location"); ?></strong>
						</td>
						<td>
							<strong><?php echo word("Function"); ?></strong>
						</td>
					</tr>
					<?php
						foreach($pData as $data){
							echo '<tr><td>';
							echo $data['title']. " &nbsp; ";
							echo '</td><td>';
							echo $data['path']. " &nbsp; ";
							echo '</td><td>';
							
							$arr['action'] = "popup";
							$arr['ptype'] = "addpodcast";
							$arr['feed_path'] = $data['path'];
							$arr['feed_title'] = $data['title'];
							$arr['feed_url'] = $data['url'];
							$arr['feed_number'] = $data['number'];
							$arr['jz_path'] = $node->getPath('String');
							
							$arr['sub_action'] = "update";							
							echo '<a href="'. urlize($arr). '">'. word("Update"). '</a>';
							/*
							echo " | ";
							
							$arr['sub_action'] = "delete";
							echo '<a href="'. urlize($arr). '">'. word("Delete"). '</a>';
							*/
							echo '</td></tr>';					
							unset($arr);
						}
					?>
				</table>
			<?php
		} else {
			echo " - ". word("None exist");
		}
		$this->closeBlock();
	}
	
	
	/**
	* Displays the media management tools - useful for MANY media functions
	*
	* @author Ross Carlson
	* @since 7/01/05
	* @version 7/01/05
	* @param $node The node that we are viewing
	**/
	function displayMediaManager($node){
		global $include_path, $jz_lang_file, $root_dir, $backend, $media_dirs, $default_art, $audio_types, $video_types, $ext_graphic; 
		
		// Let's start the page header
		$this->displayPageTop("",word("Media Manager"));
		$this->openBlock();
		
		// Now let's see if they wanted to do something
		if (isset($_GET['sub_action'])){
			// Ok, now what did they want to do?
			switch ($_GET['sub_action']){
				case "delmediadir":
					// Ok, now they wanted to wack this node manually
					echo "To Do :-)"; 
				break;
				case "addmediadir":
					// Ok, did they already want to add a directory?
					if (isset($_POST['edit_media_path'])){
						// Ok, they want to import so let's do it...
						// First let's resize so we've got some space
						?>
						<SCRIPT LANGUAGE=JAVASCRIPT TYPE="TEXT/JAVASCRIPT"><!--\
							window.resizeTo(600,400)
						-->
						</SCRIPT>
						<?php	
						echo word("Please wait while we import your media..."); 
						echo '<br><br>';
						echo '<strong>';
						$media_dir = str_replace("//","/",str_replace("\\","/",$_POST['edit_media_path']));
						echo word("Importing media from:"). " ". $media_dir; 
						echo '</strong><br>';
						
						flushdisplay();
						
						$_POST['media_path'] = stripSlashes($_POST['edit_media_path']);
						// Now we need to track ALL the media paths they enter
						if (!isset($_SESSION['all_media_paths'])){ $_SESSION['all_media_paths'] = "";}
						// Now let's make sure that's a valid path and is readable
						if (is_dir($_POST['media_path']) and !stristr($_SESSION['all_media_paths'],$_POST['media_path'])){
							$_SESSION['all_media_paths'] .= $_POST['edit_media_path']. "|";
						}
						
						// actually import it:						
						
						// First let's get a listing of ALL files so we'll be able to estimate how long this will take
						// Was this set from the popup?
						if ($_POST['edit_media_length'] <> ""){
							$len = $_POST['edit_media_length'];
						} else {
							//echo "<br><strong>". $word_analyzing_import. '</strong>';
							echo '<div id="filecount"></div>';
							?>
							<script language="javascript">
								fc = document.getElementById("filecount");							
								-->
							</SCRIPT>
							<?php
							$readCtr = 0; $_SESSION['jz_full_counter'] = 0;
							readAllDirs2($media_dir, $readCtr);
							// Now let's see how long we think it will take
							$len = $_SESSION['jz_full_counter'];
						}
						if ($len == 0){$len=1;}
						$_SESSION['jz_import_full_ammount'] = $len;
						$_SESSION['jz_import_start_time'] = time();
						$_SESSION['jz_install_timeLeft'] = 0;
						
						?>
						<script language="javascript">
							fc.innerHTML = '&nbsp;';									
							-->
						</SCRIPT>
						<?php 
						
						// Now let's import
						echo '<div id="importProgress"></div>';
						echo '<div id="importStatus"></div>';
						
						// Now let's know when we started
						$startTime = time();
						if ($media_dir == "" || !is_dir($media_dir)){
							echo "<strong>". word("Invalid Directory!"). "</strong>";
							$error = true;
						} else {
							// Let's setup the object for the HTML updates
							?>
							<script language="javascript">
							d = document.getElementById("importStatus");
							p = document.getElementById("importProgress");							
							-->
							</SCRIPT>
							<?php							
							set_time_limit(0);
							// Now let's update the cache
							$_SESSION['jz_import_progress'] = 1;
							$_SESSION['jz_import_full_progress'] = 0;

							// Now do they want to read the tags?
							$readTags = false;
							if ($backend <> "database"){
								$readTags = true;
							}
							
							// Now let's set the new media_dirs variable
							$newMediaDirs = $media_dirs. "|". $media_dir;
							
							// Now we need to write this to the settings file
							if (writeSetting("media_dirs", $newMediaDirs, $include_path. "settings.php")){
								// Let's create an empty root node
								$root = &new jzMediaNode();
								//$root->updateCache(true,$media_dir,true,false,$readTags);
								updateNodeCache($root,true,true,false,$readTags,$media_dir);
							} else {
								?>
								<script language="javascript">
									alert("There was an error writing to your settings file at <?php echo $include_path; ?>settings.php");
									-->
								</SCRIPT>
								<?php 
								exit();
							}
						}
					?>
					<script language="javascript">
						d = document.getElementById("importStatus");
						p = document.getElementById("importProgress");
						p.innerHTML = '<?php echo word("Import Complete!"); ?></strong> (<?php echo round(((time() - $startTime)/60),2). " ". word("minutes"); ?>)';						
						d.innerHTML = '<br><?php echo "<strong>". word("Import Stats"). "</strong><br>"; ?>';						
						-->
					</SCRIPT>
					<?php 
						// Now let's show them how much they imported
						$root_node = &new jzMediaNode();
						
						if (distanceTo('genre') !== false)
							$genres = $root_node->getSubNodeCount('nodes',distanceTo('genre'));
						if (distanceTo('artist') !== false)
							$artists = $root_node->getSubNodeCount('nodes',distanceTo('artist'));
						if (distanceTo('album') !== false)
							$albums = $root_node->getSubNodeCount('nodes',distanceTo('album'));
							
						$tracks = $root_node->getSubNodeCount('tracks',-1);
						$genres = isset($genres) ? $genres : "false";
						$artists = isset($artists) ? $artists : "false";
						$albums = isset($albums) ? $albums : "false";
						$tracks = isset($tracks) ? $tracks : "false";
						
						echo word("Genres"). ": ". $genres. "<br>";
						echo word("Artists"). ": ". $artists. "<br>";
						echo word("Albums"). ": ". $albums. "<br>";						
						echo word("Tracks"). ": ". $tracks. "<br><br>";
						
						?>
						<script language="javascript">
							opener.location.reload(true);
							-->
						</SCRIPT>
						<?php
					} else {
						// Ok, they wanted to add media, let's show them the form
						// Let's setup the form for the user to choose from
						$arr = array();
						$arr['action'] = "popup";
						$arr['ptype'] = "mediamanager";
						$arr['sub_action'] = "addmediadir";
						$arr['jz_path'] = $node->getPath('String');
						?>
						<script language="JavaScript">
						<!--
							function browseMedia(){
								var sw = screen.width;
								var sh = screen.height;
								var winOpt = "width=250,height=300,left=" + ((sw - 300) / 2) + ",top=" + ((sh - 300) / 2) + ",menubar=no,toolbar=no,location=no,directories=no,status=no,scrollbars=yes,resizable=no";
								thisWin = window.open('<?php echo $root_dir; ?>/install/browse.php?lang=<?php echo $jz_lang_file; ?>&prefix=edit	','AddMedia',winOpt);
							}
						// -->
						</script>
						<form action="<?php echo urlize($arr); ?>" method="POST" name="setup8">
							<center>
								<input type="text" name="edit_media_path" size="30"> 
								<input type="hidden" name="edit_media_length" value="">
								<input onClick="browseMedia();" type="button" value="Browse">
								<br><br>
								<input type="submit" name="edit_import_media_dir" value="<?php echo word("Import Directory"); ?>" class="jz_submit">
							</center>
						</form>					
						<?php
					}
				break;
			}
		}
		
		$arr = array();
		$arr['action'] = "popup";
		$arr['ptype'] = "mediamanager";
		$arr['jz_path'] = $node->getPath("string");
		
		$arr['sub_action'] = "addmediadir";
		echo "<center>";
		echo '<a href="'. urlize($arr). '">'. word("Add Media Directory"). '</a>';
		
		$arr['sub_action'] = "delmediadir";
		//echo " | ";
		//echo '<a href="'. urlize($arr). '">'. word("Delete Media"). '</a>';
		
		echo "<br><br><br>";
		$this->closeButton();
		echo "</center>";
		
		$this->openBlock();
		
	}
	
	
	/**
	* Allows the user to burn a CD
	*
	* @author Ross Carlson
	* @since 6/20/05
	* @version 6/20/05
	* @param $node The node that we are viewing
	**/
	function displayBurnCD($node){
		global $include_path, $jzSERVICES;
		
		$this->displayPageTop("",word("Burn CD"));
		$this->openBlock();
		
		// Did they want to burn?
		if (isset($_GET['sub_action'])){
			if ($_GET['sub_action'] == "create"){
				// Ok, we need to get a list of all the tracks
				$tracks = $node->getSubNodes("tracks",-1);
				$fileArray = array();
				foreach ($tracks as $track){
					// Now we need to resample each one to a WAV file
					// First let's create the new file name - we'll make this random
					echo "Resampling: ". $track->getName(). "<br>";
					flushdisplay();
					$fileArray[] = $jzSERVICES->createResampledTrack($track->getDataPath(),"wav", "", "", getcwd(). "/data/burn/". $track->getName(). ".wav");
					flushdisplay();
				}
				
				// Now let's burn this list of files
				$album = $node->getName();
				$art = $node->getAncestor("artist");
				$artist = $art->getName();
				
				echo "<br><br>";
				$jzSERVICES->burnTracks($node, $artist, $album);
				
				exit();
			}
		}
		
		$dlarr = array();
		$dlarr['action'] = "popup";
		$dlarr['ptype'] = "burncd";
		$dlarr['sub_action'] = "create";
		$dlarr['jz_path'] = $node->getPath("string");
		
		echo '<a href="'. urlize($dlarr). '">Burn CD</a>';
		
		$this->closeBlock();
	}
	
	
	
	/**
	* Creates a PDF of the cover of the album
	*
	* @author Ross Carlson
	* @since 6/20/05
	* @version 6/20/05
	* @param $node The node that we are viewing
	**/
	function createPDFCover($node){
		global $config_version;
		
		// Did they want to create it?
		if (isset($_GET['sub_action'])){
			if ($_GET['sub_action'] == "create"){
				// Ok, let's create the PDF
				// Code borrowed from Netjukebox - www.netjukebox.nl
				$album = $node->getName();
				$artnode = $node->getAncestor("artist");
				$artist = $artnode->getName();

				$pdf = pdf_new();
				pdf_open_file($pdf, '');
				
				pdf_set_info($pdf, 'Title', $artist. " - ". $album);
				pdf_set_info($pdf, 'Creator', 'Jinzora ' . $config_version);
				
				pdf_begin_page($pdf, 595, 842);		//A4
				$scale = 2.834645676;				//mm to dtp-point (1 point = 1/72 inch)
				pdf_scale($pdf, $scale, $scale);
				pdf_setlinewidth ($pdf, .1);
				
				//  +---------------------------------------------------------------------------+
				//  | PDF Back Cover                                                            |
				//  +---------------------------------------------------------------------------+
				$x0 = 30;
				$y0 = 22;
				pdf_translate($pdf, $x0, $y0);
				
				pdf_moveto($pdf, 0, -1);
				pdf_lineto($pdf, 0, -11);
				pdf_moveto($pdf, 6.5, -1);
				pdf_lineto($pdf, 6.5, -11);
				pdf_moveto($pdf, 144.5, -1);
				pdf_lineto($pdf, 144.5, -11);
				pdf_moveto($pdf, 151, -1);
				pdf_lineto($pdf, 151, -11);
				pdf_moveto($pdf, 0, 119);
				pdf_lineto($pdf, 0, 129);
				pdf_moveto($pdf, 6.5, 119);
				pdf_lineto($pdf, 6.5, 129);
				pdf_moveto($pdf, 144.5, 119);
				pdf_lineto($pdf, 144.5, 129);
				pdf_moveto($pdf, 151, 119);
				pdf_lineto($pdf, 151, 129);
				pdf_moveto($pdf, -11, 0);
				pdf_lineto($pdf, -1 , 0);
				pdf_moveto($pdf, -11, 118);
				pdf_lineto($pdf, -1 , 118);
				pdf_moveto($pdf, 152, 0);
				pdf_lineto($pdf, 162, 0);
				pdf_moveto($pdf, 152, 118);
				pdf_lineto($pdf, 162, 118);
				pdf_stroke($pdf);
								
				$temp = '';
				// Now let's get the tracks for this album
				$tracks = $node->getSubNodes("tracks",-1);
				foreach ($tracks as $track){
					$meta = $track->getMeta();
					$temp .= "         ". $meta['number']. " - ". $track->getName() . "\n";
				}				

				$font = pdf_findfont($pdf, 'Helvetica', 'winansi', 0); 
				pdf_setfont($pdf, $font, 3);
				pdf_show_boxed($pdf, $temp, 6.5, 0, 138, 108, 'left', '');
			
				pdf_setfont($pdf, $font, 4);
				pdf_set_text_pos($pdf,2,-4.5); //y,-x
				pdf_rotate($pdf, 90);
				pdf_show($pdf, $artist . ' - ' . $album);
				pdf_rotate($pdf, -90);
				
				pdf_setfont($pdf, $font, 4);
				pdf_set_text_pos($pdf,-116 ,151-4.5); //-y,x
				pdf_rotate($pdf, -90);
				pdf_show($pdf, $artist . ' - ' . $album);
				pdf_rotate($pdf, 90);
				
				
				
				//  +---------------------------------------------------------------------------+
				//  | PDF Front Cover                                                           |
				//  +---------------------------------------------------------------------------+
				$x0 = 44 - $x0;
				$y0 = 160 - $y0;
				pdf_translate($pdf, $x0, $y0);
				
				pdf_moveto($pdf, 0, -1);
				pdf_lineto($pdf, 0, -11);
				pdf_moveto($pdf, 121, -1);
				pdf_lineto($pdf, 121, -11);
				pdf_moveto($pdf, 0, 121);
				pdf_lineto($pdf, 0, 131);
				pdf_moveto($pdf, 121, 121);
				pdf_lineto($pdf, 121, 131);
				pdf_moveto($pdf, -1, 0);
				pdf_lineto($pdf, -11, 0);
				pdf_moveto($pdf, -1, 120);
				pdf_lineto($pdf, -11, 120);
				pdf_moveto($pdf, 122, 0);
				pdf_lineto($pdf, 132, 0);
				pdf_moveto($pdf, 122, 120);
				pdf_lineto($pdf, 132, 120);
				pdf_stroke($pdf);
				
				// Do we have album art?
				if ($node->getMainArt() <> false){
					$extension = substr(strrchr($node->getMainArt(), '.'), 1);
					$extension = strtolower($extension);
					if ($extension == 'jpg')	$pdfdfimage = pdf_open_image_file($pdf, 'jpeg', $node->getMainArt(), '', 0);
					if ($extension == 'png')	$pdfdfimage = pdf_open_image_file($pdf, 'png', $node->getMainArt(), '', 0);
					if ($extension == 'gif')	$pdfdfimage = pdf_open_image_file($pdf, 'gif', $node->getMainArt(), '', 0);
					$sx = 121 / pdf_get_value($pdf, 'imagewidth', $pdfdfimage);
					$sy = 120 / pdf_get_value($pdf, 'imageheight', $pdfdfimage);
					
					pdf_scale($pdf, $sx, $sy);
					pdf_place_image($pdf, $pdfdfimage, 0, 0 , 1);
				}
				
				//  +---------------------------------------------------------------------------+
				//  | Close PDF                                                                 |
				//  +---------------------------------------------------------------------------+
				pdf_end_page($pdf);
				pdf_close($pdf);
				$buffer = pdf_get_buffer($pdf);
				$file = $artist . ' - ' . $album . '.pdf';
				header('Content-Type: application/force-download');
				header('Content-Transfer-Encoding: binary');
				header('Content-Disposition: attachment; filename="' . $file . '"'); //rawurlencode not needed for header
				
				echo $buffer;
				pdf_delete($pdf);
				exit();
			}
		}
	
		$this->displayPageTop("",word("Create PDF Cover"));
		$this->openBlock();
		
		$dlarr = array();
		$dlarr['action'] = "popup";
		$dlarr['ptype'] = "pdfcover";
		$dlarr['sub_action'] = "create";
		$dlarr['jz_path'] = $node->getPath("string");
		
		echo "<center>To generate the PDF cover for the album click below<br><br><br>";
		echo '<a href="'. urlize($dlarr). '">Generate PDF</a>';
		
		$this->closeBlock();
	}
	
	
	/**
	 * Jukezora popup.
	 *
	 * @author Ben Dodson
	 * @since 4/29/05
	 * @version 4/29/05
	 **/
     function displayJukezora() {
	   global $include_path;
	   include_once($include_path.'jukezora.php');
     }
	 
	 
	/**
	* Sends a transcoded file bundle
	*
	* @author Ross Carlson
	* @since 06/10/05
	* @version 06/10/05
	*
	**/
	function sendTranscodedBundle($node){
		global $include_path;
		
		// Now let's include the libraries
		include_once($include_path. 'lib/jzcomp.lib.php');
		
		// Now we have an array of files let's use them to create the download
		sendFileBundle(unserialize($_GET['jz_files']), $node->getName());
		
		exit();
	}
	 
	/**
	* Displays the page to allow the user to select the type of transcoded download they want
	*
	* @author Ross Carlson
	* @since 06/10/05
	* @version 06/10/05
	* @param $node object The node we are viewing
	*
	**/
	function displayDownloadTranscode($node){
		global 	$cache_resampled_tracks, $jzSERVICES, $resample_cache_size, 
				$status_blocks_refresh, $allow_resample, $root_dir, $include_path;
		
		// Let's not time out...
		set_time_limit(0);
			
		// Can they resample??
		if ($allow_resample == "false"){
			echo '<body onLoad="document.dlForm.submit();"></body>';
		}		
		
		$this->displayPageTop("",word("Downloading"));
		$this->openBlock();
		
		// Let's get the meta data from the track or first track in this album
		// First we need to create a track object from the node
		if ($node->getPType() == "track"){
			$track = new jzMediaTrack($node->getPath('String'));
		} else {
			// Now we need to grab the first track as a sample
			$tracks = $node->getSubNodes("tracks",-1);
			$track = $tracks[0];
		}
		// Now let's pull the meta data
		$meta = $track->getMeta();
		
		// Ok, now based on the input file let's create the beginning of the command
		$extArr = explode(".",$node->getPath('String'));
		$ext = $extArr[count($extArr)-1];
		
		// Did they submit the form?
		if (isset($_POST['edit_dlformat'])){			
			// Ok, first we need to get ALL the files in the cache dir
			// And see how big it all is to see if we need to do some cleanup first
			$retArray = readDirInfo($include_path. "data/resampled","file");
			$size=0;
			foreach($retArray as $track){
				// Let's get the total size first
				$size = $size + round(filesize($include_path. "data/resampled/". $track)/1024000);
				flushdisplay();
			}
			// Now are we too big?
			if ($size > $resample_cache_size){
				// Ok, we'll have to loop through and delete until we get small enough
				foreach($retArray as $track){
					$size = $size - round(filesize($include_path. "data/resampled/". $track)/1024000);
					@unlink($include_path. "data/resampled/". $track);
					flushdisplay();
					if ($size < $resample_cache_size){
						break;
					}
				}
			}
			
			echo word("Beginning download"). "...<br><br>";
			flushdisplay();
			
			// First we need to know if this was a single file or an album
			if ($node->getPType() <> "track"){
				// Ok, we need to create ALL the tracks for this album
				// Let's setup our display
				echo '<div id="status"></div>';
				echo '<br><br>';
				echo '<div id="status2"></div>';
				?>
				<SCRIPT LANGUAGE=JAVASCRIPT><!--\
					s = document.getElementById("status");
					s2 = document.getElementById("status2");
					s2.innerHTML = '<?php echo '<center><img src="'. $root_dir. '/style/images/progress-bar.gif" border="0"><br>'. word("Please Wait"). '</center>'; ?>';					
					-->
				</SCRIPT>
				<?php
				flushdisplay();
				
				// Now, are they transcoding or are they downloading the native version
				if ($_POST['edit_dlformat'] <> "native" or $_POST['edit_dlbitrate'] <> "native"){

					// Now let's get all the tracks
					$tracks = $node->getSubNodes("tracks",-1);
					$fileArray = array();
					for($i=0;$i<count($tracks);$i++){
						?>
						<SCRIPT LANGUAGE=JAVASCRIPT><!--\
							s.innerHTML = '<nobr><?php echo word("Transcoding"). ": ". $tracks[$i]->getName(); ?></nobr>';					
							-->
						</SCRIPT>
						<?php
						flushdisplay();
						
						// Now let's set the format
						$dl_format = $_POST['edit_dlformat'];
						if ($dl_format == "native"){
							$dl_format = substr($tracks[$i]->getDataPath("string"),-3);
						}
						
						// Now let's transcode this track for them
						$meta = $tracks[$i]->getMeta();
						$fileArray[] = $jzSERVICES->createResampledTrack($tracks[$i]->getDataPath("string"),$dl_format, $_POST['edit_dlbitrate'], $meta);
						unset($meta);
					}
					// Now let's clean up
					?>
					<SCRIPT LANGUAGE=JAVASCRIPT><!--\
						s.innerHTML = '&nbsp;';			
						s2.innerHTML = '&nbsp;';					
						-->
					</SCRIPT>
					<?php
					flushdisplay();
					
					// Now we need to put the refreshing back to normal
					$url = array();
					$url['action'] = "nowstreaming";
					$url['refresh_int'] = $status_blocks_refresh;
					
					// Now we need to send this bundled file
					$dlarr = array();
					$dlarr['action'] = "popup";
					$dlarr['ptype'] = "downloadtranscodedbundle";
					$dlarr['jz_files'] = serialize($fileArray);
					$dlarr['jz_path'] = $node->getPath("string");
					
					$var  = word("If your download doesn't begin click"). " ";
					$var .= '<a href="'. urlize($dlarr). '">'.word('here').'.</a>';
					echo '<META HTTP-EQUIV=Refresh CONTENT="1; URL='. urlize($dlarr). '">';
					
					?>
					<SCRIPT LANGUAGE=JAVASCRIPT><!--\
						s.innerHTML = '<?php echo $var; ?>';			
						s2.innerHTML = '&nbsp;';					
						-->
					</SCRIPT>
					<?php
					flushdisplay();
					
					echo "<br><br><br><br><center>";
					$this->closeButton();		
					$this->closeBlock();		
				} else {

					// Ok, they want the native file format
					$tracks = $node->getSubNodes("tracks",-1);
					$fileArray = array();
					for($i=0;$i<count($tracks);$i++){
						$fileArray[] = $tracks[$i]->getDataPath("string");
					}
					
					// Now we need to send this bundled file
					$dlarr = array();
					$dlarr['action'] = "popup";
					$dlarr['ptype'] = "downloadtranscodedbundle";
					$dlarr['jz_files'] = serialize($fileArray);
					$dlarr['jz_path'] = $node->getPath("string");
					
					$var  = word("If your download does not begin click"). " ";
					$var .= '<a href="'. urlize($dlarr). '">HERE</a>';
					echo '<META HTTP-EQUIV=Refresh CONTENT="1; URL='. urlize($dlarr). '">';
					?>
					<SCRIPT LANGUAGE=JAVASCRIPT><!--\
						s.innerHTML = '<?php echo $var; ?>';			
						s2.innerHTML = '&nbsp;';					
						-->
					</SCRIPT>
					<?php
					flushdisplay();
					echo "<center>";
					$this->closeButton();		
					$this->closeBlock();		
				}
				
				exit();
			} else {
				// Let's create the resampled track IF we need to
				if ((($_POST['edit_dlformat'] <> $ext) or ($_POST['edit_dlbitrate'] <> $meta['bitrate'])) and $_POST['edit_dlformat'] <> "native"){
					echo word("Transcoding tracks, please stand by"). "...<br><br><br>";
					flushdisplay();
					$filename = $jzSERVICES->createResampledTrack($_POST['edit_dl_filename'],$_POST['edit_dlformat'], $_POST['edit_dlbitrate'], $meta);
				} else {	
					// Ok, use the standard filename
					$filename = $node->getDataPath('String');
				}
			}

			// Ok, now we need to send them the file
			$fileArray[] = $filename;
			$dlarr = array();
			$dlarr['action'] = "popup";
			$dlarr['ptype'] = "downloadtranscodedbundle";
			$dlarr['jz_files'] = serialize($fileArray);
			$dlarr['jz_path'] = $filename;	
			
			echo word("If your download doesn't begin click"). " ";
			echo '<a href="'. urlize($dlarr). '">HERE</a>';
			echo '<META HTTP-EQUIV=Refresh CONTENT="1; URL='. urlize($dlarr). '">';
			
			echo "<br><br><br><br><center>";
			$this->closeButton();		
			$this->closeBlock();		
			
			exit();
		}
		
		// Now we need to set the refresh time on the "Now Streaming" and "Who is where" blocks VERY
		// high - if they refresh while transcoding it'll kill the transcoding
		// Let's setup the link for the ifram
		$url = array();
		$url['action'] = "nowstreaming";
		$url['refresh_int'] = "0";
		?>
		<script type="text/javascript">
		<!--
		window.opener.document.getElementById("NowStreamingFrame").src = "<?php echo urlize($url); ?>";
		window.opener.document.getElementById("WhoIsWhereFrame").src = "<?php echo urlize($url); ?>";
		-->
		</script>
		<?php
		
		// Let's setup the form for the user to choose from
		$arr = array();
		$arr['action'] = "popup";
		$arr['ptype'] = "downloadtranscode";
		$arr['jz_path'] = $node->getPath('String');
		?>
		<form action="<?php echo urlize($arr); ?>" method="POST" name="dlForm">
			<input type="hidden" name="edit_dl_filename" value="<?php echo $node->getDataPath('String'); ?>">
			<?php
				// Can they resample?
				if ($allow_resample == "true"){
					?>
					<strong>
					<?php 
						echo word("Original File"); 
						if ($node->getPType() <> "track"){
							echo " (". word("Sample"). ")"; 
						}
					?>
					</strong>
					<table width="100%" cellpadding="2">
						<tr>
							<td width="25%" nowrap>
								<?php echo word("Format"); ?>:
							</td>
							<td width="75%" nowrap>
								<?php echo ucwords($meta['type']); ?>
							</td>
						</tr>
						<tr>
							<td width="25%" nowrap>
								<?php echo word("Bitrate"); ?>:
							</td>
							<td width="75%" nowrap>
								<?php echo $meta['bitrate']; ?> Kbps
							</td>
						</tr>
						<tr>
							<td width="25%" nowrap>
								<?php echo word("Frequency"); ?>:
							</td>
							<td width="75%" nowrap>
								<?php echo $meta['frequency']; ?> Khz
							</td>
						</tr>
						<tr>
							<td width="25%" nowrap>
								<?php echo word("Size"); ?>:
							</td>
							<td width="75%" nowrap>
								<?php echo $meta['size']; ?> MB
							</td>
						</tr>
					</table>
					<br>
					<strong><?php echo word("Download Format"); ?></strong>
					<table width="100%" cellpadding="2">
						<tr>
							<td width="25%" nowrap>
								<?php echo word("Format"); ?>:
							</td>
							<td width="75%" nowrap>
								<select name="edit_dlformat" class="jz_select" style="width: 100px;">
									<option value="native">Native</option>
									<option value="mp3">MP3</option>
									<option value="wav">WAV</option>
									<option value="flac">Flac</option>
									<option value="mpc">Musepack</option>
									<option value="wv">Wavpack</option>
									<!--<option value="ogg">OGG</option>-->
								</select>	
							</td>
						</tr>
						<tr>
							<td width="25%" nowrap>
								<?php echo word("Quality"); ?>:
							</td>
							<td width="75%" nowrap>
								<select name="edit_dlbitrate" class="jz_select" style="width: 100px;">
									<option selected value="native">Native</option>
									<option value="32"><?php echo word("Spoken word"); ?> (32kbps)</option>
									<option value="64"><?php echo word("Low quality"); ?> (64kbps)</option>
									<option value="96"><?php echo word("Medium quality"); ?> (96kbps)</option>
									<option value="128"><?php echo word("Low quality"); ?> (128kbps)</option>
									<option value="192"><?php echo word("Good quality"); ?> (192kbps)</option>
									<option value="320"><?php echo word("Highest quality"); ?> (320kbps)</option>
								</select>
							</td>
						</tr>
					</table>
					<br><br>
					<input type="submit" name="edit_download_tc_file" value="<?php echo word("Download"); ?>" class="jz_submit">
					<?php
				} else {
					echo '<input type="hidden" name="edit_dlformat" value="native">';
					echo '<input type="hidden" name="edit_dlbitrate" value="native">';
					echo '<body onLoad="document.dlForm.submit();"></body>';
				}
			?>			
		</form>
		<?php
		
		$this->closeBlock();		
		exit();
	}


	/**
	* Pulls the tag data from all tracks to import into the backend
	*
	* @author Ross Carlson
	* @since 04/12/05
	* @version 04/12/05
	* @param $node object The node we are viewing
	*
	**/
	function displayTagDataImporter($node){
	
		$this->displayPageTop("",word("Reading All Tag Data"));
		$this->openBlock();
		
		echo word('Searching, please wait...'). "<br><br>";
		echo '<div id="status"></div>';
		?>
		<SCRIPT LANGUAGE=JAVASCRIPT><!--\
			s = document.getElementById("status");
			-->
		</SCRIPT>
		<?php
		flushdisplay();
		
		$ctr=0;
		$tracks = $node->getSubNodes("tracks",-1);
		foreach($tracks as $track){
			// let's pull the meta data so it gets updated
			$track->getMeta();
			$ctr++;
			if ($ctr % 10 == 0){ 
				?>
				<SCRIPT LANGUAGE=JAVASCRIPT><!--\
					s.innerHTML = '<nobr><?php echo word("Analyzed"); ?>: <?php echo $ctr; ?></nobr>';					
					-->
				</SCRIPT>
				<?php
				flushdisplay();
			}
		}
		echo "<br><br><center>";
		$this->closeButton();		
		$this->closeBlock();		
	}


  function viewCurrentlyPlaying($mysid) {
    global $status_blocks_refresh;
    $this->displayPageTop("",word("Current Information"));
    $this->openBlock();
    echo '<span id="currentInfo">&nbsp;</span>';
    ?>
		<script>function updateCurrentInfo(update) {
			currentInfo("<?php echo $mysid; ?>", update);
			setTimeout("updateCurrentInfo(true)",<?php echo ($status_blocks_refresh * 1000); ?>);
		}
		updateCurrentInfo(false);
    </script>
    <?php
    //echo '<br><br><center>';
    //$this->closeButton();
      
    $this->closeBlock();
    
  }

	/**
	* Pulls the lyrics from a track and displays just them
	*
	* @author Ross Carlson
	* @since 04/08/05
	* @version 04/08/05
	* @param $node object The node we are viewing
	*
	**/
	function displayViewLyricsForTrack($node){
		$track = new jzMediaTrack($node->getPath('String'));		
		$meta = $track->getMeta();
	
		$this->displayPageTop("",word("Lyrics for:"). " ". $meta['title']);
		$this->openBlock();
		
		echo nl2br($meta['lyrics']);
		
		echo '<br><br><center>';
		$this->closeButton();
		
		$this->closeBlock();
	}
	
	/**
	* Goes through each subnode, one by one, and resizes the art
	*
	* @author Ross Carlson
	* @since 04/05/05
	* @version 04/05/05
	* @param $node object The node we are viewing
	*
	**/
	function displayArtResize($node){
		
		$this->displayPageTop("",word("Resize all album art"));
		$this->openBlock();
		
		// Did they submit?
		if (isset($_POST['edit_resize_art'])){
			// Let's set the start time
			$start = time();
			
			echo word("Resizing, please wait...");
			echo "<br><br>";
			echo '<div id="artist"></div>';
			echo '<div id="album"></div>';
			echo '<div id="total"></div>';
			// Ok, now we need to recurisvely get ALL subnodes
			$i=0;
			$nodes = $node->getSubNodes("nodes", -1);
			?>
			<SCRIPT LANGUAGE=JAVASCRIPT><!--\
				ar = document.getElementById("artist");
				a = document.getElementById("album");
				t = document.getElementById("total");
				-->
			</SCRIPT>
			<?php
			foreach($nodes as $node){
				if ($node->getName() <> "" and $node->getPtype() == "album"){
					$parent = $node->getParent();
					?>
					<SCRIPT LANGUAGE=JAVASCRIPT><!--\
						ar.innerHTML = '<nobr><?php echo word("Artist"); ?>: <?php echo $parent->getName(); ?></nobr>';					
						a.innerHTML = '<nobr><?php echo word("Album"); ?>: <?php echo $node->getName(); ?></nobr>';					
						t.innerHTML = '<nobr><?php echo word("Analyzed"); ?>: <?php echo $i; ?></nobr>';					
						-->
					</SCRIPT>
					<?php
					flushdisplay();
					// Now let's look at the art for this item and resize it if needed
					// BUT we don't want to create blank ones with this tool...
					$node->getMainArt($_POST['edit_resize_dim'], false);
					$i++;
				}
			}
			?>
			<SCRIPT LANGUAGE=JAVASCRIPT><!--\
				ar = document.getElementById("artist");
				a = document.getElementById("album");
				ar.innerHTML = '<nobr><?php echo word("Completed in"). " ". convertSecMins((time() - $start)). " ". word("seconds"); ?></nobr>';					
				a.innerHTML = '<nobr><?php echo word("Analyzed"); ?>: <?php echo $i; ?></nobr>';							
				t.innerHTML = '&nbsp;';							
				-->
			</SCRIPT>
			<?php
			flushdisplay();
			echo "<br><br><center>";
			$this->closeButton();
			exit();
		}
		
		// Let's setup our form
		$arr = array();
		$arr['action'] = "popup";
		$arr['ptype'] = "resizeart";
		$arr['jz_path'] = $node->getPath('String');
		echo '<form action="'. urlize($arr). '" method="POST">';
		?>
		<?php echo word("This tool will resize all your art to the specified dimensions below.  This will not delete or remove your existing art.  This will precreate the art for tools like the random albums so that it will run faster."); ?>
		<br><br>
		<?php echo word("100x100 is used by the random album block<br>Other common values are 75x75 and 150x150"); ?>
		<br>
		<br>
		<?php echo word("Dimensions (WidthxHeight)"); ?><br><input type="text" class="jz_input" name="edit_resize_dim" value="100x100">
		<br><br>
		<input type="submit" class="jz_submit" value="<?php echo word("Resize All Art"); ?>" name="edit_resize_art">
		<?php
		$this->closeButton();
		echo '</form>';
		
		
		$this->closeBlock();
	}
	
	/**
	* Runs through all nodes and automatically sets the page type on them
	* This goes from the bottom and recursive up...
	*
	* @author Ross Carlson
	* @since 04/01/05
	* @version 04/01/05
	*
	**/
	function displayAutoPageType($node){
	  global $jzUSER;

	  if (!checkPermission($jzUSER,"admin",$node->getPath("String"))) {
	    echo word("Insufficient permissions.");
	    return;
	  }



		$this->displayPageTop("",word("Auto setting page types"));
		$this->openBlock();
		
		// Now let's setup our display elements
		echo word("Analysing..."). '<br><br>';		
		echo '<div id="artist"></div>';		
		echo '<div id="album"></div>';		
		?>
		<SCRIPT LANGUAGE=JAVASCRIPT><!--\
			ar = document.getElementById("artist");
			a = document.getElementById("album");
			-->
		</SCRIPT>
		<?php
		flushdisplay();
		
		$nodes = $node->getSubNodes("nodes", -1);
		foreach($nodes as $node){
			// If there are NO subnodes let's assume that it's an album
			$snodes = $node->getSubNodes("nodes");
			if (count($snodes) == 0){
				// Now let's get it's parent, it must be an artist
				$parent = $node->getParent();
				$parent->setPType('artist');
				?>
				<SCRIPT LANGUAGE=JAVASCRIPT><!--\
					ar.innerHTML = '<?php echo word("Artist"); ?>: <?php echo $parent->getName(); ?>';					
					-->
				</SCRIPT>
				<?php
				flushdisplay();				
				?>
				<SCRIPT LANGUAGE=JAVASCRIPT><!--\
					a.innerHTML = '<?php echo word("Album"); ?>: <?php echo $node->getName(); ?>';					
					-->
				</SCRIPT>
				<?php
				flushdisplay();
				$node->setPType('album');
			}
		}
		?>
		<SCRIPT LANGUAGE=JAVASCRIPT><!--\
			ar.innerHTML = '<?php echo word("Complete!"); ?>';					
			a.innerHTML = '&nbsp;';					
			-->
		</SCRIPT>
		<?php
		echo "<br><br><center>";
		$this->closeButton(false);
		$this->closeBlock();
		
	}
	
	
	/**
	* Pulls art from the ID3 tags and adds it to the backend
	*
	* @author Ross Carlson
	* @since 04/01/05
	* @version 04/01/05
	*
	**/
	function displayArtFromTags($node){
	
		$this->displayPageTop("",word("Pull art from Tag Data"));
		$this->openBlock();
		
		// Now let's setup our display elements
		echo word("Searching..."). '<br><br>';		
		echo '<div id="current"></div>';		
		?>
		<SCRIPT LANGUAGE=JAVASCRIPT><!--\
			c = document.getElementById("current");
			-->
		</SCRIPT>
		<?php
		
		// Ok, let's get ALL the tracks and look at each node and see if we can get art for it
		// and see if we can get art for them
		$nodes = $node->getSubNodes("nodes", -1);
		
		foreach($nodes as $node){
			// Ok, let's see if we can get art for this node
			if ($node->getMainArt() <> ""){
				// Now let's add art for this node
				$node->addMainArt($node->getMainArt());
				?>
				<SCRIPT LANGUAGE=JAVASCRIPT><!--\
					c.innerHTML = '<?php echo word("Art found for"); ?>: <?php echo $node->getName(); ?>';					
					-->
				</SCRIPT>
				<?php
				flushdisplay();
			}
		}
		?>
		<SCRIPT LANGUAGE=JAVASCRIPT><!--\
			c.innerHTML = '<?php echo word("Complete!"); ?>';					
			-->
		</SCRIPT>
		<?php
		echo "<br><br><center>";
		$this->closeButton(true);
		$this->closeBlock();
	}
	
	/**
	* Displays the request manager
	*
	* @author Ross Carlson
	* @since 03/19/05
	* @version 03/19/05
	*
	**/
	function displayRequestManager($node){
		global $jzUSER;
		
		$this->displayPageTop("",word("Request Manager"));
		$this->openBlock();
		
		// Now let's see if they wanted to add
		if (isset($_POST['edit_add'])){
			$node->addRequest($_POST['edit_request'], '', $jzUSER->getName());
		}
		// Did they want to delete
		if (isset($_POST['edit_delete'])){
			$node->removeRequest($_POST['edit_previous_requests']);
		}
		
		// Let's setup our form
		$arr = array();
		$arr['action'] = "popup";
		$arr['ptype'] = "requestmanager";
		$arr['jz_path'] = $node->getPath('String');
		echo '<form action="'. urlize($arr). '" method="POST">';
		
		?>
		<?php echo word('Enter your request below'); ?>:<br>
		<input type="text" name="edit_request" class="jz_input" size="30">
		<input type="submit" name="edit_add" class="jz_submit" value="<?php echo word('Go'); ?>">
		<br>
		<br>
		<br>
		<?php echo word('Current Requests'); ?>:<br>
		<select class="jz_select" name="edit_previous_requests" size="10" style="width:200px;">
			<?php
				$req = $node->getRequests(-1, "all");
		                rsort($req);
				for($i=0;$i<count($req);$i++){
					echo '<option value="'. $req[$i]['id']. '">'. $req[$i]['entry']. '</option>';
				}
			?>
		</select>
		<br><br>
		    <?php
			if ($jzUSER->getSetting('admin')){
			?>
			<input type="submit" name="edit_delete" class="jz_submit" value="<?php echo word('Delete'); ?>">
			<!--<input type="submit" name="edit_notify" class="jz_submit" value="<?php echo word("Notify requestor"); ?>">-->
			<?php
		}
		
		echo "</form>";
		
		$this->closeBlock();
	}
	
	/**
	* Displays the discussion page
	*
	* @author Ross Carlson
	* @since 03/07/05
	* @version 03/07/05
	* @param $node The node we are looking at
	*
	**/
	function displayDiscussion($node){
		global $jzUSER, $row_colors;
		
		// Let's setup the object		
		$item = new jzMediaElement($node->getPath('String'));		
		$track = new jzMediaTrack($node->getPath('String'));		
		
		// Let's grab the meta data from the file and display it's name
		$meta = $track->getMeta();
		
		$this->displayPageTop("","Discuss Item: ". $meta['title']);
		$this->openBlock();
		
		// Did they submit the form?
		if (isset($_POST['edit_addcomment'])){
			// Let's add it
			$item->addDiscussion($_POST['edit_newcomment'],$jzUSER->getName());
		}
		
		// Let's setup our form
		$arr = array();
		$arr['action'] = "popup";
		$arr['ptype'] = "discussitem";
		$arr['jz_path'] = $node->getPath('String');
		echo '<form action="'. urlize($arr). '" method="POST">';
		
		// Now let's setup the display
		$i=0;
		?>
		<table width="100%" cellpadding="5" cellspacing="0" border="0">
			<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
				<td width="20%" valign="top">
					<nobr>
						<?php echo word('New Comment'); ?>:
					</nobr>
				</td>
				<td width="80%" valign="top">
					<textarea name="edit_newcomment" rows="3" style="width:300px;" class="jz_input"></textarea>
					<br><br>
					<input type="submit" name="edit_addcomment" value="<?php echo word('Add Comment'); ?>" class="jz_submit">
					<br><br>
				</td>
			</tr>
			<tr class="<?php echo $row_colors[0];?>">
				<td colspan="2" width="100%" align="center">
					<strong><?php echo word('Previous Comments'); ?></strong><br><br>
				</td>
			</tr>
			<?php
				// Now let's get the previous discussions
				$disc = $item->getDiscussion();
				if (count($disc) > 0){
					rsort($disc);
					foreach($disc as $comment){
						?>
						<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
							<td width="20%" valign="top">
								<nobr>
									<?php echo $comment['user']; ?>
								</nobr>
							</td>
							<td width="80%" valign="top">
								<?php echo $comment['comment']; ?>
							</td>
						</tr>
						<?php
					}
				}
			?>
		</table>
		</form>
		<?php
		
		
		$this->closeBlock();
	}
	
	/**
	* This tools displays a page of art so that the user can choose which one they want
	*
	* @author Ross Carlson
	* @since 03/07/05
	* @version 03/07/05
	* @param $node The node we are looking at
	*
	**/
	function displayGetAlbumArt($node){
		global $allow_filesystem_modify, $backend, $include_path;
		
		// Now let's see if they choose an image
		$i=0;
		while($i<5){
			if (isset($_POST['edit_download_'. $i])){
				// Ok, we got it, now we need to write this out
				$image = $_POST['edit_image_'. $i];
				$imageData = file_get_contents($image);
				
				// now let's set the path for the image
				if (stristr($backend,"id3") or $allow_filesystem_modify == "false"){
					$imgFile = $include_path. "data/images/". str_replace("/","--",$node->getPath("String")). "--". $node->getName(). ".jpg";
				} else {
					$imgFile = $node->getDataPath(). "/". $node->getName(). ".jpg";
				}
				
				// Now let's delete it if it already exists
				if (is_file($imgFile)){ unlink($imgFile); }
				// Now we need to see if any resized versions of it exist
				$retArray = readDirInfo($include_path. "data/images","file");
				foreach($retArray as $file){
					if (stristr($file,str_replace("/","--",$node->getPath("String")). "--". $node->getName())){	
						// Ok, let's wack it
						@unlink($include_path. "data/images/".$file);
					}
				}

				// Now let's get the data and add it to the node
				$handle = fopen($imgFile, "w");
				if (fwrite($handle,$imageData)){
					// Ok, let's write it to the backend
					$node->addMainArt($imgFile);
				}
				fclose ($handle);
				
				// now let's close out
				$this->closeWindow(true);
				exit();
			}
			$i++;
		}
	
		// Let's resize
		?>
		<SCRIPT LANGUAGE=JAVASCRIPT TYPE="TEXT/JAVASCRIPT"><!--\
			window.resizeTo(500,700)
		-->
		</SCRIPT>
		<?php	
		flushdisplay();
		
		$display = new jzDisplay();
	
		$this->displayPageTop("","Searching for art for: ". $node->getName());
		$this->openBlock();
		
		echo word('Searching, please wait...'). "<br><br>";
		flushdisplay();
		
		// Now let's display what we got
		$i=0;
		echo "<center>";
		// Let's setup our form
		$arr = array();
		$arr['action'] = "popup";
		$arr['ptype'] = "getalbumart";
		$arr['jz_path'] = $node->getPath('String');
		echo '<form action="'. urlize($arr). '" method="POST">';
		
		$i=0;
		// Ok, now let's setup a service to get the art for each of the providers
		// Now let's get a link from Amazon
		$jzService = new jzServices();		
		$jzService->loadService("metadata", "amazon");
		$image = $jzService->getAlbumMetadata($node, false, "image");
		if (strlen($image) <> 0){
			echo '<img src="'. $image. '" border="0"><br>';
			echo $display->returnImageDimensions($image);
			echo '<br><br>';
			echo '<input type="hidden" value="'. $image. '" name="edit_image_'. $i. '">';
			echo '<input type="submit" name="edit_download_'. $i. '" value="'. word('Download'). '" class="jz_submit"><br><br><br>';
			$i++;
		}
		flushdisplay();
		
		// Now let's get a link from Rollingstone
		unset($jzService);unset($image);
		$jzService = new jzServices();		
		$jzService->loadService("metadata", "google");
		$image = $jzService->getAlbumMetadata($node, false, "image");
		if (strlen($image) <> 0){
			echo '<img src="'. $image. '" border="0"><br>';
			echo $display->returnImageDimensions($image);
			echo '<br><br>';
			echo '<input type="hidden" value="'. $image. '" name="edit_image_'. $i. '">';
			echo '<input type="submit" name="edit_download_'. $i. '" value="'. word('Download'). '" class="jz_submit"><br><br><br>';
			$i++;
		}
		flushdisplay();
		
		// Now let's get a link from Rollingstone
		unset($jzService);unset($image);
		$jzService = new jzServices();		
		$jzService->loadService("metadata", "rs");
		$image = $jzService->getAlbumMetadata($node, false, "image");
		if (strlen($image) <> 0){
			echo '<img src="'. $image. '" border="0"><br>';
			echo $display->returnImageDimensions($image);
			echo '<br><br>';
			echo '<input type="hidden" value="'. $image. '" name="edit_image_'. $i. '">';
			echo '<input type="submit" name="edit_download_'. $i. '" value="'. word('Download'). '" class="jz_submit"><br><br><br>';
			$i++;
		}
		flushdisplay();
		
		// Now let's get a link from Rollingstone
		unset($jzService);unset($image);
		$jzService = new jzServices();		
		$jzService->loadService("metadata", "msnmusic");
		$image = $jzService->getAlbumMetadata($node, false, "image");
		if (strlen($image) <> 0){
			echo '<img src="'. $image. '" border="0"><br>';
			echo $display->returnImageDimensions($image);
			echo '<br><br>';
			echo '<input type="hidden" value="'. $image. '" name="edit_image_'. $i. '">';
			echo '<input type="submit" name="edit_download_'. $i. '" value="'. word('Download'). '" class="jz_submit"><br><br><br>';
			$i++;
		}
		flushdisplay();
		
		// Now let's get a link from Musicbrainz
		unset($jzService);unset($image);
		$jzService = new jzServices();		
		$jzService->loadService("metadata", "musicbrainz");
		$image = $jzService->getAlbumMetadata($node, false, "image");
		if (strlen($image) <> 0){
			echo '<img src="'. $image. '" border="0"><br>';
			echo $display->returnImageDimensions($image);
			echo '<br><br>';
			echo '<input type="hidden" value="'. $image. '" name="edit_image_'. $i. '">';
			echo '<input type="submit" name="edit_download_'. $i. '" value="'. word('Download'). '" class="jz_submit"><br><br><br>';
			$i++;
		}
		flushdisplay();
		echo "<br>";
		$this->closeButton();
		echo "</form></center>";		
		
		$this->closeBlock();
	
	}
	
	/**
	* This tools lets us automatically renumber the tracks for an album
	*
	* @author Ross Carlson
	* @since 03/07/05
	* @version 03/07/05
	* @param $node The node we are looking at
	*
	**/
	function displayRenumber($node){
		global $allow_filesystem_modify,$jzSERVICES;

		$this->displayPageTop("",word("Renumbering tracks for"). ": ". $node->getName());
		$this->openBlock();

		if (!isset($_GET['renumber_type'])) {
		  $arr = array();
		  $arr['action'] = "popup";
		  $arr['ptype'] = "autorenumber";
		  $arr['jz_path'] = $_GET['jz_path'];

		  echo '<table><tr><td>';
		  $arr['renumber_type'] = "mb";
		  echo '<a href="'.urlize($arr).'">'.word("From Musicbrainz").'</a>';
		  echo '</td></tr><tr><td>';
		  $arr['renumber_type'] = "fn";
		  echo '<a href="'.urlize($arr).'">'.word("From filenames").'</a>';
		  echo '</td></tr></table>';
		}
		if ($_GET['renumber_type'] == "fn") {
		  $albums = array();
		  if (sizeof($albums = $node->getSubNodes("nodes")) == 0) {
		    $albums[] = $node;
		  }
		  foreach ($albums as $album) {
		    $tracks = $album->getSubNodes("tracks");
		    sortElements($tracks,"filename");
		    for ($i = 0; $i < sizeof($tracks); $i++) {
		      $meta = $tracks[$i]->getMeta();
		      $meta['number'] = $i+1;
		      $tracks[$i]->setMeta($meta);
		    }
		  }
		  echo 'Done!';
		  echo '<br><br><center>';
		  $this->closeButton(true);
		  exit();
		}
		else if ($_GET['renumber_type'] == "mb") {
		  $jzSERVICES->loadService("metadata", "musicbrainz");
		  // Did they submit the form?
		if (isset($_POST['edit_renumber'])){			
			echo word('Renumbing tracks, please stand by...'). "<br><br>";
			echo '<div id="status"></div>';
			echo '<div id="oldname"></div>';
			echo '<div id="newname"></div>';
			?>
			<SCRIPT LANGUAGE=JAVASCRIPT><!--\
				o = document.getElementById("oldname");
				n = document.getElementById("newname");
				s = document.getElementById("status");
				s.innerHTML = '<nobr><?php echo word("Status: Getting track information"); ?>...</nobr>';
				-->
			</SCRIPT>
			<?php
			flushdisplay();
			
			// Now let's get the tracks
			$tracks = $jzSERVICES->getAlbumMetadata($node, false, "tracks");
			$aTracks = $node->getSubNodes("tracks");
			$c=1;
			for($i=0;$i<count($tracks);$i++){
				if ($tracks[$i] <> ""){
					// Ok, let's see if we can match this to one of the tracks we have
					foreach($aTracks as $track){
						if (stristr($tracks[$i],$track->getName()) or stristr($track->getName(),$tracks[$i])){
							// Ok, let's make the number 2 chars
							if ($c < 10){ $num = "0". $c; } else { $num = $c; }
							// Now we need to get the meta on this track
							$meta = $track->getMeta();
							// Now let's set the track number
							$meta['number'] = $num;
							// Now let's write that to the meta on the file
							$track->setMeta($meta);
							?>
							<SCRIPT LANGUAGE=JAVASCRIPT><!--\
								o.innerHTML = '<nobr><?php echo word("Old Name"); ?>: <?php echo $track->getName(); ?></nobr>';
								n.innerHTML = '<nobr><?php echo word("New Name"); ?>: <?php echo $num. " - ". $tracks[$i]; ?></nobr>';
								s.innerHTML = '<nobr><?php echo word("Status: Renumbering"); ?></nobr>';
								-->
							</SCRIPT>
							<?php
							flushdisplay();
							sleep(1);
							// Now do they want to update the filename?
							if ($allow_filesystem_modify == "true"){
								$oldFile = $track->getDataPath();
								$tArr = explode("/",$track->getDataPath());
								$file = $tArr[count($tArr)-1];
								unset($tArr[count($tArr)-1]);
								$newPath = implode("/",$tArr);
								$newFile = $newPath. "/". $num. " - ". $file;
								$success="Failed";
								if (@rename($oldFile,$newFile)){
									$success = "Success";
								}
								?>
								<SCRIPT LANGUAGE=JAVASCRIPT><!--\
									o.innerHTML = '<nobr><?php echo word("Old Name"); ?>: <?php echo $oldFile; ?></nobr>';
									n.innerHTML = '<nobr><?php echo word("New Name"); ?>: <?php echo $newFile; ?></nobr>';
									s.innerHTML = '<nobr><?php echo word("Status: Renaming"); ?> - <?php echo $success; ?></nobr>';
									-->
								</SCRIPT>
								<?php
								flushdisplay();
							}
						}
					}
					$c++;
				}
			}
			?>
			<SCRIPT LANGUAGE=JAVASCRIPT><!--\
				o.innerHTML = '&nbsp;';
				n.innerHTML = '&nbsp;';
				s.innerHTML = '<nobr><?php echo word("Complete!"); ?></nobr>';
				-->
			</SCRIPT>
			<?php
			flushdisplay();
			echo '<br><br><center>';
			$this->closeButton(true);
			exit();
		}
		
		$this->displayPageTop("",word("Searching for data for: "). $node->getName());
   		$this->openBlock();
		flushdisplay();
		
		// Now let's get the tracks
		$tracks = $jzSERVICES->getAlbumMetadata($node, false, "tracks");
		if (count($tracks) > 1){
			// Ok, we got tracks, let's try to match them up...
			$c=1;
			$aTracks = $node->getSubNodes("tracks");
			for($i=0;$i<count($tracks);$i++){
				if ($tracks[$i] <> ""){
					// Ok, let's see if we can match this to one of the tracks we have
					$found=false;
					foreach($aTracks as $track){
						if (stristr($tracks[$i],$track->getName()) or stristr($track->getName(),$tracks[$i])){
							echo '<font color="green"><nobr>'. $track->getName(). " --- ". $c. " - ". $tracks[$i]. "</nobr></font><br>";
							$found=true;
						}
					}
					if (!$found){
						echo '<font color="red">'. $c. " - ". $tracks[$i]. " ". word('not matches'). "</font><br>";
					}
					$c++;
				}
			}
			$arr = array();
			$arr['action'] = "popup";
			$arr['ptype'] = "autorenumber";
			$arr['jz_path'] = $_GET['jz_path'];
			$arr['renumber_type'] = "mb";
			echo '<form action="'. urlize($arr). '" method="POST">';
			echo "<br><br>";			
			echo '<input type="submit" name="edit_renumber" value="'. word('Renumber Tracks'). '" class="jz_submit"> &nbsp; ';
			$this->closeButton();
			echo "</form><br><br>";
		} else {
			echo word("Sorry, we didn't get good data back for this album...");
			echo '<br><br><center>';
			$this->closeButton();
		}
		}
	}
	
	/**
	* Displays a close window input button
	*
	* @author Ross Carlson
	* @since 03/07/05
	* @version 03/07/05
	* @param $reload Should we reload the parent on click (default to false)
	*
	**/
	function closeButton($reload = false){
		echo '<input type="submit" value="'. word('Close'). '" name="close" onClick="window.close();';
		if ($reload){
			echo 'opener.location.reload(true);';
		}
		echo '" class="jz_submit">';
	}
	
	
	/**
	* Let's us bulk edit an entire album
	*
	* @author Ross Carlson
	* @since 03/07/05
	* @version 03/07/05
	* @param $node The node we are looking at
	*
	**/
	function displayBulkEdit($node){
		global $row_colors, $allow_filesystem_modify, $clip_length, $clip_start, $root_dir, $lame_opts, $jzSERVICES;
		
		$this->displayPageTop("",word("Bulk edit"). ": ". $node->getName());
   		$this->openBlock();
		
		if ($allow_filesystem_modify == "false"){
			echo 'You do not allow your filesystem to be modified.<br><br>'.
				 'Please see <a target="_blank" href="http://www.jinzora.com/pages.php?pn=support&sub=faq#10">our FAQ</a> on this issue.';
			$this->closeBlock();
			exit();
		}
		
		// Now let's get 1 track to show as a sample
		$tracks = $node->getSubNodes("tracks",-1);
		
		// Did they bulk edit and do a replace?
		if (isset($_POST['edit_replace_close']) 
			or isset($_POST['edit_replace']) 
			or isset($_POST['edit_create_clips']) 
			or isset($_POST['edit_delete_clips']) 
			or isset($_POST['edit_create_lofi']) 
			or isset($_POST['edit_delete_lofi']) 
			or isset($_POST['edit_fix_case'])){
						
			
			if (isset($_POST['edit_create_lofi'])){	
				// Ok, let's give them status
				echo "<center>". word('Resampling files, please stand by...'). "<br><br>";
				echo '<img src="'. $root_dir. '/style/images/convert.gif?'. time(). '"></center><br>';
				echo '<div id="path"></div>';
				echo '<div id="oldname"></div>';
				echo '<div id="newname"></div>';
				echo '<div id="status"></div>';
				flushdisplay();
			} else {
				// Ok, let's give them status
				echo word("Modifying files, please stand by..."). "<br><br>";
				echo '<div id="path"></div>';
				echo '<div id="oldname"></div>';
				echo '<div id="newname"></div>';
				echo '<div id="status"></div>';
			}
			?>
			<SCRIPT LANGUAGE=JAVASCRIPT><!--\
				p = document.getElementById("path");
				o = document.getElementById("oldname");
				n = document.getElementById("newname");
				s = document.getElementById("status");
				-->
			</SCRIPT>
			<?php
			$updateNode=true;
			// First let's get the list of actual files
			for($i=0;$i<count($tracks);$i++){
				// Let's get all the data we'll need
				$oArr = explode("/",$tracks[$i]->getDataPath("String"));
				$oldName = $oArr[count($oArr)-1];
				unset($oArr[count($oArr)-1]);
				$path = implode("/",$oArr);
				$oldPath = $tracks[$i]->getDataPath("String");
				
				// Now let's set this based on the tool
				if (isset($_POST['edit_replace_close']) or isset($_POST['edit_replace'])){
					$newName = trim(str_replace($_POST['edit_file_search'],$_POST['edit_file_replace'],$oldName));
					$newPath = $path. "/". $newName;
					// Ok, let's copy then kill
					$error = word("Failed!");
					if (@rename($oldPath,$newPath)){
						$error = word("Success!");
					}
					$meta['title'] = $newName;
					$tracks[$i]->setMeta($meta);
					$updateNode=true;
				}
				// Fixing case?
				if (isset($_POST['edit_fix_case'])){
					$newName = ucwords($oldName);
					$newPath = $path. "/". $newName;
					// Ok, let's copy then kill
					$error = word("Failed!");
					if (copy($oldPath,$newPath.".tmp")){
						if (unlink($oldPath)){
							if (rename($newPath. ".tmp",$newPath)){
								$error = word("Success!");
							}
						}
					}
				}
				// Creating clips?
				if (isset($_POST['edit_create_clips'])){
					if (substr($oldName,-4) == ".mp3" and is_file($oldPath) and !stristr($oldName,".clip.")){
						$newName = substr($oldName,0,-3). 'clip.mp3';
						$newPath = $path. "/". $newName;
						// Now let's write out the new clip track
						$handle = fopen($newPath, "w");
						fwrite($handle,substr(file_get_contents($oldPath),($clip_start * 25000),($clip_length * 25000)));				
						fclose($handle);
						
						// Now let's write the meta to this track
						$tMeta = new jzMediaTrack($tracks[$i]->getPath("String"));
						$meta = $tMeta->getMeta();
						// Now let's write this
						$jzSERVICES->setTagData($newPath, $meta);
						
						$error = word("Success!");
					} else {
						$error = word("Failed - not an MP3 file!");
					}
				}
				// Deleting clips?
				if (isset($_POST['edit_delete_clips'])){
					// Ok, let's unlink .clip.mp3 tracks
					if (substr($oldPath,-9) == ".clip.mp3"){
						$error="Failed!";
						if (unlink($oldPath)){
							$newName = word("...deleted...");
							$error = word("Success!");
						}
					} else {
						$error = word("Skipping, not a clip track...");
					}
				}			
				// Creating a lofi resample?
				if (isset($_POST['edit_create_lofi'])){	
					if (!stristr($oldName,".mp3") or stristr($oldName,".clip.")){continue;}
					$error = "Lo-fi file create failed!";
					// Now let's encode
					$newName = substr($oldName,0,-3). 'lofi.mp3';
					$newPath = $path. "/". $newName;
					$command = $lame_opts. ' "'. $oldPath. '" "'. $newPath. '"';
					?>
					<SCRIPT LANGUAGE=JAVASCRIPT><!--\
						p.innerHTML = '<nobr><?php echo word("Path"); ?>: <?php echo $path; ?></nobr>';
						o.innerHTML = '<nobr><?php echo word("Old Name"); ?>: <?php echo $oldName; ?></nobr>';
						n.innerHTML = '<nobr><?php echo word("New Name"); ?>: <?php echo $newName; ?></nobr>';
						s.innerHTML = '<nobr><?php echo word("Status"); ?>: creating...</nobr>';
						-->
					</SCRIPT>
					<?php
					flushdisplay();
					$output = "";
					$returnvalue = "";
					if (exec($command, $output, $returnvalue)){
						$error = word("Lo-fi file created successfully!");
						// Now we need to get the meta data from the orginal file so we can write it to the new file
						$tMeta = new jzMediaTrack($tracks[$i]->getPath("String"));
						$meta = $tMeta->getMeta();
						// Now let's write this
						$jzSERVICES->setTagData($newPath, $meta);
					}
				}
				// Are we deleting clips?
				if (isset($_POST['edit_delete_lofi'])){	
					// Ok, let's unlink .clip.mp3 tracks
					if (substr($oldPath,-9) == ".lofi.mp3"){
						$error = word("Failed!");
						if (unlink($oldPath)){
							$newName = word("...deleted...");
							$error = word("Success!");
						}
					} else {
						$error = word("Skipping, not a lofi track...");
					}
				}
				
				// Now let's status
				?>
				<SCRIPT LANGUAGE=JAVASCRIPT><!--\
					p.innerHTML = '<nobr><?php echo word("Path"); ?>: <?php echo $path; ?></nobr>';
					o.innerHTML = '<nobr><?php echo word("Old Name"); ?>: <?php echo $oldName; ?></nobr>';
					n.innerHTML = '<nobr><?php echo word("New Name"); ?>: <?php echo $newName; ?></nobr>';
					s.innerHTML = '<nobr><?php echo word("Status"); ?>: <?php echo $error; ?></nobr>';
					-->
				</SCRIPT>
				<?php
				flushdisplay();
			}
			
			// Now we need to update the node			
			?>
			<SCRIPT LANGUAGE=JAVASCRIPT><!--\
				p.innerHTML = '<center><?php echo word("Updating the node cache..."); ?></center>';
				o.innerHTML = '&nbsp;';
				n.innerHTML = '&nbsp;';
				s.innerHTML = '&nbsp;';
				-->
			</SCRIPT>
			<?php
			flushdisplay();
			if ($updateNode){
				//$node->updateCache(true, false, false, true);
				updateNodeCache($node,true,false,true);
			}
			
			// Now we need to update the node			
			?>
			<SCRIPT LANGUAGE=JAVASCRIPT><!--\
				p.innerHTML = '<center><?php echo word("Complete!"); ?></center>';
				o.innerHTML = '&nbsp;';
				n.innerHTML = '&nbsp;';
				s.innerHTML = '&nbsp;';
				-->
			</SCRIPT>
			<?php
			flushdisplay();
			
			if (isset($_POST['edit_replace_close'])){
				$this->closeWindow(true);
			}
			echo '<center>';
			$this->closeButton(true);
			exit();
		}
		
		
		
		
		// This is the display for this tool
		
		// Let's give them the options to update
		$arr = array();
		$arr['action'] = "popup";
		$arr['ptype'] = "bulkedit";
		$arr['jz_path'] = $_GET['jz_path'];
		echo '<form action="'. urlize($arr). '" method="POST">';
		$i=0;
		?>
		<table width="100%" cellpadding="3" cellspacing="0">
			<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
				<td>
					<nobr>
					<?php echo word("Sample Track"); ?>:
					</nobr>
				</td>
				<td>
					<?php 
						$pArr = explode("/",$tracks[0]->getFilePath());
						echo $pArr[count($pArr)-1];
					?>
				</td>
			</tr>
			<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
				<td>
					<nobr>
					<?php echo word("String Replace"); ?>:
					</nobr>
				</td>
				<td>
					<?php echo word("Search"); ?><br>
					<input type="text" name="edit_file_search" size="30" class="jz_input"><br>
					<?php echo word("Replace"); ?><br>
					<input type="text" name="edit_file_replace" size="30" class="jz_input"><br><br>
					<input type="submit" name="edit_replace" value="<?php echo word("Replace"); ?>:" class="jz_submit">
					<input type="submit" name="edit_replace_close" value="<?php echo word("Replace & Close"); ?>:" class="jz_submit"><br><br>
					</form>
				</td>
			</tr>
			<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
				<td>

				</td>
				<td>
					<?php
						$tarr = array();
						$tarr['action'] = "popup";
						$tarr['ptype'] = "retagger";
						$tarr['jz_path'] = $node->getPath("String");
						echo '<form action="'. urlize($tarr). '" method="POST">';
					?>
					<input type="submit" name="edit_retag_tracks" value="<?php echo word("Retag All Tracks"); ?>" class="jz_submit">
					</form>
				</td>
			</tr>
			<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
				<td>

				</td>
				<td>
					<?php
						$arr = array();
						$arr['action'] = "popup";
						$arr['ptype'] = "autorenumber";
						$arr['jz_path'] = $_GET['jz_path'];
						echo '<form action="'. urlize($arr). '" method="POST">';
						echo '<input type="submit" name="edit_show_renumber" value="'. word('Renumber Tracks'). '" class="jz_submit">';
						echo "</form>";
					?>
				</td>
			</tr>
			<?php
				// Let's give them the options to update
				$arr = array();
				$arr['action'] = "popup";
				$arr['ptype'] = "bulkedit";
				$arr['jz_path'] = $_GET['jz_path'];
				echo '<form action="'. urlize($arr). '" method="POST">';
			?>
			<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
				<td>

				</td>
				<td>
					<input type="submit" name="edit_fix_case" value="<?php echo word("Fix Filename Case"); ?>" class="jz_submit">
				</td>
			</tr>
			<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
				<td>

				</td>
				<td>
					<input type="submit" name="edit_create_clips" value="<?php echo word("Create Clips"); ?>" class="jz_submit">
					<input type="submit" name="edit_delete_clips" value="<?php echo word("Delete Clips"); ?>" class="jz_submit">
				</td>
			</tr>
			<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
				<td>

				</td>
				<td>
					<input type="submit" name="edit_create_lofi" value="<?php echo word("Create LoFi Tracks"); ?>" class="jz_submit">
					<input type="submit" name="edit_delete_lofi" value="<?php echo word("Delete LoFi Tracks"); ?>" class="jz_submit">
				</td>
			</tr>
		</table>
		</form>
		<br><br><center>
		<?php
		$this->closeButton();
		$this->closeBlock();
	}
	
	/**
	* Sitewide settings editor
	*
	* @author Ben Dodson
	* @since 2/2/05
	* @version 2/2/05
	*
	**/
	function displayPopPlayer(){
		global $css, $jzSERVICES;
		
		// Let's setup the css for the page
		include_once($css);
		
		// Now let's open the service for this
		$jzSERVICES->loadService("players",$_GET['embed_player']);
		$jzSERVICES->displayPlayer();
	}



	/**
	 * Sitewide settings editor
	 *
	 * @author Ben Dodson
	 * @since 2/2/05
	 * @version 2/2/05
	 *
	 **/
	function displaySiteSettings() {
	  global $include_path,$jzUSER,$my_frontend;
	  
	  if ($jzUSER->getSetting('admin') !== true) {
	    exit();
	  }
	  
	  $display = new jzDisplay();
	  $page_array = array();
	  $page_array['action'] = 'popup';
	  $page_array['ptype'] = 'sitesettings';
	  if (isset($_GET['subpage'])) {
	    $page_array['subpage'] = $_GET['subpage'];
	  }
	  if (isset($_GET['subsubpage'])) {
	    $page_array['subsubpage'] = $_GET['subsubpage'];
	  }
	  if (isset($_GET['set_fe'])) {
	    $page_array['set_fe'] = $_GET['set_fe'];
	  }
	  if (isset($_POST['set_fe'])) {
	    $page_array['set_fe'] = $_POST['set_fe'];
	  }
	  
	  $this->displayPageTop("",word('Site Settings'));
	  $this->openBlock();
	  
	  // Index page:
	  if (!isset($_GET['subpage'])) {
	    echo "<table><tr><td>";
	    $page_array['subpage'] =  "main";
	    echo '<a href="'.urlize($page_array).'">'. word('Main Settings'). '</a>';
	    echo "</td></tr><tr><td>";
	    
	    $page_array['subpage'] =  "services";
	    echo '<tr><td><a href="'.urlize($page_array).'">'. word('Services'). '</a>';
	    echo "</td></tr><tr><td>";
	    
	    $page_array['subpage'] =  "frontend";
	    echo '<tr><td> <a href="'.urlize($page_array).'">'. word('Frontend Settings'). '</a>';
	    echo "</td></tr></table>";
	    
	    //unset($page_array['subpage']);
	    
	    $this->closeBlock();
	    return;
	  }
	  if ($_GET['subpage'] == "frontend" && !isset($page_array['set_fe'])) {
  ?>
 <form method="POST" action="<?php echo urlize($page_array); ?>">
    <select class="jz_select" name="<?php echo jz_encode('set_fe');?>">Frontend: 
   <?php
    $arr = readDirInfo($include_path.'frontend/frontends',"dir");
 foreach ($arr as $a) {
   if (file_exists($include_path."frontend/frontends/${a}/settings.php")) {
     echo "<option value=\"".jz_encode($a)."\"";
     if ($a == $my_frontend) {
       echo ' selected';
     }
     echo ">$a</option>";
   }
 }
   ?>
   </select>
       &nbsp;<input type="submit" class="jz_submit" value="<?php echo word('Go'); ?>">
		<?php 
		$this->closeBlock();
 return;
	  }
	  if (isset($_POST['update_postsettings']) && $_GET['subpage'] != "main") {
	    echo word('Settings Updated.'). "<br><br>";
	  }
	  
	  $display->openSettingsTable(urlize($page_array));
	  
	  if ($_GET['subpage'] == "main") {
	    $settings_file = $include_path.'settings.php';
	    $settings_array = settingsToArray($settings_file);

	    $urla = array();
	    $urla['subpage'] = "main";
	    $urla['action'] = "popup";
	    $urla['ptype'] = "sitesettings";



	      echo '<center>';
	      echo "| ";
	      $urla['subsubpage'] = "system";
	      echo '<a href="'.urlize($urla).'">'.word("System") . "</a> | ";
	      $urla['subsubpage'] = "playlist";
	      echo '<a href="'.urlize($urla).'">'.word("Playlist") . "</a> | ";
	      $urla['subsubpage'] = "display";
	      echo '<a href="'.urlize($urla).'">'.word("Display") . "</a> | ";
	      $urla['subsubpage'] = "image";
	      echo '<a href="'.urlize($urla).'">'.word("Image") . "</a> | ";
	      $urla['subsubpage'] = "groupware";
	      echo '<a href="'.urlize($urla).'">'.word("Groupware") . "</a> | ";
	      $urla['subsubpage'] = "jukebox";
	      echo '<a href="'.urlize($urla).'">'.word("Jukebox") . "</a> | ";
	      echo '<br>| ';
	      $urla['subsubpage'] = "resample";
	      echo '<a href="'.urlize($urla).'">'.word("Resampling") . "</a> | ";
	      $urla['subsubpage'] = "charts";
	      echo '<a href="'.urlize($urla).'">'.word("Charts/Random Albums") . "</a> | ";
	      $urla['subsubpage'] = "downloads";
	      echo '<a href="'.urlize($urla).'">'.word("Downloads") . "</a> | ";
	      $urla['subsubpage'] = "email";
	      echo '<a href="'.urlize($urla).'">'.word("Email") . "</a> | ";
	      $urla['subsubpage'] = "keywords";
	      echo '<a href="'.urlize($urla).'">'.word("Keywords") . "</a> | ";
	      echo "</center><br>";

	      if (isset($_POST['update_postsettings'])) {
		echo "<strong>".word("Settings Updated.")."</strong><br>";
	      }
	      echo "<br>";

	    switch ($_GET['subsubpage']) {
	    case "system":
	      $display->settingsTextbox("media_dirs","media_dirs",$settings_array);
	      $display->settingsTextbox("web_dirs","web_dirs",$settings_array);
	      $display->settingsDropdown("live_update","live_update",array("true","false"),$settings_array);
	      $display->settingsTextbox("audio_types","audio_types",$settings_array);
	      $display->settingsTextbox("video_types","video_types",$settings_array);
	      $display->settingsTextbox("ext_graphic","ext_graphic",$settings_array);
	      $display->settingsTextbox("track_num_seperator","track_num_seperator",$settings_array);
	      $display->settingsTextbox("date_format","date_format",$settings_array);
	      $display->settingsTextbox("short_date","short_date",$settings_array);
	      $display->settingsDropdown("allow_filesystem_modify","allow_filesystem_modify",array("true","false"),$settings_array);
	      $display->settingsDropdown("allow_id3_modify","allow_id3_modify",array("true","false"),$settings_array);
	      $display->settingsDropdown("gzip_handler","gzip_handler",array("true","false"),$settings_array);
	      $display->settingsDropdown("ssl_stream","ssl_stream",array("true","false"),$settings_array);
	      $display->settingsDropdown("media_lock_mode","media_lock_mode",array("off","track","album","artist","genre"),$settings_array);
	      break;
	    case "playlist":
	      $display->settingsDropdown("enable_playlist","enable_playlist",array("true","false"),$settings_array);
	      $display->settingsTextbox("playlist_ext","playlist_ext",$settings_array);
	      $display->settingsDropdown("use_ext_playlists","use_ext_playlists",array("true","false"),$settings_array);
	      $display->settingsTextbox("max_playlist_length","max_playlist_length",$settings_array);
	      $display->settingsTextbox("random_play_amounts","random_play_amounts",$settings_array);
	      $display->settingsTextbox("default_random_count","default_random_count",$settings_array);
	      $display->settingsTextbox("default_random_type","default_random_type",$settings_array);
	      $display->settingsTextbox("embedded_player","embedded_player",$settings_array);
	      break;
	    case "display":
	      $display->settingsTextbox("site_title","site_title",$settings_array);
	      $display->settingsDropdownDirectory("jinzora_skin","jinzora_skin",$include_path.'style',"dir",$settings_array);
	      $display->settingsDropdownDirectory("frontend","frontend",$include_path.'frontend/frontends',"dir",$settings_array);
	      $display->settingsDropdown("jz_lang_file","jz_lang_file",getLanguageList(),$settings_array);
	      $display->settingsDropdown("allow_lang_choice","allow_lang_choice",array("true","false"),$settings_array);
	      $display->settingsDropdown("allow_style_choice","allow_style_choice",array("true","false"),$settings_array);
	      $display->settingsDropdown("allow_interface_choice","allow_interface_choice",array("true","false"),$settings_array);
	      $display->settingsDropdown("use_ext_playlists","use_ext_playlists",array("true","false"),$settings_array);
	      $display->settingsDropdown("show_page_load_time","show_page_load_time",array("true","false"),$settings_array);
	      $display->settingsDropdown("show_sub_numbers","show_sub_numbers",array("true","false"),$settings_array);
	      $display->settingsTextbox("quick_list_truncate","quick_list_truncate",$settings_array);
	      $display->settingsTextbox("album_name_truncate","album_name_truncate",$settings_array);
	      $display->settingsDropdown("sort_by_year","sort_by_year",array("true","false"),$settings_array);
	      $display->settingsTextbox("num_other_albums","num_other_albums",$settings_array);	      
	      $display->settingsDropdown("header_drops","header_drops",array("true","false"),$settings_array);
	      $display->settingsDropdown("genre_drop","genre_drop",array("true","false","popup"),$settings_array);
	      $display->settingsDropdown("artist_drop","artist_drop",array("true","false","popup"),$settings_array);
	      $display->settingsDropdown("album_drop","album_drop",array("true","false","popup"),$settings_array);
	      $display->settingsDropdown("song_drop","song_drop",array("true","false","popup"),$settings_array);
	      $display->settingsDropdown("quick_drop","quick_drop",array("true","false"),$settings_array);
	      $display->settingsTextbox("days_for_new","days_for_new",$settings_array);	      
	      $display->settingsTextbox("hide_id3_comments","hide_id3_comments",$settings_array);	      
	      $display->settingsTextbox("show_all_checkboxes","show_all_checkboxes",$settings_array);	      
	      $display->settingsTextbox("status_blocks_refresh","status_blocks_refresh",$settings_array);	
	      $display->settingsDropdown("compare_ignores_the","compare_ignores_the",array("true","false"),$settings_array);      
	      $display->settingsDropdown("handle_compilations","handle_compilations",array("true","false"),$settings_array);      
	      $display->settingsTextbox("embedded_header","embedded_header",$settings_array);	      
	      $display->settingsTextbox("embedded_footer","embedded_footer",$settings_array);	      
	      break;
	    case "image":
	      $display->settingsDropdown("resize_images","resize_images",array("true","false"),$settings_array);
	      $display->settingsDropdown("keep_porportions","keep_porportions",array("true","false"),$settings_array);
	      $display->settingsDropdown("auto_search_art","auto_search_art",array("true","false"),$settings_array);
	      $display->settingsDropdown("create_blank_art","create_blank_art",array("true","false"),$settings_array);
	      //$display->settingsTextbox("default_art","default_art",$settings_array);	
	      break;
	    case "groupware":
	      $display->settingsDropdown("enable_discussions","enable_discussions",array("true","false"),$settings_array);
	      $display->settingsDropdown("enable_requests","enable_requests",array("true","false"),$settings_array);
	      $display->settingsDropdown("enable_ratings","enable_ratings",array("true","false"),$settings_array);
	      $display->settingsTextbox("rating_weight","rating_weight",$settings_array);
	      $display->settingsDropdown("track_plays","track_plays",array("true","false"),$settings_array);
	      $display->settingsDropdown("display_downloads","display_downloads",array("true","false"),$settings_array);
	      $display->settingsDropdown("secure_links","secure_links",array("true","false"),$settings_array);
	      $display->settingsDropdown("user_tracking_display","user_tracking_display",array("true","false"),$settings_array);
	      $display->settingsTextbox("user_tracking_age","user_tracking_age",$settings_array);
	      $display->settingsDropdown("disable_random","disable_random",array("true","false"),$settings_array);
	      $display->settingsTextbox("info_level","info_level",$settings_array);
	      $display->settingsDropdown("track_play_only","track_play_only",array("true","false"),$settings_array);
	      $display->settingsDropdown("allow_clips","allow_clips",array("true","false"),$settings_array);
	      $display->settingsTextbox("clip_length","clip_length",$settings_array);
	      $display->settingsTextbox("clip_start","clip_start",$settings_array);
	      break;
	    case "jukebox":
	      $display->settingsDropdown("jukebox","jukebox",array("true","false"),$settings_array);
	      $display->settingsDropdown("jukebox_display","jukebox_display",array("default","small","off"),$settings_array);
	      $display->settingsDropdown("jukebox_default_addtype","jukebox_default_addtype",array("current","begin","end","replace"),$settings_array);
	      $display->settingsTextbox("default_jukebox","default_jukebox",$settings_array);
	      $display->settingsTextbox("jb_volumes","jb_volumes",$settings_array);
	      break;
	    case "resample":
	      $display->settingsDropdown("allow_resample","allow_resample",array("true","false"),$settings_array);
	      $display->settingsDropdown("force_resample","force_resample",array("true","false"),$settings_array);
	      $display->settingsDropdown("allow_resample_downloads","allow_resample_downloads",array("true","false"),$settings_array);
	      $display->settingsTextbox("default_resample","default_resample",$settings_array);
	      $display->settingsTextbox("resampleRates","resampleRates",$settings_array);
	      $display->settingsTextbox("lame_cmd","lame_cmd",$settings_array);
	      $display->settingsTextbox("lame_opts","lame_opts",$settings_array);
	      $display->settingsTextbox("path_to_lame","path_to_lame",$settings_array);
	      $display->settingsTextbox("path_to_flac","path_to_flac",$settings_array);
	      $display->settingsTextbox("path_to_oggenc","path_to_oggenc",$settings_array);
	      $display->settingsTextbox("path_to_oggdec","path_to_oggdec",$settings_array);
	      $display->settingsTextbox("path_to_mpc","path_to_mpc",$settings_array);
	      $display->settingsTextbox("path_to_mpcenc","path_to_mpcenc",$settings_array);
	      $display->settingsTextbox("path_to_wavpack","path_to_wavpack",$settings_array);
	      $display->settingsTextbox("path_to_wavunpack","path_to_wavunpack",$settings_array);
	      $display->settingsTextbox("path_to_wmadec","path_to_wmadec",$settings_array);
	      $display->settingsTextbox("path_to_shn","path_to_shn",$settings_array);
	      $display->settingsTextbox("path_to_mplayer","path_to_mplayer",$settings_array);
	      $display->settingsTextbox("mplayer_opts","mplayer_opts",$settings_array);
	      $display->settingsTextbox("always_resample","always_resample",$settings_array);
	      $display->settingsTextbox("always_resample_rate","always_resample_rate",$settings_array);
	      $display->settingsTextbox("resample_cache_size","resample_cache_size",$settings_array);
	      break;
	    case "charts":
	      $display->settingsDropdown("display_charts","display_charts",array("true","false"),$settings_array);
	      $display->settingsTextbox("chart_types","chart_types",$settings_array);
	      $display->settingsTextbox("num_items_in_charts","num_items_in_charts",$settings_array);
	      $display->settingsTextbox("chart_timeout_days","chart_timeout_days",$settings_array);
	      $display->settingsTextbox("random_albums","random_albums",$settings_array);
	      $display->settingsTextbox("random_per_slot","random_per_slot",$settings_array);
	      $display->settingsTextbox("random_rate","random_rate",$settings_array);
	      $display->settingsTextbox("random_art_size","random_art_size",$settings_array);
	      $display->settingsDropdown("rss_in_charts","rss_in_charts",array("true","false"),$settings_array);
	      break;
	    case "downloads":
	      $display->settingsTextbox("multiple_download_mode","multiple_download_mode",$settings_array);
	      $display->settingsTextbox("single_download_mode","single_download_mode",$settings_array);
	      break;
	    case "email":
	      $display->settingsDropdown("allow_send_email","allow_send_email",array("true","false"),$settings_array);
	      //$display->settingsTextbox("email_from_address","email_from_address",$settings_array);
	      //$display->settingsTextbox("email_from_name","email_from_name",$settings_array);
	      //$display->settingsTextbox("email_server","email_server",$settings_array);
	      break;
	    case "keywords":
	      $display->settingsTextbox("keyword_radio","keyword_radio",$settings_array);
	      $display->settingsTextbox("keyword_random","keyword_random",$settings_array);
	      $display->settingsTextbox("keyword_play","keyword_play",$settings_array);
	      $display->settingsTextbox("keyword_track","keyword_track",$settings_array);
	      $display->settingsTextbox("keyword_album","keyword_album",$settings_array);
	      $display->settingsTextbox("keyword_artist","keyword_artist",$settings_array);
	      $display->settingsTextbox("keyword_genre","keyword_genre",$settings_array);
	      $display->settingsTextbox("keyword_lyrics","keyword_lyrics",$settings_array);
	      $display->settingsTextbox("keyword_limit","keyword_limit",$settings_array);
	      $display->settingsTextbox("keyword_id","keyword_id",$settings_array);
	      break;
	    default:
	      $this->closeBlock();
	      return;
	    }
	    /*
	    foreach ($settings_array as $key => $val) {
	      // The settingsTextbox (and other) functions update the array for us
	      // on a form submit. No other form handling is needed,
	      // other than to write the data back to the file!
	      // Plus, settings aren't modified if they aren't in the form.
	      if ($key == "jinzora_skin") {
		$display->settingsDropdownDirectory($key,$key,$include_dir."style","dir",$settings_array);
	      } else if ($key == "frontend") {
		$display->settingsDropdownDirectory($key,$key,$include_dir."frontend/frontends","dir",$settings_array);
	      } else {
		$display->settingsTextbox($key,$key,$settings_array);
	      }
	    }
	    */
	  } else if ($_GET['subpage'] == "services") {
	    $settings_file = $include_path.'services/settings.php';
	    $settings_array = settingsToArray($settings_file);
	    $display->settingsDropdownDirectory(word("Lyrics"), "service_lyrics", $include_path.'services/services/lyrics','file',$settings_array);
	    $display->settingsDropdownDirectory(word("Similar Artists"), "service_similar", $include_path.'services/services/similar','file',$settings_array);
	    $display->settingsDropdownDirectory(word("Links"), "service_link", $include_path.'services/services/link','file',$settings_array);
	    $display->settingsDropdownDirectory(word("Metadata Retrieval"), "service_metadata", $include_path.'services/services/metadata','file',$settings_array);
	    //$display->settingsDropdownDirectory(word("ID3 Tagging"), "service_tagdata", $include_path.'services/services/tagdata','file',$settings_array);
	  } else if ($_GET['subpage'] == "frontend") {
	    $settings_file = $include_path."frontend/frontends/".$page_array['set_fe']."/settings.php";
	    $settings_array = settingsToArray($settings_file);      
	    foreach ($settings_array as $key => $val) {
	      $display->settingsTextbox($key,$key,$settings_array);      
	    }
	  }
	  
	  $display->closeSettingsTable(is_writeable($settings_file));
	  //echo "&nbsp;";
	  //$this->closeButton();
	  if (isset($_POST['update_postsettings']) && is_writeable($settings_file)) {
	    arrayToSettings($settings_array,$settings_file);
	  }
	  $this->closeBlock();
	}

	/**
	* Displays the upload status box
	* 
	* @author Ross Carlson, Ben Dodson
	* @version 03/01/05
	* @since 03/01/05
	*/
	function displayUploadStatus(){
		global $root_dir;
		
		$this->displayPageTop("",word("Uploading Media, Please wait..."));
		$this->openBlock();
		
		echo '<br><center>';
		echo word('<strong>File upload in progress!</strong><br><br>This page will go away automatically when the upload is complete. Please be patient!'). "<br><br>";
		echo '<img src="'. $root_dir. '/style/images/computer.gif" border="0">';
		echo '<img src="'. $root_dir. '/style/images/uploading.gif" border="0">';
		echo '<img src="'. $root_dir. '/style/images/computer.gif" border="0">';

		$this->closeBlock();
	}	
	
	/**
	* Allows the user to add media
	* 
	* @author Ross Carlson, Ben Dodson
	* @version 03/01/05
	* @since 03/01/05
	*/
	function displayUploadMedia($node){
		global $audio_types, $video_types, $include_path, $root_dir, $jzUSER;
		

		if (checkPermission($jzUSER,"upload",$node->getPath("String")) === false) {
			echo word("Insufficient permissions.");
			exit();
		}
		// Did they want to actually create the link track
		if (isset($_POST['edit_add_link_track'])){
			// Ok, let's add the link
			$node->inject(array($_POST['edit_link_track_name']), $_POST['edit_link_track_url'],"track");

			exit();
			$this->closeWindow(true);
		}
		
		// Let's open the page
		$this->displayPageTop("",word("Add Media"). ": ". $node->getName());
		$this->openBlock();
		
		// Did they want to create a link track
		// This will show them the form
		if (isset($_POST['add_link_track'])){
			$arr = array();
			$arr['action'] = "popup";
			$arr['ptype'] = "uploadmedia";
			$arr['jz_path'] = $_GET['jz_path'];
			echo '<form action="'. urlize($arr). '" method="POST">';
			echo '<table class="jz_track_table" width="100%" cellpadding="3">';
			echo '<tr><td align="right">';
			echo word("Track Name"). ":";
			echo '</td><td>';
			echo '<input type="text" name="edit_link_track_name" class="jz_input" size="30">';
			echo '</td></tr>';
			echo '<tr><td align="right">';
			echo word("Track URL"). ":";
			echo '</td><td>';
			echo '<input type="text" name="edit_link_track_url" class="jz_input" size="30">';
			echo '</td></tr>';
			echo '</table>';
			echo '<br><center>';
			echo '<input type="submit" name="edit_add_link_track" value="'. word("Add Link Track"). '" class="jz_submit"></form> ';
			$this->closeButton(true);
			exit();
		}
		
		// Ok, did they want to uploade?
		if (isset($_POST['uploadfiles'])){
			// First let's flushout the display
			flushdisplay();
			
			echo word("Writing out files, please stand by..."). "<br><br>";
			echo '<div id="status"></div>';
			?>
			<SCRIPT LANGUAGE=JAVASCRIPT><!--\
				s = document.getElementById("status");
				-->
			</SCRIPT>
			<?php
			// BEN PUT THIS IN:
			// I'm not sure what it's supposed to be set to.
			// fixing a PHP warning.
			$c=0;
			// Ok, did they want to add a new sub location
			if (isset($_POST['edit_new_sub'])){
				// Ok, we need to create that new dir
				$newDir = $node->getDataPath("String"). "/". $_POST['edit_new_sub'];
				// Now we need to make sure that exsists
				$dArr = explode("/",$newDir);
				$newDir = "";
				for ($i=0;$i<count($dArr)+$c;$i++){
					if ($dArr[$i] <> ""){
						// Now let's build the newdir
						$newDir .= "/". $dArr[$i];
						if (!is_dir($newDir)){
							mkdir($newDir);
							chmod($newDir,0666);
							?>
							<SCRIPT LANGUAGE=JAVASCRIPT><!--\
								s.innerHTML = '<nobr><?php echo word("Status: Creating Dir:"); ?> <?php echo $dArr[$i]; ?></nobr>';
								-->
							</SCRIPT>
							<?php
							flushdisplay();
							sleep(1);
						}
					}
				}
			} else {
				$newDir =  $node->getDataPath("String");
			}
			$c=0;
			for ($i=1;$i<6;$i++){
				// Now let's see what they uploaded
				if ($_FILES['edit_file'. $i]['name'] <> ""){
					// Ok, They wanted to upload file #1, let's do it
					$newLoc = $newDir. "/". $_FILES['edit_file'. $i]['name'];
					// Ok, now that we've got the new name let's put it there
					if (copy($_FILES['edit_file'. $i]['tmp_name'], $newLoc)){
						// Now let's set the permissions
						chmod($newLoc, 0666);
						?>
						<SCRIPT LANGUAGE=JAVASCRIPT><!--\
							s.innerHTML = "<nobr><?php echo word('Status: Adding File:'); ?> <?php echo $_FILES['edit_file'. $i]['name']; ?></nobr>";
							-->
						</SCRIPT>
						<?php
						flushdisplay();
						sleep(1);
						$c++;
						// Ok, now was this a zip file?
						if (substr($_FILES['edit_file'. $i]['name'],-4) == ".zip"){
							?>
							<SCRIPT LANGUAGE=JAVASCRIPT><!--\
								s.innerHTML = "<nobr><?php echo word('Status: Extracting files in:'); ?> <?php echo $_FILES['edit_file'. $i]['name']; ?></nobr>";
								-->
							</SCRIPT>
							<?php
							flushdisplay();
							sleep(1);
							include_once($include_path. "lib/pclzip.lib.php");
							$zipfile = $newLoc;
							$archive = new PclZip($zipfile);
							if ($archive->extract(PCLZIP_OPT_PATH, $newDir) == 0) {
								?>
								<SCRIPT LANGUAGE=JAVASCRIPT><!--\
									s.innerHTML = "<nobr><?php echo word('Status: Extracting files in:'); ?> <?php echo $_FILES['edit_file'. $i]['name']; ?>!</nobr>";
									-->
								</SCRIPT>
								<?php
								flushdisplay();
							} else {
								$fileList = $archive->listContent();
								for ($i=0; $i < count($fileList); $i++){
									?>
									<SCRIPT LANGUAGE=JAVASCRIPT><!--\
										s.innerHTML = "<nobr><?php echo word('Status: Extracting file:'); ?> <?php echo $fileList[$i]['filename']; ?></nobr>";
										-->
									</SCRIPT>
									<?php
									flushdisplay();
									sleep(1);
									$c++;
								}
								$c=$c-1;
							}
							flushdisplay();
							// Now let's unlink that file
							unlink($zipfile);
						}
					}
				}
			}
			
			?>
			<SCRIPT LANGUAGE=JAVASCRIPT><!--\
				s.innerHTML = "<nobr><?php echo word('Status: Upload Complete!'); ?><br><?php echo $c; ?> <?php echo word('files uploaded'); ?></nobr>";
				-->
			</SCRIPT>
			<?php
			flushdisplay();
			sleep(1);
			?>
			<SCRIPT LANGUAGE=JAVASCRIPT TYPE="TEXT/JAVASCRIPT"><!--\
				thisWin = window.open('','StatusPop','');
				thisWin.close();
			-->
			</SCRIPT>
			<?php	
			echo '<br><br><center>';	
			$this->closeButton();	
			echo '</center>';
			exit();
		}
		// Did they just want to close?
		if (isset($_POST['justclose'])){
			$this->closeWindow(false);
		}
		
		echo word('When uploading you may upload single files or zip files containing all the files you wish to upload.  These will then be extracted once they have been uploaded.  You may also add your descritpion files and album art now and they will be displayed.  The following media types are supported by this system and may be uploaded:');
		echo "<br><br>". word('Audio'). ": ". $audio_types. "<br>". word('Video'). ": ". $video_types;
		echo "<br><br>";
		
		// Now let's start our form so they can upload
		$arr = array();
		$arr['action'] = "popup";
		$arr['ptype'] = "uploadmedia";
		$arr['jz_path'] = $_GET['jz_path'];
		echo '<form action="'. urlize($arr). '" method="POST" enctype="multipart/form-data">';
		?>		
		<center>
			<?php echo word("New Sub Path"); ?>: <br>
			<input type="text" name="edit_new_sub" class="jz_input" size="40"><br><br>
			<?php echo word('File'); ?> 1: <input type="file" name="edit_file1" class="jz_input" size="40"><br>
			<?php echo word('File'); ?> 2: <input type="file" name="edit_file2" class="jz_input" size="40"><br>
			<?php echo word('File'); ?> 3: <input type="file" name="edit_file3" class="jz_input" size="40"><br>
			<?php echo word('File'); ?> 4: <input type="file" name="edit_file4" class="jz_input" size="40"><br>
			<?php echo word('File'); ?> 5: <input type="file" name="edit_file5" class="jz_input" size="40"><br>
			<br><br>
			<input type=submit class="jz_submit" name="<?php echo jz_encode('justclose'); ?>" value="<?php echo word('Close'); ?>">
			<SCRIPT LANGUAGE=JAVASCRIPT TYPE="TEXT/JAVASCRIPT"><!--\
				function openStatusPop(obj, boxWidth, boxHeight){
					var sw = screen.width;
					var sh = screen.height;
					var winOpt = "width=" + boxWidth + ",height=" + boxHeight + ",left=" + ((sw - boxWidth) / 2) + ",top=" + ((sh - boxHeight) / 2) + ",menubar=no,toolbar=no,location=no,directories=no,status=yes,scrollbars=yes,resizable=no";
					thisWin = window.open(obj,'StatusPop',winOpt);
				}	
			-->
			</SCRIPT>
			<?php
				$aRR = array();
				$aRR['action'] = "popup";
				$aRR['ptype'] = "showuploadstatus";
			?>
			<input onMouseDown="openStatusPop('<?php echo urlize($aRR); ?>',300,200)" type=submit class="jz_submit" name="<?php echo jz_encode('uploadfiles'); ?>" value="<?php echo word('Upload'); ?>">
			<!--<input type=submit class="jz_submit" name="<?php echo jz_encode('add_link_track'); ?>" value="<?php echo word('Add Link Track'); ?>">-->
		</center>
		<?php
		echo '</form>';
		
		$this->closeBlock();
	}
	
	/**
	* Displays the Playlist Editor
	* 
	* @author Ross Carlson, Ben Dodson
	* @version 02/22/05
	* @since 02/22/05
	*/
	function displayPlaylistEditor(){
		global $jzUSER, $row_colors, $raw_img_play_clear,$random_play_amounts,$default_random_count,$jzSERVICES;
		// First we need to know if they deleted a list or not
		  if (isset($_POST['deletePlaylist'])){
		    if ($_SESSION['jz_playlist'] == "session") {
		      $pl = $jzUSER->loadPlaylist();
		      $pl->truncate(0);
		      $jzUSER->storePlaylist($pl);
		    } else {
		      $jzUSER->removePlaylist($_SESSION['jz_playlist']);
		      unset($_SESSION['jz_playlist']);
		    }
		    //$this->closeWindow(true);
		    //exit();
		  }
		  /*
		  // Now let's make sure the playlist session ID is set and if not set it to the first one
		  if (!isset($_SESSION['jz_playlist'])){
		    $lists = $jzUSER->listPlaylists();
		    foreach ($lists as $id=>$pname) {
		      $_SESSION['jz_playlist'] = $id;
		      break;
		    }
		  }
                  */
		  // Did they want to edit a different playlist?
		  if (isset($_POST['plToEdit'])){
		    $_SESSION['jz_playlist'] = $_POST['plToEdit'];
		  }
		  
		  $display = new jzDisplay();
		  $pl = new jzPlaylist();
		  
		  // Let's setup the form data
		  $arr = array();
		  $arr['action'] = "popup";
		  $arr['ptype'] = "playlistedit";

		  if (isset($_GET['createpl'])) {
		    if (isset($_POST['createpl2']) && $_POST['query'] != "") {
		      // HERE: Make list and set the session appropriately.
		      $pl = new jzPlaylist(array(),$_POST['query'],$_POST['pltype']);
		      $jzUSER->storePlaylist($pl);
		      $_SESSION['jz_playlist'] = $pl->getID();
		    }
		  }
		  
		  $title = "";
		  $title .= '<table cellspacing="0" border="0" width="100%"><tr><td align="left">';
		  $title .= word("Playlist Editor"). " &nbsp; - &nbsp; ";
		  $title .= '<form action="'. urlize($arr). '" method="POST">';
		  $title .= ' <select onChange="submit()" style="width:150;" name="plToEdit" class="jz_select">';
		  // Now we need to get all the lists
		  $lists = $jzUSER->listPlaylists("all");
		  $title .= '<option value= "session">'. word(" - Session Playlist - "). '</option>'. "\n";
		  foreach ($lists as $id=>$pname) {
		    $title .= '<option value="'.$id.'"';
		    if ($_SESSION['jz_playlist'] == $id){ $title .= ' selected'; } 
		    $title .= '>' . $pname . '</option>'."\n";
		  }		
		  $title .= '</select></form>';
		  $title .= '&nbsp;&nbsp;&nbsp;';
		  
		  $title .= '</td><td align="right">';
		  $arr['createpl'] = "true";
		  $title .= '<a href="'.urlize($arr).'">'.word('Create Playlist').'</a>';
		  $title .= "</td></tr></table>";
		  unset($arr['createpl']); 
		  if (isset($_SESSION['jz_playlist'])){
		    // Ok, let's show them the playlist dropdown
		    
		    $lists = $jzUSER->listPlaylists();
		    //$title .= ": ". $lists[$_SESSION['jz_playlist']];
		    $plName = $lists[$_SESSION['jz_playlist']];
		  } else {
		    $plName = false;
		  }
		  
		  $this->displayPageTop("",$title, false);
		  $this->openBlock();
		  

		  // * * * * * * * * *
		  // NEW PLAYLIST:
		  // * * * * * * * * *
		  if (isset($_GET['createpl'])) {
		    if (isset($_POST['createpl2']) && $_POST['query'] != "") {
		      // handled up above.
		    } else {
		      $arr = array();
		      $arr['action'] = "popup";
		      $arr['ptype'] = "playlistedit";
		      $arr['createpl'] = "true";

		      echo '<form method="POST" action="'.urlize($arr).'">';
		      echo '<table width="40%" align="left" border="0"><tr><td>';
		      echo word('Name:') . '<input name="query" class="jz_input"></td></tr><tr><td>';
		      echo '<input type="radio" class="jz_radio" name="'.jz_encode('pltype').'" value="'.jz_encode('static').'" CHECKED>Static';
		      echo '<input type="radio" class="jz_radio" name="'.jz_encode('pltype').'" value="'.jz_encode('dynamic').'">Dynamic</td></tr><tr><td>';
		      echo '<input type="submit" class="jz_submit" name="'.jz_encode('createpl2').'" value="'.word('Go').'"></td></tr>';
		      echo '</table>';
		      $this->closeBlock();
		      return;
		    }
		  }

		  // * * * * * * * * *
		  // DYNAMIC PLAYLISTS:
		  // * * * * * * * * *
		  if (getListType($_SESSION['jz_playlist']) == "dynamic") {
		    $i = 0;
		    $pl = $jzUSER->loadPlaylist();
		    if (isset($_POST['addrule'])) {
		      if ($_POST['source1'] != "") {
			$source = $_POST['source1'];
		      } else if ($_POST['source2'] != "") {
			$source = $_POST['source2'];
		      } else {
			$source = "";
		      }
		      $pl->addRule($_POST['amount'],$_POST['function'],$_POST['type'],$source);
		      $jzUSER->storePlaylist($pl);
		    }
		    if (isset($_POST['updateRestrictions'])) {
		      if (is_numeric($_POST['query'])) {
			$pl->setLimit($_POST['query']);
		      }
		      $jzUSER->storePlaylist($pl);
		    }

		    $arr = array();
		    $arr['action'] = "playlist";
		    $arr['type'] = "playlist";
		    $arr['jz_pl_id'] = $pl->getID();
		    echo '<strong><a href="'.urlize($arr).'"';
		    if (checkPlayback() == "embedded") {
		      echo ' ' . $jzSERVICES->returnPlayerHref();
		    }
                    echo '>'.word('Play this list').'</a></strong><br>';
                    ?>
		    <table class="jz_track_table" width="100%" cellpadding="1">
			<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
				<td width="3%" valign="top">&nbsp;
		       
				</td>
				<td width="12%">
					<nobr>
					<strong><?php echo word("Amount"); ?></strong>
					</nobr>
				</td>
				<td width="35%">
					<nobr>
						<strong><?php echo word("Function"); ?></strong>
					</nobr>
				</td>
				<td width="10%">
					<nobr>
						<strong><?php echo word("Type"); ?></strong>
					</nobr>
				</td>
				<td width="40%">
					<nobr>
						<strong><?php echo word("Source"); ?></strong>
					</nobr>
				</td>
			</tr>
		        <?php
		    $functions = getDynamicFunctions();
		    $e = 0;
		    $remove = false;
		    $arr = array();
		    $arr['action'] = "popup";
		    $arr['ptype'] = "playlistedit";
		    echo '<form method="POST" action="'.urlize($arr).'">';
		    $rules = $pl->getRules();

	            foreach($rules as $rule) {
		      if (isset($_POST['plRuleDel-'. $e]) && !$remove){
			// Ok, now let's delete that location
			$pl->removeRule($e);
			$remove = true;
		      } else {
			echo '<tr class="'.$row_colors[$i].'">';
			echo '<td><input type="image" value="'.$e.'" name="'.jz_encode('plRuleDel-'.$e).'" src="'.$raw_img_play_clear.'" title="'.word("Delete").'"></td>';
			echo '<td>&nbsp;'.$rule['amount'].'</td>';
			echo '<td>'.$functions[$rule['function']].'</td>';
			echo '<td>'.$rule['type'].'</td>';
			if (($src = $rule['source']) == "") {
			  $src = word('All Media');
			}
			echo '<td>'.$src.'</td>';
			$i = 1 - $i;
			$e++;
		      }
		      
		    }
		    if ($remove) {
		      $jzUSER->storePlaylist($pl);
		    }
 	            echo '</tr></form></table><br>';
		    echo '<form method="POST" action="'.urlize($arr).'">';
		    echo word("Limit:");
		    echo '&nbsp';
		    echo '<input class="jz_input" size="3" name="query" value="'.$pl->getLimit().'">';
		    echo '&nbsp;';
		    echo '<input class="jz_submit" type="submit" name="'.jz_encode('updateRestrictions').'" value="'.word('Update').'">';
		    echo '</form>';



		    echo '<br><br><br><br><br><br>';
		    // ADD A RULE:
		    echo '<form method="POST" action="'.urlize($arr).'">';
		    echo '<table border="0" align="center" cellspacing="0"><tr>';
		    // AMOUNT
		    echo '<td valign="top">';
		    $random_play = explode("|", $random_play_amounts);
		    echo '<select class="jz_select" name="'.jz_encode('amount').'">';
		    $ctr = 0;
		    while (count($random_play) > $ctr){
		      echo '<option value="'. jz_encode($random_play[$ctr]).'"';
		      if ($random_play[$ctr] == $default_random_count) {
			echo " selected";
		      }
		      echo '>'. $random_play[$ctr].'</option>'. "\n";
		      $ctr = $ctr + 1;
		    }
		    echo '</select></td>';
		    // FUNCTION
		    echo '<td valign="top">';
		    		    
		    echo '<select class="jz_select" name="'.jz_encode('function').'">';
		    foreach ($functions as $val=>$name) {
		      echo '<option value="'.jz_encode($val).'">';
		      echo $name;
		      echo '</option>';
		    }
		    echo '</select></td>';
		    // TYPE
		    echo '<td valign="top">';
		    echo '<select class="jz_select" name="'.jz_encode('type').'">';
		    echo '<option value="'.jz_encode('tracks').'">'.word('Songs').'</option>';
		    echo '<option value="'.jz_encode('albums').'">'.word('Albums').'</option>';
		    echo '</select>';
		    echo '</td>';
		    // SOURCE
		    if (distanceTo('genre') !== false || distanceTo('artist') !== false) {
		      echo '<td valign="top">'.word('from:').'</td>';
		      echo '<td valign="top">';
		      if (distanceTo("genre") !== false) {
			$display->dropdown("genre",false,"source1");
		      }
		      if (distanceTo("genre") !== false && distanceTo("artist") !== false) {
			echo '<br>';
		      }
		      if (distanceTo("artist") !== false) {
			echo $display->dropdown("artist",false,"source2");
		      }
		      echo '</td>';
		      echo '</td></tr></table><br>';
		      echo '<table align="center" border="0" cellspacing="0"><tr><td>';
 		      echo '<input type="submit" class="jz_submit" name="'.jz_encode('addrule').'" value="'.word('Add Rule').'">';
		      echo ' &nbsp;';
 		      echo '<input type="submit" name="deletePlaylist" value="'.word("Delete Playlist").'" class="jz_submit">';
		      echo '</td></tr></form>';

		    }
		    $this->closeBlock();
		    return;
		  }


		  // Now let's get the list into an array
		  $plist = $jzUSER->loadPlaylist($_SESSION['jz_playlist']);
		  if ($plist == ""){ exit(); }
		  
		  $plist->flatten();
		  $list = $plist->getList();
		  
		  // Now we need to see if they deleted a track or not
		  $e=0;
		  $remove = false;
		  foreach($list as $item){
		    //echo 'plTrackDel-'. $e. "<br>";
		    if (isset($_POST['plTrackDel-'. $e])){
		      // Ok, now let's delete that location
		      $plist->remove($e);
		      $remove = true;
		    }
		    $e++;
		  }
		  if ($remove){
		    // Now let's store it
		    $jzUSER->storePlaylist($plist, $plName);
		    // Now let's read the list again
		    $list = $plist->getList();
		  }
		  
		  // Ok, did they submit the form or not
		  if (isset($_POST['updatePlaylist'])){
		    // Ok, they want to update the playlist, so we need to rebuild it
		    // let's get the track positions so we can reorder the array
		    $nList = array();
		    $PostArray = $_POST;$i=0;$nArr=array();
		    foreach ($PostArray as $key => $val) {
		      if (stristr($key,"plTrackPos-")){
			// Now let's make sure this spot isn't taken
			if (isset($nArr[$val])){
			  // Now let's increment until we're clear
			  $c=$val;
			  while (isset($nArr[$c])){
			    $c++;
			  }
			  $nArr[$c] = $list[$i];
			} else {
			  $nArr[$val] = $list[$i];
			}
			$i++;
		      }
		    }
		    
		    // Let's truncate the old list
		    $plist->truncate(0);
		    // Now let's add them in order
		    for ($i=0;$i<count($nArr)+$c;$i++){
		      if (isset($nArr[$i])){
			$plist->add($nArr[$i]);
		      }
		    }
		    
		    // Now let's store it
		    $jzUSER->storePlaylist($plist, $plName);
		    // Now let's read the list again
		    $list = $plist->getList();
		  }
		  
		  $arr2 = array();
		    $arr2['action'] = "playlist";
		    $arr2['type'] = "playlist";
		    $arr2['jz_pl_id'] = $plist->getID();
		    echo '<strong><a href="'.urlize($arr2).'"';
		    if (checkPlayback() == "embedded") {
		      echo ' ' . $jzSERVICES->returnPlayerHref();
		    }
		    echo '>'.word('Play this list').'</a></strong>';
		  // Now we need to setup a table to display the list in
		  $i=1;
		    ?>
		    <form action=" <?php echo urlize($arr); ?>" method="POST">
		       <table class="jz_track_table" width="100%" cellpadding="1">
		       <tr>
		       <!--<td width="1%">
		       <nobr>
		       Playlist Type:
		      </nobr>
		       </td>
		       <td width="99%">
		       <select style="width:70;" name="plType" class="jz_select"><option value="private">Private</option><option value="public">Public</option></select>
		       </td>-->
		       <!--<td width="1%">
		       <nobr>
		       Share with user:
		      </nobr>
		       </td>
		       <td width="49%">
		       <select style="width:70;" name="shareWithUser" class="jz_select"><option value=""> - </option></select>
		       </td>
		       -->
		       </tr>
		       </table>
		<table class="jz_track_table" width="100%" cellpadding="1">
			<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
				<td width="1%" valign="top">
					
				</td>
				<td width="1%">
					
				</td>
				<td width="1%">
					<nobr>
					<strong><?php echo word("Track"); ?></strong>
					</nobr>
				</td>
				<td width="1%">
					<nobr>
						<strong><?php echo word("Album"); ?></strong>
					</nobr>
				</td>
				<td width="1%">
					<nobr>
						<strong><?php echo word("Artist"); ?></strong>
					</nobr>
				</td>
				<td width="1%">
					<nobr>
						<strong><?php echo word("Genre"); ?></strong>
					</nobr>
				</td>
			</tr>
			<?php
				$e=0;
				foreach($list as $item){
					// Now let's setup or names for below
					$track = $item->getName();
					$aItem = $item->getParent();
					$album = $aItem->getName();
					$artItem = $aItem->getParent();
					$artist = $artItem->getName();
					$gItem = $artItem->getParent();
					$genre = $gItem->getName();
					?>
					<input type="hidden" name="plItemPath" value="<?php echo $item->getPath("String"); ?>"
					<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
						<td width="1%">
							<input type="text" name="plTrackPos-<?php echo $e; ?>" size="2" class="jz_input" value="<?php echo $e; ?>">
						</td>
						<td width="1%">
							<input type="image" value="<?php echo $e; ?>" name="plTrackDel-<?php echo $e; ?>" src="<?php echo $raw_img_play_clear; ?>" title="<?php echo word("Delete"); ?>">
						</td>
						<td width="1%">
							<nobr>
							<?php echo $display->playlink($item,$display->returnShortName($track,20)); ?>
							</nobr>
						</td>
						<td width="1%">
							<nobr>
								<?php echo $display->returnShortName($album,20); ?>
							</nobr>
						</td>
						<td width="1%">
							<nobr>
								<?php echo $display->returnShortName($artist,20); ?>
							</nobr>
						</td>
						<td width="1%">
							<nobr>
								<?php echo $display->returnShortName($genre,20); ?>
							</nobr>
						</td>
					</tr>
					<?php
					$e++;
				}
			?>
		</table>
		<center>
			<br><br>
			<input type="submit" name="updatePlaylist" value="<?php echo word("Update Playlist"); ?>" class="jz_submit"> &nbsp;
			<input type="submit" name="deletePlaylist" value="<?php echo word("Delete Playlist"); ?>" class="jz_submit">
			<br><br><br>
		</center>
		</form>
		<?php
		
		$this->closeBlock();
	}
	
	
	/**
	* Displays the Item Retagger tool
	* 
	* @author Ross Carlson, Ben Dodson
	* @version 01/27/05
	* @since 01/27/05
	* @param $node The node we are looking at
	*/
	function displayReTagger($node){
		 global $jzSERVICES,$jzUSER;
		 
		 if (!checkPermission($jzUSER,"admin",$node->getPath("String"))) {
		   echo word("Insufficient permissions.");
		   return;
		 }
		 
		$title = word("Retag files");
		if ($node->getName() <> ""){
			$title = word("Retag files in"). ": ". $node->getName();
		}
		$this->displayPageTop("",$title, false);
		$this->openBlock();

		// Did they submit the form?
		if (isset($_POST['updateTags'])){
			// Let's not timeout
			set_time_limit(0);

			// Ok, now let's see what they wanted to retag
			$reGenre = false;$reArtist = false;$reAlbum = false;
			$reTrack = false;$reNumber = false; $reAlbumArt = false;
			
			if (isset($_POST['reGenre']) && $_POST['reGenre'] == "on"){
				$reGenre = true;
			}
			if (isset($_POST['reArtist']) && $_POST['reArtist'] == "on"){
				$reArtist = true;
			}
			if (isset($_POST['reAlbum']) && $_POST['reAlbum'] == "on"){
				$reAlbum = true;
			}
			if (isset($_POST['reTrack']) && $_POST['reTrack'] == "on"){
				$reTrack = true;
			}
			if (isset($_POST['reNumber']) && $_POST['reNumber'] == "on"){
				$reNumber = true;
			}
			if (isset($_POST['reAlbumArt']) && $_POST['reAlbumArt'] == "on"){
				$reAlbumArt = true;
			}
			
			// Now let's see what grouping were on
			$length = 50;
			if (!isset($_SESSION['jz_retag_group'])){
				$_SESSION['jz_retag_group'] = 0;
			} else {
				$_SESSION['jz_retag_group'] = $_SESSION['jz_retag_group'] + $length;
			}

			// Ok, now let's get on with it, first let's setup the div for displaying the data	
			echo word("Retagging files, please stand by..."). "<br><br>";
			flushdisplay();
			
			echo '<div id="group"></div>';
			echo '<div id="track"></div>';
			echo '<div id="trackname"></div>';	
			echo '<div id="tracknum"></div>';	
			echo '<div id="genre"></div>';
			echo '<div id="artist"></div>';
			echo '<div id="album"></div>';
			echo '<div id="status"></div>';	
			echo '<div id="percent"></div>';	
			?>
			<SCRIPT LANGUAGE=JAVASCRIPT><!--\
				gr = document.getElementById("group");
				t = document.getElementById("track");
				g = document.getElementById("genre");
				ar = document.getElementById("artist");
				al = document.getElementById("album");
				tnu = document.getElementById("tracknum");
				tn = document.getElementById("trackname");
				s = document.getElementById("status");
				p = document.getElementById("percent");
				-->
			</SCRIPT>
			<?php					
			
			// Ok, now we need to move track by track and get all it's data
			$allTracks = $node->getSubNodes("tracks",-1);
			?>
			<SCRIPT LANGUAGE=JAVASCRIPT><!--\
				gr.innerHTML = '<nobr><?php echo word("Files"); ?>: <?php echo $_SESSION['jz_retag_group']. " - ". ($_SESSION['jz_retag_group'] + $length). "/". count($allTracks); ?></nobr>';
				-->
			</SCRIPT>
			<?php	
			flushdisplay();
			
			$track = array_slice($allTracks,$_SESSION['jz_retag_group'],$length);
			$total = count($track);
			$success=0;$failed=0;$totalCount=0;
			$start = time();

			// Now let's get the art for this track
			for ($i=0;$i<count($track);$i++){
				$parent = $track[$i]->getParent();

				if ( $reAlbumArt && ($albumArt = $parent->getMainArt("200x200")) !== false) {
					if (!stristr($albumArt,"ID3:")){
						// Ok, let's get the properties of it
						if ($fd = fopen($albumArt, 'rb')){
							$APICdata = fread($fd, filesize($albumArt));
							fclose ($fd);
							list($APIC_width, $APIC_height, $APIC_imageTypeID) = GetImageSize($albumArt);
							$imagetypes = array(1=>'gif', 2=>'jpeg', 3=>'png');
							$pArr = explode("/",$albumArt);
							$pic_name = $pArr[count($pArr)-1];
							if (isset($imagetypes[$APIC_imageTypeID])) {
								$pic_data = $APICdata;
								$pic_ext = returnFileExt($albumArt);
								$pic_name = $pic_name;
								$pic_mime = 'image/'.$imagetypes[$APIC_imageTypeID];
							}
						}
					}
				}
				// First lets set the art
				if ($pic_data){
					$meta['pic_data'] = $pic_data;
					$meta['pic_ext'] = $pic_ext;
					$meta['pic_name'] = $pic_name;
					$meta['pic_mime'] = $pic_mime;
				}

				if ($track[$i]->getPath() == ""){continue;}
				// Ok, now we need to figure out the data from the path
				$path = $track[$i]->getPath();
				$filename = $track[$i]->getDataPath("String");
				
				if( !fopen( $filename, 'r+' ) ) {
					writeLogData( "messages", "ERROR: Could not open file for retagging: $filename" );
					echo "<br><br>Error opening file for writing at: ". $filename; 
					echo"<br><br><br><br><center>";
					unset($_SESSION['jz_retag_group']);
					$this->closeButton();
					exit();
				}
				
				$tName = $path[count($path)-1];
				$fName = $tName;
				// now let's split the exension and number IF it's there
				$tArr = explode(".",$tName);
				unset($tArr[count($tArr)-1]);
				$tName = implode(".",$tArr);
				if (is_numeric(substr($tName,0,2))){
					$tNum = substr($tName,0,2);
					$tName = substr($tName,3);
					// Now we need to clean off the dashes
					trim($tName);
					if (substr($tName,0,1) == "-"){
						$tName = trim(substr($tName,1));
					}
					if (substr($tName,0,1) == "_"){
						$tName = trim(substr($tName,1));
					}
				} else {
					$tNum = "01";
				}
				// Now let's convert underscores to spaces
				$tName = str_replace("_", " ",$tName);
				
				// Now let's get the rest and convert underscores to dashes
				if ($_POST['edit_reAlbum_custom'] <> ""){
				  $album = str_replace("_", " ",$_POST['edit_reAlbum_custom']);
				} else {
				  $album = getInformation($track[$i],"album");
				  if (!isNothing($album)) {
				    $disk = getInformation($track[$i],"disk");
				    if (!isNothing($disk)) {
				      $album .= " (" . $disk . ")";
				    }
				  }
				  // TODO: Do we want the Disk information here too?
				}
				if ($_POST['edit_reArtist_custom'] <> ""){
				  $artist = str_replace("_", " ",$_POST['edit_reArtist_custom']);
				} else {
				  $artist = getInformation($track[$i],"artist");
				}
				if ($_POST['edit_reGenre_custom'] <> ""){
					$genre = str_replace("_", " ",$_POST['edit_reGenre_custom']);
				} else {
				  $genre = getInformation($track[$i],"genre");
				}
				

				// Ok, we've got the data let's build our meta array
				if ( $reGenre && !isNothing($genre)){$meta['genre'] = $genre;}
				if ( $reArtist && !isNothing($artist)){$meta['artist'] = $artist;}
				if ( $reAlbum && !isNothing($album)){$meta['album'] = $album;}
				if ( $reTrack && !isNothing($tNum)){$meta['track'] = $tNum;}
				if ( $reNumber && !isNothing($tName)){$meta['title'] = $tName;}
				
				// Now let's display
				?>
				<SCRIPT LANGUAGE=JAVASCRIPT><!--\
					t.innerHTML = '<nobr><?php echo word("Track"); ?>: <?php echo str_replace("'","",$fName); ?></nobr>';
					tn.innerHTML = '<nobr><?php echo word("Track Name"); ?>: <?php echo str_replace("'","",$tName); ?></nobr>';
					tnu.innerHTML = '<nobr><?php echo word("Track Num"); ?>: <?php echo str_replace("'","",$tNum); ?></nobr>';
					g.innerHTML = '<nobr><?php echo word("Genre"); ?>: <?php echo str_replace("'","",$genre); ?></nobr>';
					ar.innerHTML = '<nobr><?php echo word("Artist"); ?>: <?php echo str_replace("'","",$artist); ?></nobr>';
					al.innerHTML = '<nobr><?php echo word("Album"); ?>: <?php echo str_replace("'","",$album); ?></nobr>';
					-->
				</SCRIPT>
				<?php
				flushdisplay();

				// Now let's get the progress
				$progress = round(($i / $total) * 100);
				$totalCount++;
				$progress = $totalCount. "/". $total. " - ". $progress. "%";
				// now let's write it
				
				if ($track[$i]->setMeta($meta)){
					?>
					<SCRIPT LANGUAGE=JAVASCRIPT><!--\
						s.innerHTML = '<nobr><?php echo word("Status"); ?>: <?php echo word("Success"); ?></nobr>';
						p.innerHTML = '<nobr><?php echo word("Progress"); ?>: <?php echo $progress; ?></nobr>';
						-->
					</SCRIPT>
					<?php
					$success++;
				} else {
					?>
					<SCRIPT LANGUAGE=JAVASCRIPT><!--\
						//s.innerHTML = '<nobr><?php echo word("Status"); ?>: <?php echo word("Failed"); ?></nobr>';
						p.innerHTML = '<nobr><?php echo word("Progress"); ?>: <?php echo $progress; ?></nobr>';
						-->
					</SCRIPT>
					<?php
					$failed++;
				}

				flushdisplay();
				unset($meta);
			}
			
			// Now are we done or do we continue?
			if (count($allTracks) < $_SESSION['jz_retag_group']){
				// Now let's update the nodes cache
				?>
				<SCRIPT LANGUAGE=JAVASCRIPT><!--\
					t.innerHTML = '<nobr><?php echo word("Updating Track Caching..."); ?></nobr>';
					tn.innerHTML = '&nbsp;';
					tnu.innerHTML = '&nbsp;';
					g.innerHTML = '&nbsp;';
					ar.innerHTML = '&nbsp;';
					al.innerHTML = '&nbsp;';
					s.innerHTML = '&nbsp;';
					p.innerHTML = '&nbsp;';
					gr.innerHTML = '&nbsp;';
					-->
				</SCRIPT>
				<?php
				flushdisplay();
				//$node->updateCache(true, false, false, true);
				updateNodeCache($node,true,false,true);
				?>
				<SCRIPT LANGUAGE=JAVASCRIPT><!--\
					t.innerHTML = '<nobr><?php echo word("Process Complete!"); ?></nobr>';
					tn.innerHTML = '&nbsp;';
					tnu.innerHTML = '&nbsp;';
					g.innerHTML = '&nbsp;';
					ar.innerHTML = '&nbsp;';
					al.innerHTML = '&nbsp;';
					s.innerHTML = '&nbsp;';
					p.innerHTML = '&nbsp;';
					gr.innerHTML = '&nbsp;';
					-->
				</SCRIPT>
				<?php
				unset($_SESSION['jz_retag_group']);
				echo"<center>";
				$this->closeButton();
				exit();
			} else {
				?>
				<SCRIPT LANGUAGE=JAVASCRIPT><!--\
					t.innerHTML = '<nobr><?php echo word("Proceeding, please stand by..."); ?></nobr>';
					tn.innerHTML = '&nbsp;';
					tnu.innerHTML = '&nbsp;';
					g.innerHTML = '&nbsp;';
					ar.innerHTML = '&nbsp;';
					al.innerHTML = '&nbsp;';
					s.innerHTML = '&nbsp;';
					p.innerHTML = '&nbsp;';
					gr.innerHTML = '&nbsp;';
					-->
				</SCRIPT>
				<?php
				flushdisplay();
				// Now we need to setup our bogus form
				$arr = array();
				$arr['action'] = "popup";
				$arr['ptype'] = "retagger";
				$arr['jz_path'] = $node->getPath("String");
				echo '<form name="retagger" action="'. urlize($arr). '" method="POST">';
				echo '<input type="hidden" name="reGenre" 				value="'. $_POST['reGenre']. '">';
				echo '<input type="hidden" name="reGenre_filesystem" 	value="'. $_POST['reGenre_filesystem']. '">';
				echo '<input type="hidden" name="edit_reGenre_custom" 	value="'. $_POST['edit_reGenre_custom']. '">';
				echo '<input type="hidden" name="reArtist" 				value="'. $_POST['reArtist']. '">';
				echo '<input type="hidden" name="reArtist_filesystem" 	value="'. $_POST['reArtist_filesystem']. '">';
				echo '<input type="hidden" name="edit_reArtist_custom" 	value="'. $_POST['edit_reArtist_custom']. '">';
				echo '<input type="hidden" name="reAlbum" 				value="'. $_POST['reAlbum']. '">';
				echo '<input type="hidden" name="reAlbum_filesystem" 	value="'. $_POST['reAlbum_filesystem']. '">';
				echo '<input type="hidden" name="edit_reAlbum_custom" 	value="'. $_POST['edit_reAlbum_custom']. '">';
				echo '<input type="hidden" name="reTrack" 				value="'. $_POST['reTrack']. '">';
				echo '<input type="hidden" name="reNumber" 				value="'. $_POST['reNumber']. '">';
				echo '<input type="hidden" name="reAlbumArt" 				value="'. $_POST['reAlbumArt']. '">';
				echo '<input type="hidden" name="updateTags" 			value="'. $_POST['updateTags']. '">';
				echo '</form>';
				
				?>
				<SCRIPT language="JavaScript">
				document.retagger.submit();
				</SCRIPT>

				<?php
				exit();
			}
		}
		
		// Now let's give them a form so they can pick what to auto-tag
		$arr = array();
		$arr['action'] = "popup";
		$arr['ptype'] = "retagger";
		$arr['jz_path'] = $node->getPath("String");
		echo '<form name="retagger" action="'. urlize($arr). '" method="POST">';
		
		?>
		<?php echo word("This tool will rewrite the ID3 tags on your MP3 files based on their structure in the filesystem or with the values you specify below.  You may select which values will be updated by check them below."); ?>
		<br><br>
		<table width="100%">
			<tr>
				<td valign="top">
					<input type="checkbox" <?php if (getInformation($node,"genre") !== false) echo "checked"; ?> name="reGenre"> <?php echo word("Genre"); ?>
				</td>
				<td>
					<input onClick="document.retagger.edit_reGenre_custom.value='';" value="filesystem" type="radio" checked name="reGenre_filesystem"> <?php echo word("Filesystem Data"); ?><br>
					<input value="custom" type="radio" name="reGenre_filesystem"> 
					<input type="text" name="edit_reGenre_custom" value="" size="30" class="jz_input">
				</td>
			</tr>
			<tr>
				<td valign="top">
					<input type="checkbox" <?php if (getInformation($node,"artist") !== false) echo "checked"; ?> name="reArtist"> <?php echo word("Artist"); ?>
				</td>
				<td>
					<input onClick="document.retagger.edit_reArtist_custom.value='';" value="filesystem" type="radio" checked name="reArtist_filesystem"> <?php echo word("Filesystem Data"); ?><br>
					<input value="custom" type="radio" name="reArtist_filesystem"> 
					<input type="text" name="edit_reArtist_custom" value="" size="30" class="jz_input">
				</td>
			</tr>
			<tr>
				<td valign="top">
					<input type="checkbox" <?php if (getInformation($node,"album") !== false) echo "checked"; ?> name="reAlbum"> <?php echo word("Album"); ?>
				</td>
				<td>
					<input onClick="document.retagger.edit_reAlbum_custom.value='';" value="filesystem" type="radio" checked name="reAlbum_filesystem"> <?php echo word("Filesystem Data"); ?><br>
					<input value="custom" type="radio" name="reAlbum_filesystem"> 
					<input type="text" name="edit_reAlbum_custom" value="" size="30" class="jz_input">
					<br><br>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<input type="checkbox" checked name="reTrack"> <?php echo word("Track Name"); ?>
				</td>
				<td>
					&nbsp; &nbsp; &nbsp; <?php echo word("Filesystem Data"); ?><br>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<input type="checkbox" checked name="reNumber"> <?php echo word("Track Number"); ?>
				</td>
				<td>
					&nbsp; &nbsp; &nbsp; <?php echo word("Filesystem Data"); ?><br>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<input type="checkbox" checked name="reAlbumArt"> <?php echo word("Album Art"); ?>
				</td>
				<td>
					&nbsp; &nbsp; &nbsp; <?php echo word("Filesystem Data"); ?><br>
				</td>
			</tr>
		</table>
		<?php $_SESSION['jz_retag_group'] == "NULL"; ?>
		<br><center><input type="submit" name="updateTags" value="<?php echo word("Retag Tracks"); ?>" class="jz_submit"></center>
		</form>
		<?php
		
		$this->closeBlock();
		
	}
	

  /**
   * This is a 'smart' function that displays the user 
   * information about a piece of media.
   *
   * @author Ben Dodson
   * @since 7/6/05
   * @version 7/6/05
   **/
  function itemInformation($item) {
    if ($item->getType() == "jzMediaNode" || $item->getType() == "jzMediaTrack") {
      if ($item->isLeaf()) {
		$this->displayTrackInfo($item);
      } else { // node
		if (isNothing($item->getDescription())) {
		  $this->displayNodeStats($item);
		} else {
		  $this->displayReadMore($item);
		}
      }
    }
  }

	/**
	* Displays the Item Information editing tool
	* 
	* @author Ross Carlson, Ben Dodson
	* @version 01/27/05
	* @since 01/27/05
	* @param $node The node we are looking at
	*/
	function displayItemInfo($node){
		global $row_colors, $include_path, $allow_filesystem_modify, $resize_images, $jzSERVICES,$jzUSER;


		if (!checkPermission($jzUSER,"admin",$node->getPath("String"))) {
		  $this->itemInformation($node);
		  return;
		}
		// Ok, did they submit this form?
		if (isset($_POST['closeupdate']) or isset($_POST['updatedata'])){
			// Alright, they wanted to update
			
			// Let's update the descriptions
			if ($allow_filesystem_modify == "true"){
				$fName = $node->getDataPath("String"). "/album-desc.txt";
				$handle = @fopen ($fName, "w");
				@fwrite($handle,$_POST['edit_long_desc']);				
				@fclose($handle);
			}
			$node->addShortDescription($_POST['edit_short_desc']);
			$node->addDescription($_POST['edit_long_desc']);
			
			// Now, did they update the ID
			if ($_POST['edit_item_id'] <> $node->getID()){
				if ($allow_filesystem_modify == "true"){
					$fName = $node->getDataPath("String"). "/album.id";
					$handle = @fopen ($fName, "w");
					@fwrite($handle,$_POST['edit_item_id']);				
					@fclose($handle);
				}
				$node->setID($_POST['edit_item_id']);
			}
			
			// Now, did they update the year?
			if ($_POST['edit_item_year'] <> $node->getYear()){
				// Ok, now we need to update the year on this node
				$meta['year'] = $_POST['edit_item_year'];
				$dirtyFlag = true;

				// Now let's update the cache for the node
				//$node->updateCache(true, false, false, true);
			}
			
			// Now let's update the image, IF they did
			if ($_FILES['edit_thumbnail']['name'] <> ""){
				// Ok, now we need to put it into the data dir
				// First let's get the name of the new image
				if ($allow_filesystem_modify == "true"){
					$imgName = $node->getDataPath("String"). "/". $node->getName(). ".jpg";

					//NOTE: this should really be put in the general lib
					//*ALL* filenames should be given this treatment
					$imgName = preg_replace( "/(:|\*|\?|<|>|\'|\"|\|)/", "", $imgName);
				} else {
					$imgName = $include_path. "data/images/". str_replace("/","---",$node->getPath("String")). ".jpg";
					$imgName = preg_replace( "/(:|\*|\?|<|>|\'|\"|\|)/", "", $imgName);
				}

				// Now let's kill the old image if it's there
				if (is_file($imgName)){unlink($imgName);}
				
				// Now let's put the new file in place
				//echo $_FILES['edit_thumbnail']['tmp_name']; exit();
				if (copy($_FILES['edit_thumbnail']['tmp_name'], $imgName)){

					// Now let's set the permissions
					chmod($imgName, 0666);
					// Now let's add it to the node
					$node->addMainArt($imgName);

					//Regenerate the thumbnail images
					//HACK: don't know a better way to get the filename we're trying to write to
					//Don't unlink the image if it wasn't resized

					$retVal100 = $jzSERVICES->resizeImage($imgName, "100x100");
					if( !strcmp( $imgName, $retVal100 ) ) { @unlink($retVal100); }
				
					$retVal150 = $jzSERVICES->resizeImage($imgName, "150x150");
					if( !strcmp( $imgName, $retVal150 ) ) { @unlink($retVal150); }
				
					$retVal200 = $jzSERVICES->resizeImage($imgName, "200x200");
					if( !strcmp( $imgName, $retVal150 ) ) { @unlink($retVal200); }
				}
			}
			
			if (isset($_POST['edit_delete_thumb'])){
				$node->addMainArt('');
			}
			
			// Now do we need to close out?
			if (isset($_POST['closeupdate'])){
				$this->closeWindow(true);
			}
		
			// Did they want to rotate the art?
			if (isset($_POST['edit_rotate_image'])){
				$jzSERVICES->rotateImage($node->getMainArt(),$node);
			}

			// Now update the id3 tag with the image
			if ($_FILES['edit_thumbnail']['name'] <> "" && isset( $_POST['edit_image_to_id3'] ) ){

				$imageData = file_get_contents($imgName);
				$image_info = getimagesize( $imgName );

				// From getimagesize(); 1 = jpeg, 2 = gif
				$mimeType = null;
				$picExt = null;
				if( $image_info[2] == 1 ) {
					$mimeType = "image/jpeg";
					$picExt = "jpg";
				} else if( $image_info[2] == 2 ) { 
					$mimeType = "image/gif";
					$picExt = "gif";
				}

				$needsResizing = ( $image_info[0] > 200 || $image_info[1] > 200 );

				// Ok, now let's resize this first
				// If art is too big it looks like shit in the players
				if( $needsResizing ) {

					// Now let's write it out
					$file = $include_path. 'temp/tempimage.jpg';
					$dest = $include_path. 'temp/destimage.jpg';
					$handle = fopen($file, "w");
					fwrite($handle,$imageData);
					fclose ($handle);

					// Now let's resize; do this for all standard images larger than 200x200
					// Note that if this fails, we just use the original image in the tag
					if ( strcmp( $jzSERVICES->resizeImage($file, "200x200", $dest), $imgName ) != 0 ){
						// Now let's get new data for the tag writing
						unset($imageData);
						$imageData = file_get_contents($dest);

						// Reset the mime type, since we're probably converting the image to a jpg
						// regardless of the input type
						$new_image_info = getimagesize( $dest );
						$mimeType = null;
						$picExt = null;

						// From getimagesize(); 1 = jpeg, 2 = gif
						if( $new_image_info[2] == 1 ) {
							$mimeType = "image/jpeg";
							$picExt = "jpg";
						} else if( $new_image_info[2] == 2 ) { 
							$mimeType = "image/gif";
							$picExt = "gif";
						} else {
							// currently unsupported type
							$mimeType = null;
						}
					}

					// Now let's clean up
					@unlink($file);
					@unlink($dest);
				}

				// Now let's make sure that was valid
				if (strlen($imageData) >= 2000 && $mimeType ){
					$dirtyFlag = true;

					$nameParts = explode( '/', $imgName );
					$imgShortName = $nameParts[count( $nameParts )-1];
	
					$meta['pic_mime'] = $mimeType;							
					$meta['pic_data'] = $imageData;
					$meta['pic_ext'] = $picExt;
					$meta['pic_name'] = $imgShortName;
				}
			}

			if( $dirtyFlag ) { $node->bulkMetaUpdate($meta); }
		}
		
		$this->displayPageTop("",word("Item Information for"). ": ". $node->getName());
		$this->openBlock();
		$display = new jzDisplay();
		
		// Let's setup our form
		$arr = array();
		$arr['action'] = "popup";
		$arr['ptype'] = "iteminfo";
		$arr['jz_path'] = $_GET['jz_path'];
		echo '<form action="'. urlize($arr). '" method="POST" enctype="multipart/form-data">';
		
		// Ok, now let's see what they can edit?
				$i=0;
				?>
				<table class="jz_track_table" width="100%" cellpadding="5" cellspacing="0" border="0">
				   <?php
				   $artist = $node->getAncestor("artist");
				if ($artist !== false) { ?>
					<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
						<td width="30%" valign="top">
							<nobr>
								<?php echo word('Artist'); ?>:
							</nobr>
						</td>
						<td width="70%" valign="top">
							<!--<input type="text" name="edit_item_name" value="<?php echo $node->getName(); ?>" class="jz_input">-->
							<?php echo $artist->getName(); ?>
						</td>
					</tr>
						    <?php } $album = $node->getAncestor("album");
					if ($album !== false) { ?>
					<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
						<td width="30%" valign="top">
							<nobr>
								<?php echo word('Album'); ?>:
							</nobr>
						</td>
						<td width="70%" valign="top">
							<!--
							<select name="edit_item_parent" class="jz_select" style="width:185px;">
								<?php
									// Now let's get all the items at this level
									$root = new jzMediaNode();
									switch ($node->getPType()){
										case "artist":
											$valArr = $root->getSubNodes("nodes",distanceTo("genre"));
										break;
									}			
									for ($e=0;$e<count($valArr);$e++){
										echo '<option ';
										if ($valArr[$e]->getName() == $parent->getName()){
											echo ' selected ';
										}
										echo 'value="'. $valArr[$e]->getName(). '">'. $valArr[$e]->getName(). "</option>\n";
									}						
								?>
							</select>-->
							<?php echo $album->getName(); ?>
						</td>
					</tr>
					<?php
						} if ($node->getPType() == "album"){
					?>
						<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
							<td width="30%" valign="top">
								<nobr>
									<?php echo word("Album year"); ?>
								</nobr>
							</td>
							<td width="70%" valign="top">
								<input type="text" name="edit_item_year" value="<?php echo $node->getYear(); ?>" class="jz_input" size="4">
							</td>
						</tr>
								    <?php
								    } else {
								      echo '<input type="hidden" name="edit_item_year" value="'. $node->getYear(). '" class="jz_input" size="4">';
								    } 
					$be = new jzBackend();
					if ($be->hasFeature('setID')) {
						  ?>
									    
						<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
							<td width="30%" valign="top">
								    
								<nobr>
									<?php echo word("Item ID"); ?>
								</nobr>
							</td>
							<td width="70%" valign="top">
								<input type="text" name="edit_item_id" value="<?php echo $node->getID(); ?>" class="jz_input" size="20">
							</td>
						</tr>
					<?php
					}
					?>
					<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
						<td width="30%" valign="top">
							<nobr>
								<?php echo word("Short Description"); ?>
							</nobr>
						</td>
						<td width="70%" valign="top">
							<textarea class="jz_input" name="edit_short_desc" style="width: 250px" rows="10"><?php echo $node->getShortDescription(); ?></textarea>
						</td>
					</tr>
					<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
						<td width="30%" valign="top">
							<nobr>
								<?php echo word("Description"); ?>
							</nobr>
						</td>
						<td width="70%" valign="top">
							<textarea class="jz_input" name="edit_long_desc" style="width: 250px" rows="10"><?php echo $node->getDescription(); ?></textarea>
						</td>
					</tr>
					<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
						<td width="30%" valign="top">
							<nobr>
								<?php echo $third_desc; ?>
							</nobr>
						</td>
						<td width="70%" valign="top">
							<?php 
								if (($art = $node->getMainArt()) <> false) {
									$display->image($art,$node->getName(),150,150,"limit",false,false,false,"","");
									echo "<br>";
								}
							?>
							New Image:<br><input type="file" class="jz_input" name="edit_thumbnail" size="30"><br>
							<input type="checkbox" name="edit_image_to_id3"> <?php echo word("Apply image to ID3 tags"); ?>
							<input type="checkbox" name="edit_delete_thumb"> <?php echo word("Delete image"); ?>
						</td>
					</tr>
					<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
						<td width="100%" colspan="2" valign="top" align="center">
							<br><br>
							<input type=submit class="jz_submit" name="<?php echo jz_encode('closeupdate'); ?>" value="<?php echo word("Update & Close"); ?>">
							<input type=submit class="jz_submit" name="<?php echo jz_encode('updatedata'); ?>" value="<?php echo word("Update"); ?>">
							<?php
								if ($resize_images = "true"){
									//echo '<input type=submit class="jz_submit" name="edit_rotate_image" value="'. word("Rotate Image"). '">';
								}
								echo "<br><br>";
								$this->closeButton();
							?>
							<br><br><br>
						</td>
					</tr>
				</table>
				<?php
		echo "</form>";
		
		$this->closeBlock();
	}

	/**
	* Displays the tool to let the user add a link track.
	* 
	* @author Ross Carlson, Ben Dodson
	* @version 9/22/05
	* @since 9/22/05
	* @param $node The node we are looking at
	*/
	function displayAddLinkTrack($node){

	  $this->displayPageTop("",word("Add Link Track in"). ": ". $node->getName());
	  $this->openBlock();

	  if (isset($_POST['edit_taddress'])) {
	    $path = array();
	    $path[] = $_POST['edit_tname'];
	    $tr = $node->inject($path,$_POST['edit_taddress']);
	    if ($tr !== false) {
	      $meta = $tr->getMeta();
	      $meta['title'] = $_POST['edit_tname'];
	      $tr->setMeta($meta);
	    }

	    echo word("Added") . ": " . $_POST['edit_tname'];
	    echo " (" . $_POST['edit_taddress'] . ")";

	    echo '<br><br>';
	    $this->closeButton();
	    $this->closeBlock();
	    return;
	  }


	  // Let's show the form to edit with
	  $arr = array();
	  $arr['action'] = "popup";
	  $arr['ptype'] = "addlinktrack";
	  $arr['jz_path'] = $node->getPath("String");
	  echo '<form action="'. urlize($arr). '" method="POST">';
	  echo '<table><tr><td width="30%">';
	  echo word("Name"). ": ";
	  echo '</td><td>';
	  echo '<input name="edit_tname" class="jz_input">';
	  echo '</td></tr>';
	  echo '<tr><td>';
	  echo word("Address"). ": ";
	  echo '</td><td>';
	  echo '<input name="edit_taddress" class="jz_input">';
	  echo '</td></tr></table>';
	  echo '<br><br>';
	  echo '<input type="submit" class="jz_submit" value="'.word('Add Link').'">';
	  $this->closeButton();
	  echo '</form>';
	  $this->closeBlock();
	}
	
	/**
	* Displays the tool to let the user set the page type
	* 
	* @author Ross Carlson, Ben Dodson
	* @version 01/27/05
	* @since 01/27/05
	* @param $node The node we are looking at
	*/
	function displaySetPType($node){
	  global $jzUSER;
	  
	  if (!checkPermission($jzUSER,"admin",$node->getPath("String"))) {
	    echo word("Insufficient permissions.");
	    return;
	  }


		if (isset($_POST['edit_auto_set_ptype'])){
			$this->displayAutoPageType($node);
			exit();
		}
		
		// Let's see if they submitted the form
		if (isset($_POST['newPType'])){			
			// Now let's set the type
		  if ($_POST['newPType'] != "unchanged") {
		    $node->setPType($_POST['newPType']);
		  }

		  $i = 1;
		  while (isset($_POST["newPType-$i"])) {
		    if (($pt = $_POST["newPType-$i"]) != "unchanged") {
		      $nodes = $node->getSubNodes("nodes",$i);
		      foreach ($nodes as $n) {
				$n->setPType($pt);
		      }
		    }
		    $i++;
		  }
		  echo "<br><br><center>";
		  $this->closeButton(true);
		  exit();
		}
		$this->displayPageTop("",word("Set Page Type for"). ": ". $node->getName());
		$this->openBlock();
		
		// Let's show the form to edit with
		$arr = array();
		$arr['action'] = "popup";
		$arr['ptype'] = "setptype";
		$arr['jz_path'] = $node->getPath("String");
		echo '<form action="'. urlize($arr). '" method="POST">';
		echo word("Current Page Type"). ": ". $node->getPType(). "<br><br>";
		echo '<table><tr><td>';
		echo word("New Page Type"). ": ";
		echo '</td><td>';
		echo '<select name="'. jz_encode("newPType"). '" class="jz_select">';
		echo '<option value="'. jz_encode("unchanged"). '">'. word("Unchanged"). '</option>';
		echo '<option value="'. jz_encode("genre"). '">'. word("Genre"). '</option>';
		echo '<option value="'. jz_encode("artist"). '">'. word("Artist"). '</option>';
		echo '<option value="'. jz_encode("album"). '">'. word("Album"). '</option>';
		echo '<option value="'. jz_encode("disk"). '">'. word("Disk"). '</option>';
		echo '<option value="'. jz_encode("generic"). '">'. word("Generic"). '</option>';
		echo '</select>';
		echo '</td></tr>';
		$i = 1;
		while ($node->getSubNodeCount("nodes",$i) > 0) {
		  echo "<tr><td>Level $i:</td><td>";
		  echo '<select name="'. jz_encode("newPType-$i"). '" class="jz_select">';
		  echo '<option value="'. jz_encode("unchanged"). '">'. word("Unchanged"). '</option>';
		  echo '<option value="'. jz_encode("genre"). '">'. word("Genre"). '</option>';
		  echo '<option value="'. jz_encode("artist"). '">'. word("Artist"). '</option>';
		  echo '<option value="'. jz_encode("album"). '">'. word("Album"). '</option>';
		  echo '<option value="'. jz_encode("disk"). '">'. word("Disk"). '</option>';
		  echo '<option value="'. jz_encode("generic"). '">'. word("Generic"). '</option>';
		  echo '</select></td></tr>';
		  $i++;
		}
		echo "</table>";
		echo '<br><input type="submit" name="updatePType" value="'. word("Update Type"). '" class="jz_submit">';
		echo ' <input type="submit" name="edit_auto_set_ptype" value="'. word("Auto Set Page Type"). '" class="jz_submit">';
		echo " ";
		$this->closeButton();
		echo '</form>';
		
		$this->closeBlock();
		exit();
	}
	
	/**
	* Displays the site/location news block text to be edited
	* 
	* @author Ross Carlson, Ben Dodson
	* @version 01/27/05
	* @since 01/27/05
	* @param $node The node we are looking at
	*/
	function displaySiteNews($node){
	  global $jzUSER;

	  if (!checkPermission($jzUSER,"admin",$node->getPath("String"))) {
	    echo word("Insufficient permissions.");
	    return;
	  }
	  

	  $be = new jzBackend();
		
		// Let's figure out the news location
		if ($node->getName() == ""){
			$news = "site-news";
			$title = word("Site News");
		} else {
			$news = $node->getName(). "-news";
			$title = word("Site News"). ": ". $node->getName();
		}
		
		$this->displayPageTop("",$title);
		$this->openBlock();
		
		// Did they submit the form to edit the news?
		if (isset($_POST['updateSiteNews'])){
			// Now let's store the data
			$be->storeData($news, nl2br(str_replace("<br />","",$_POST['siteNewsData'])));
		}
		
		// Let's show the form to edit with
		$arr = array();
		$arr['action'] = "popup";
		$arr['ptype'] = "sitenews";
		$arr['jz_path'] = $_GET['jz_path'];
		echo '<form action="'. urlize($arr). '" method="POST">';
		?>
		<br>
		<center>
			<textarea name="siteNewsData" cols="60" rows="20" class="jz_input"><?php echo $be->loadData($news); ?></textarea>
			<br><br>
			<input type="submit" value="<?php echo word("Update News"); ?>" name="<?php echo jz_encode("updateSiteNews"); ?>" class="jz_submit">
			&nbsp;
			<?php
				$this->closeButton(false);
			?>
		</center>
		<?php
		
		$this->closeBlock();
		exit();
	}
	
	/**
	* Displays the full top played list
	* 
	* @author Ross Carlson, Ben Dodson
	* @version 01/27/05
	* @since 01/27/05
	*/
	function displayDupFinder(){
		
		$this->displayPageTop("",word("Duplicate Finder"));
		$this->openBlock();
		
		// Now let's see if they searched
		if (isset($_POST['searchDupArtists']) or isset($_POST['searchDupAlbums']) or isset($_POST['searchDupTracks'])){
			// Ok, let's search, but for what?
			if (isset($_POST['searchDupArtists'])){
				$distance = distanceTo("artist");
				$what = "nodes";
			}
			if (isset($_POST['searchDupAlbums'])){
				$distance = distanceTo("album");
				$what = "nodes";
			}
			
			// Ok, now we need to get a list of ALL artist so we can show possible dupes
			echo word("Retrieving full list..."). "<br><br>";
			flushdisplay();
			
			$root = new jzMediaNode();
			$artArray = $root->getSubNodes($what,$distance);
			for ($i=0;$i<count($artArray);$i++){
				$valArray[] = $artArray[$i]->getName();
			}
			echo word("Scanning full list..."). "<br><br>";
			flushdisplay();
			
			$found = $root->search($valArray,$what,$distance,sizeof($valArray),"exact");
			foreach ($found as $e) {
				$matches[] = $e->getName();
				echo $e->getName(). '<br>';
				flushdisplay();
			}
			
			

			
			$this->closeBlock();
			exit();
			
		}
		
		$arr = array();
		$arr['action'] = "popup";
		$arr['ptype'] = "dupfinder";
		echo '<form action="'. urlize($arr). '" method="POST">';
		echo "<br><br>";
		echo "<center>";
		echo word("Please select what you would like to search for"). "<br><br><br>";
		echo '<input type="submit" value="'. word("Search Artists"). '" name="'. jz_encode("searchDupArtists"). '" class="jz_submit">';
		echo ' &nbsp; ';
		echo '<input type="submit" value="'. word("Search Albums"). '" name="'. jz_encode("searchDupAlbums"). '" class="jz_submit">';
		echo ' &nbsp; ';
		echo '<input type="submit" value="'. word("Search Tracks"). '" name="'. jz_encode("searchDupTracks"). '" class="jz_submit">';
		
		echo "</center>";
		echo '</form>';
		
		$this->closeBlock();
	}
	
	
	/**
	* Displays the full top played list
	* 
	* @author Ross Carlson, Ben Dodson
	* @version 01/27/05
	* @since 01/27/05
	* @param $node The node we are looking at
	*/
	function displayNodeStats($node){
		global $row_colors,$site_title,$jzUSER;
		

		if (!checkPermission($jzUSER,"admin",$node->getPath("String"))) {
		  echo word("Insufficient permissions.");
		  return;
		}

		$display = new jzDisplay();
		if ($node->getLevel() == 0) {
		  $this->displayPageTop("",word("Stats for"). ": " . $site_title);
		} else {
		  $this->displayPageTop("",word("Stats for"). ": ". $node->getName());
		}
		$this->openBlock();
		$stats = $node->getStats();
		$i=0;
		?>
		<table width="100%" cellpadding="5" cellspacing="0">
		   <?php if (distanceTo("artist",$node) !== false) { ?>
			<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
				<td width="40%">
					<?php echo word("Total Artists"); ?>:
				</td>
				<td width="60%">
		   <?php echo $stats['total_artists']; ?>
				</td>
			</tr>
			<?php } ?>
		   <?php if (distanceTo("album",$node) !== false) { ?>
			<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
				<td width="40%">
					<?php echo word("Total Albums"); ?>:
				</td>
				<td width="60%">
					<?php echo $stats['total_albums']; ?>
				</td>
			</tr>
				    <?php } ?>
			<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
				<td width="40%">
					<?php echo word("Total Tracks"); ?>:
				</td>
				<td width="60%">
				<?php echo $stats['total_tracks']; ?>
				</td>
			</tr>
			<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
				<td width="40%">
					<?php echo word("Total Size"); ?>:
				</td>
				<td width="60%">
				    <?php echo $stats['total_size_str']; ?>
				</td>
			</tr>
			<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
				<td width="40%">
					<?php echo word("Total Length"); ?>:
				</td>
				<td width="60%">
				    <?php echo $stats['total_length_str']; ?>
				</td>
			</tr>
			<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
				<td width="40%">
					<?php echo word("Total Plays"); ?>:
				</td>
				<td width="60%">
				    <?php echo $node->getPlaycount(); ?>
				</td>
			</tr><?php /* ?>
			<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
				<td width="40%">
					<?php echo word("Total Downloads"); ?>:
				</td>
				<td width="60%">
				    <?php echo $node->getDownloadCount(); ?>
				</td>
			</tr><?php */ ?>
				    <?php if (distanceTo("artist",$node) !== false) { ?>
			<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
				<td width="40%">
					<?php echo word("Most Played Artist"); ?>:
				</td>
				<td width="60%">
				    <?php $a = $node->getMostPlayed("nodes",distanceTo("artist",$node),1);
		if (sizeof($a) > 0) { echo $a[0]->getName(); } ?>
				</td>
			</tr>
				   <?php } ?>
		<?php if (distanceTo("album",$node) !== false) { ?>
			<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
				<td width="40%">
					<?php echo word("Most Played Album"); ?>:
				</td>
				<td width="60%">
				    <?php $a = $node->getMostPlayed("nodes",distanceTo("album",$node),1);
			if (sizeof($a) > 0) { 
			  if ($node->getPType() != "artist") {
			    echo getInformation($a[0],"artist") . " - " . $a[0]->getName(); 
			  }
			  else {
			    echo $a[0]->getName();
			  }
			} ?>
				</td>
			</tr>
				    <?php   } ?>
			<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
				<td width="40%">
					<?php echo word("Most Played Track"); ?>:
				</td>
				<td width="60%">
				    <?php $a = $node->getMostPlayed("tracks",-1,1);
		if (sizeof($a) > 0) { 
		  if ($node->getPType() != "artist") {
		    echo getInformation($a[0],'artist') . " - " . $a[0]->getName(); 
		  } else {
		    echo $a[0]->getName();
		  }
		} ?>
				</td>
			</tr>
			<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
				<td width="40%">
					<?php echo word("Average Track Length"); ?>:
				</td>
				<td width="60%">
					<?php echo convertSecMins($stats['avg_length']); ?>
				</td></tr>
<tr class="<?php echo $row_colors[$i]; $i = 1 - $i; ?>">
<td width="40%">
				    <?php echo word("Average Bitrate"); ?>:
</td>
<td width="60%">
<?php
				    echo round($stats['avg_bitrate'],0); 
?>
</td>
			</tr>
			<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
				<td width="40%">
					<?php echo word("Average Year"); ?>:
				</td>
				<td width="60%">
					<?php echo round($stats['avg_year'],0); ?>
				</td>
			</tr>
		</table>
		<br><center>
		<?php $this->closeButton(); ?>
		</center>
		<?php
		
		// Now let's get the stats
		
		
		$this->closeBlock();
	}
	
	/**
	* Displays the full top played list
	* 
	* @author Ross Carlson
	* @version 01/27/05
	* @since 01/27/05
	* @param $node The node we are looking at
	*/
	function displayTopStuff($node){
		global $img_tiny_play, $album_name_truncate, $root_dir; 
		
		$display = new jzDisplay();
		
		// First let's display the top of the page and open the main block
		$title = "Top ";
		switch ($_GET['tptype']){
			case "played-albums":
				$limit = 50;
				$title .= $limit. " ". word("Played Albums");
				$type = "album";
				$func = "getMostPlayed";
			break;
			case "played-artists":
				$limit = 50;
				$title .= $limit. " ". word("Played Artists");
				$type = "artist";
				$func = "getMostPlayed";
			break;
		        case "played-tracks":
				$limit = 50;
				$title .= $limit. " ". word("Played Tracks");
				$type = "track";
				$func = "getMostPlayed";
			break;
			case "downloaded-albums":
				$limit = 50;
				$title .= $limit. " ". word("Downloaded Albums");
				$type = "album";
				$func = "getMostDownloaded";
			break;
			case "new-albums":
				$limit = 100;
				$title .= $limit. " ". word("New Albums");
				$type = "album";
				$func = "getRecentlyAdded";
			break;
		        case "new-artists":
		                $limit = 100;
				$title .= $limit. " ". word("New Artists");
				$type = "artist";
				$func = "getRecentlyAdded";
			break;
		        case "new-tracks":
				$limit = 100;
				$title .= $limit. " ". word("New Tracks");
				$type = "track";
				$func = "getRecentlyAdded";
			break;
		case "recentplayed-albums":
		  $limit = 50;
		  $title .= $limit. " ". word("Played Albums");
		  $type = "album";
		  $func = "getRecentlyPlayed";
		  break;
		case "recentplayed-artists":
		  $limit = 50;
		  $title .= $limit. " ". word("Played Artists");
		  $type = "artist";
		  $func = "getRecentlyPlayed";
		  break;
		case "recentplayed-albums":
		  $limit = 50;
		  $title .= $limit. " ". word("Played Albums");
		  $type = "album";
		  $func = "getRecentlyPlayed";
		  break;
		case "recentplayed-tracks":
		  $limit = 50;
		  $title .= $limit. " ". word("Played Tracks");
		  $type = "track";
		  $func = "getRecentlyPlayed";
		  break;
		case "toprated-artists":
		  $limit = 50;
		  $title .= $limit. " ". word("Rated Artists");
		  $type = "artist";
		  $func = "getTopRated";
		  break;
		case "toprated-albums":
		  $limit = 50;
		  $title .= $limit. " ". word("Rated Albums");
		  $type = "album";
		  $func = "getTopRated";
		  break;
		case "topviewed-artists":
		  $limit = 50;
		  $title .= $limit. " ". word("Viewed Artists");
		  $type = "artist";
		  $func = "getMostViewed";
		  $showCount = "view";
		  break;



		}
		$this->displayPageTop("",$title);
		$this->openBlock();
		
		// Now let's get the recently added items
		if ($type == "track") {
		  $retType = "tracks";
		} else {
		  $retType = "nodes";
		}
		$recent = $node->$func($retType,distanceTo($type,$node),$limit);
		
		// Now let's loop through the results
		for ($i=0;$i<count($recent);$i++){
			// Now let's create our node and get the properties
			$item = $recent[$i];
			$album = $item->getName();
			$parent = $item->getParent();
			$artist = $parent->getName();
			
			// Now let's create our links
			$albumArr['jz_path'] = $item->getPath("String");
			$artistArr['jz_path'] = $parent->getPath("String");
			
			// Now let's create our short names
			$artistTitle = returnItemShortName($artist,$album_name_truncate);
			$albumTitle = returnItemShortName($album,$album_name_truncate);													
			
			// Now let's display it
			echo "<nobr>";
			$display->playLink($item, $img_tiny_play, $album);
			
			// Now let's set the hover code
			$innerOver = "";
			if (($art = $item->getMainArt()) <> false) {
				$innerOver .= $display->returnImage($art,$item->getName(),75,75,"limit",false,false,"left","3","3");
			}
			$desc_truncate = 200;
			$desc = $item->getDescription();
			$innerOver .= $display->returnShortName($desc,$desc_truncate);
			if (strlen($desc) > $desc_truncate){
				$innerOver .= "...";
			}
			$innerOver = str_replace('"',"",$innerOver);
			$innerOver = str_replace("'","",$innerOver);
			
			// Now let's return our tooltip													
			$capTitle = $artist. " - ". $album;
			$overCode = $display->returnToolTip($innerOver, $capTitle);
			echo ' <a onClick="opener.location.href=\''. urlize($albumArr) . '\';window.close();" '. $overCode. 'href="javascript:void()">'. $albumTitle;	
			$cval = false;
			// TODO: showCount values can be:
			// view,dowload,play
			if ($showCount == "view") {
			  $cval = $item->getViewCount();
			} else {
			  $cval = $item->getPlayCount();
			}
			if ($cval !== false && $cval <> 0){
				echo ' ('. $cval. ')';
			}
			echo "</a><br>";
			// Now let's set the hover code
			//echo ' <a title="'. $artist. ' - '. $album. '" href="'. urlize($albumArr). '">'. $albumTitle. '</a> ('. $albumPlayCount. ')';
			//echo "<br>";
			echo "</nobr>";
			flushdisplay();
		}
		
		$this->closeBlock();
	}
	
	/**
	* Shows the user manager
	* 
	* @author Ross Carlson
	* @version 01/25/05
	* @since 01/25/05
	* @param $node The node we are looking at
	*/
	function displayTrackInfo($track){
		global $row_colors, $jzSERVICES, $jzUSER, $lame_opts, $root_dir, $allow_filesystem_modify, $allow_id3_modify,$backend,$short_date;
		if (is_string($track)) {
		  $track = new jzMediaTrack($track);
		}
		
		// Ok, now we need to see if they did something with this form
		if (isset($_POST['justclose'])){
			$this->closeWindow(false);
		}
		if (isset($_POST['closeupdate']) or isset($_POST['updatedata'])){
			// Ok, they are updating this track so let's do it
			// Let's create our object so we can get the full path and meta
			$track = new jzMediaTrack($_GET['jz_path']);
			$fname = $track->getDataPath("String");

			if ($allow_id3_modify == "true") {
				$fileAvailable = @fopen( $fname, 'r+' );
				if( !$fileAvailable ) {
					$writeback_message = word("Could not write to file %s. This track's ID3 tag has not been modified.'", $fname);
				} else {
					$writeback_message = word("Metadata for %s has been stored in Jinzora and this file's ID3 tag.'", $track->getName());
				}
			} else {
				$writeback_message = word("Metadata for %s has been updated in Jinzora. To update ID3 tags, please enable \$allow_id3_modify.", $track->getName());
			}

			$meta = $track->getMeta();
			
			// Now we need to set the meta we want to rewrite
			$meta['title'] = $_POST['edit_title'];
			$meta['artist'] = $_POST['edit_artist'];
			$meta['album'] = $_POST['edit_album'];
			$meta['number'] = $_POST['edit_number'];
			$meta['genre'] = $_POST['edit_genre'];
			$meta['comment'] = $_POST['edit_comment'];
			$meta['lyrics'] = $_POST['edit_lyrics'];
			
			// Now let's write this
			$track->setMeta($meta);

			// Now let's write the long description if they had one
			$track->addDescription($_POST['edit_long_desc']);
			$track->addShortDescription($_POST['edit_comment']);
			
			// Now let's update the play count
			$track->setPlayCount($_POST['edit_plays']);
			
			// Now let's update the cache
			//$path = $track->getPath();
			//unset($path[count($path)-1]);
			//$path = implode("/",$path);
			//$node = new jzMediaNode($path);
			//$node->updateCache(true, false, false, true);
			
			// Now do we need to close out?
			if (isset($_POST['closeupdate'])){
				$this->closeWindow(true);
			}
		}		
		if (isset($_POST['createlowfi'])){
			// First let's display the top of the page and open the main block
			$this->displayPageTop("","Resampling Track");
			$this->openBlock();
			
			// Let's get all the data we'll need
			$oArr = explode("/",$track->getDataPath("String"));
			$oldName = $oArr[count($oArr)-1];
			unset($oArr[count($oArr)-1]);
			$path = implode("/",$oArr);
			$oldPath = $track->getDataPath("String");
			
			if (!stristr($oldName,".mp3") or stristr($oldName,".clip.")){continue;}
				$error = "Lo-fi file create failed!";
				// Now let's encode
				$newName = substr($oldName,0,-3). 'lofi.mp3';
				$newPath = $path. "/". $newName;
				$command = $lame_opts. ' "'. $oldPath. '" "'. $newPath. '"';
				// Ok, let's give them status
				echo "<center>". word("Resampling track, please stand by..."). "<br><br>";
				echo '<img src="'. $root_dir. '/style/images/convert.gif?'. time(). '"></center><br>';
				echo '<div id="path"></div>';
				echo '<div id="oldname"></div>';
				echo '<div id="newname"></div>';
				echo '<div id="status"></div>';
				flushdisplay();
				?>
				<SCRIPT LANGUAGE=JAVASCRIPT><!--\
					p = document.getElementById("path");
					o = document.getElementById("oldname");
					n = document.getElementById("newname");
					s = document.getElementById("status");
					p.innerHTML = '<nobr><?php echo word("Path"); ?>: <?php echo $path; ?></nobr>';
					o.innerHTML = '<nobr><?php echo word("Old Name"); ?>: <?php echo $oldName; ?></nobr>';
					n.innerHTML = '<nobr><?php echo word("New Name"); ?>: <?php echo $newName; ?></nobr>';
					s.innerHTML = '<nobr><?php echo word("Status: creating..."); ?></nobr>';
					-->
				</SCRIPT>
				<?php
				flushdisplay();
				$output = "";
				$returnvalue = "";
				if (exec($command, $output, $returnvalue)){
					?>
					<SCRIPT LANGUAGE=JAVASCRIPT><!--\
						s = document.getElementById("status");
						s.innerHTML = '<nobr><?php echo word("Status: updating tags..."); ?></nobr>';
						-->
					</SCRIPT>
					<?php
					flushdisplay();
					// Now we need to get the meta data from the orginal file so we can write it to the new file
					$tMeta = new jzMediaTrack($track->getPath("String"));
					$meta = $tMeta->getMeta();
					// Now let's write this
					$jzSERVICES->setTagData($newPath, $meta);
					?>
					<SCRIPT LANGUAGE=JAVASCRIPT><!--\
						p = document.getElementById("path");
						o = document.getElementById("oldname");
						n = document.getElementById("newname");
						s = document.getElementById("status");
						p.innerHTML = '<?php echo word("Complete!"); ?>';
						o.innerHTML = '&nbsp;';
						n.innerHTML = '&nbsp;';
						s.innerHTML = '&nbsp;';
						-->
					</SCRIPT>
					<?php
				}
			echo '<br><br><center>';
			$this->closeButton();
			exit();
		}
		if (isset($_POST['createclip'])){
			exit();
		}		
		
		// Ok, now we need to create an object from the path so we can read its data
		$fname = $track->getDataPath("String");
		//$meta = $jzSERVICES->getTagData($fname);
		$meta = $track->getMeta();
		
		// First let's display the top of the page and open the main block
		$this->displayPageTop("",word("Track Details"). ": ". $meta['title']);
		$this->openBlock();
		if (checkPermission($jzUSER,'admin',$track->getPath("String"))) {
		  if ($allow_id3_modify == "false" && !isset($writeback_message)) {
		    echo '<p><i>' . word("Note: You must have allow_id3_modify enabled if you want Jinzora to manage your ID3 tags.") . '</i></p><br>';
		  }
		  if (isset($writeback_message)) {
		  	echo '<p><b>' . $writeback_message . "</b></p><br>";
		  }
		}
		// Now let's display the details
		$i=1;
		$arr = array();
		$arr['action'] = "popup";
		$arr['ptype'] = "trackinfo";
		$arr['jz_path'] = $_GET['jz_path'];
		echo '<form action="'. urlize($arr). '" method="POST">';
		?>
		<table width="100%" cellpadding="5" cellspacing="0" border="0">
			<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
				<td width="30%" valign="top">
					<nobr>
						<?php echo word('File Name'); ?>:
					</nobr>
				</td>
				<td width="70%" valign="top">
					<?php echo $meta['filename']; ?>
				</td>
			</tr>
			<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
				<td width="30%" valign="top">
					<nobr>
						<?php echo word('Track Number'); ?>:
					</nobr>
				</td>
				<td width="70%" valign="top">
					<input type="input" class="jz_input" name="edit_number" value="<?php echo $meta['number']; ?>" size="3">
				</td>
			</tr>
			<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
				<td width="30%" valign="top">
					<nobr>
						<?php echo word('Track Name'); ?>:
					</nobr>
				</td>
				<td width="70%" valign="top">
					<input type="input" class="jz_input" name="edit_title" value="<?php echo $meta['title']; ?>" size="30">
				</td>
			</tr>
			<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
				<td width="30%" valign="top">
					<nobr>
						<?php echo word('Artist'); ?>:
					</nobr>
				</td>
				<td width="70%" valign="top">
					<input type="input" class="jz_input" name="edit_artist" value="<?php echo $meta['artist']; ?>" size="30">
				</td>
			</tr>
			<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
				<td width="30%" valign="top">
					<nobr>
						<?php echo word('Album'); ?>:
					</nobr>
				</td>
				<td width="70%" valign="top">
					<input type="input" class="jz_input" name="edit_album" value="<?php echo $meta['album']; ?>" size="30">
				</td>
			</tr>
			<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
				<td width="30%" valign="top">
					<nobr>
						<?php echo word('Genre'); ?>:
					</nobr>
				</td>
				<td width="70%" valign="top">
					<input type="input" class="jz_input" name="edit_genre" value="<?php echo $meta['genre']; ?>" size="30">
				</td>
			</tr>
			<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
				<td width="30%" valign="top">
					<nobr>
						<?php echo word('Track Length'); ?>:
					</nobr>
				</td>
				<td width="70%" valign="top">
					<?php echo convertSecMins($meta['length']); ?>
				</td>
			</tr>
			<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
				<td width="30%" valign="top">
					<nobr>
						<?php echo word('Bit Rate'); ?>:
					</nobr>
				</td>
				<td width="70%" valign="top">
					<?php echo $meta['bitrate']; ?> kbps
				</td>
			</tr>
			<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
				<td width="30%" valign="top">
					<nobr>
						<?php echo word('Sample Rate'); ?>:
					</nobr>
				</td>
				<td width="70%" valign="top">
					<?php echo $meta['frequency']; ?> kHz
				</td>
			</tr>
			<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
				<td width="30%" valign="top">
					<nobr>
						<?php echo word('File Size'); ?>:
					</nobr>
				</td>
				<td width="70%" valign="top">
					<?php echo $meta['size']; ?> Mb
				</td>
			</tr>
			<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
				<td width="30%" valign="top">
					<nobr>
						<?php echo word('File Date'); ?>:
					</nobr>
				</td>
				<td width="70%" valign="top">
					<?php echo date($short_date,$track->getDateAdded()); ?>
				</td>
			</tr>
			<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
				<td width="30%" valign="top">
					<nobr>
						<?php echo word('ID3 Description'); ?>:
					</nobr>
				</td>
				<td width="70%" valign="top">
					<input type="input" class="jz_input" name="edit_comment" value="<?php echo $meta['comment']; ?>" size="30">
				</td>
			</tr>
			<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
				<td width="30%" valign="top">
					<nobr>
						<?php echo word('Thumbnail'); ?>:
					</nobr>
				</td>
				<td width="70%" valign="top">
					<input type="file" class="jz_input" name="edit_thumbnail" size="22">
				</td>
			</tr>
			<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
				<td width="30%" valign="middle">
					<nobr>
						<?php echo word('Long Description'); ?>:
					</nobr>
				</td>
				<td width="70%" valign="top">
					<textarea class="jz_input" name="edit_long_desc" style="width: 195px" rows="5"><?php echo $track->getDescription($_POST['edit_long_desc']); ?></textarea>
				</td>
			</tr>
			<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
				<td width="30%" valign="top">
					<nobr>
						<?php echo word('Plays'); ?>:
					</nobr>
				</td>
				<td width="70%" valign="top">
					<input type="input" class="jz_input" name="edit_plays" value="<?php echo $track->getPlayCount(); ?>" size="3">
				</td>
			</tr>
			<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
				<td width="100%" valign="top" colspan="2" align="center">
					<?php
						if ($meta['lyrics']){
							echo '<div align="left">'.word('Lyrics').':</div><textarea name="edit_lyrics" class="jz_input" rows="20" cols="45" class="jz_input">'. $meta['lyrics']. '</textarea>';
						} else {
						  $lyrics = $jzSERVICES->getLyrics($track);
						  if (!(($lyrics === false) || ($lyrics == ""))) {
						    $meta2 = array();
						    $meta2['lyrics'] = $lyrics;
						    $track->setMeta($meta2);
						    echo '<div align="left">'.word('Lyrics').':</div><textarea name="edit_lyrics" class="jz_input" rows="20" cols="45" class="jz_input">'. $lyrics . '</textarea>';  
						  } else if (checkPermission($jzUSER,"admin",$track->getPath("String"))) {
						    echo '<div align="left">'.word('Lyrics').':</div><textarea name="edit_lyrics" class="jz_input" rows="20" cols="45" class="jz_input"></textarea>';  
						  }
						  
						}
					?>
				</td>
			</tr>
			<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
				<td width="100%" valign="top" colspan="2" align="center">
				    <?php if (checkPermission($jzUSER,"admin",$track->getPath("String"))) {?>
						<input type=submit class="jz_submit" name="<?php echo jz_encode('closeupdate'); ?>" value="<?php echo word('Update & Close'); ?>">
						<input type=submit class="jz_submit" name="<?php echo jz_encode('updatedata'); ?>" value="<?php echo word('Update'); ?>">
						<br><br>
						<?php
							// We can only do this if they allow filesystem modify
							if ($allow_filesystem_modify == "true"){
						?>
						<input type=submit class="jz_submit" name="<?php echo jz_encode('createlowfi'); ?>" value="<?php echo word('Create Lo-Fi'); ?>">  
						<input type=submit class="jz_submit" name="<?php echo jz_encode('createclip'); ?>" value="<?php echo word('Create Clip'); ?>"> 
						<br><br>
						<?php } ?>
					<?php } ?>
					<input type=submit class="jz_submit" name="<?php echo jz_encode('justclose'); ?>" value="<?php echo word('Close'); ?>">
					<br><br>
				</td>
			</tr>
		</table>
		</form>
		<?php
		
		$this->closeBlock();
	}

	/**
	* Shows the user manager
	* 
	* @author Ross Carlson, Ben Dodson
	* @version 2/20/05
	* @since 01/19/05
	*/
	function userManager(){
		global $resampleRates,$frontend,$jinzora_skin,$include_path,$jzUSER,$jzSERVICES,$cms_mode;
		
		$ucount = sizeof($jzUSER->listUsers());
		$display = new jzDisplay();
		$be = new jzBackend();
		// First let's display the top of the page and open the main block
		$this->displayPageTop("",word("User Manager (%s users)", $ucount));
		$this->openBlock();
		
		if ($jzUSER->getSetting('admin') === false) {
		  echo word("Insufficient permissions");
		  $this->closeBlock();
		  return;
		}
		// Make a menu button for later:
		$urla = array();
		$urla['action'] = "popup";
		$urla['ptype'] = "usermanager";
		
		$MENU_BUTTON =  '<form method="POST" action="'.urlize($urla).'">';
		$MENU_BUTTON .= '<input type="submit" class="jz_submit" name="menu" value="'.word('Menu').'">';
		$MENU_BUTTON .= '</form>';

		// Different features:
		if (!isset($_GET['subaction'])) {
		  echo '<table>';

			 echo '<tr><td>';
		  $urla['subaction'] = "adduser";
		  echo '<a href="' . urlize($urla) . '">'. word("Add a user"). '</a>';
		  echo "</td></tr>";

		if ($ucount > 2) {
		    echo '<tr><td>';
		    $urla['subaction'] = "edituser";
		    echo '<a href="' . urlize($urla) . '">'. word("Modify a user"). '</a>';
		    echo "</td></tr>";
		  }

 		if ($ucount > 2) {
		    echo '<tr><td>';
		    $urla['subaction'] = "removeuser";
		    echo '<a href="' . urlize($urla) . '">'. word("Remove a user"). '</a>';
		    echo "</td></tr>";
		  }
		  
		  echo '<tr><td>';
		  $urla['subaction'] = "editclasses";
		  echo '<a href="' . urlize($urla) . '">'. word("Modify user templates"). '</a>';
		  echo "</td></tr>";		 

		  echo '<tr><td>';
		  $urla['subaction'] = "default_access";
		  echo '<a href="' . urlize($urla) . '">'. word("Edit default access"). '</a>';
		  echo "</td></tr>";
  		    

		  echo '<tr><td>';
		  $urla['subaction'] = "registration";
		  echo '<a href="' . urlize($urla) . '">'. word("Edit self-registration"). '</a>';
		  echo "</td></tr>";
		  
		 

		  echo '</table>';
		  $this->closeBlock();
		  return;
		}

		// HANDLE USER CHANGES:
		if ($_GET['subaction'] == "handleuser") {
		  // Now, did they submit the form?
		  if ($_POST['field1'] != $_POST['field2']) {
		      echo word("Error: Password mismatch");
		      return;
		    }
		}
		if ($_GET['subaction'] == "handleuser" && ($_POST['templatetype'] == "customize") && !isset($_POST['usr_interface'])) {
			 if ($_GET['usermethod'] == "add") {
		      $myid = $jzUSER->addUser($_POST['username'],$_POST['field1']);
		      if ($myid === false) {
						echo word("Could not add user"). " " . $_POST['username'] . ".";
						return;
		      }
		    } else {
		      $myid = $_POST['user_to_edit'];
		      if ($_POST['field1'] != "jznoupd") {
						// update password
						$jzUSER->changePassword($_POST['field1'],$jzUSER->lookupName($myid));
		      }
		      // change name
		      if (($oldname = $jzUSER->lookupName($myid)) != $_POST['username']) {
						$jzUSER->changeName($_POST['username'],$oldname);
		      }
		    }
		  if ($_POST['userclass'] == "jznewtemplate") {
		    $settings = array();
		    if (isset($_POST['user_to_edit'])) {
		      $settings = $jzUSER->loadSettings($_POST['user_to_edit']);
		    } else {
		      $settings = array();
		    }
		  } else {
		    $classes = $be->loadData('userclasses');
		    $settings = $classes[$_POST['userclass']];
		  }
		  $post = $_POST;
		  unset($post['handleUser']);
		  $this->userManSettings("custom",$settings, 'handleuser', $post);
		  return;
		}

		if ($_GET['subaction'] == "handleuser") {
		    if ($_GET['usermethod'] == "add") {
		      $myid = $jzUSER->addUser($_POST['username'],$_POST['field1']);
		      if ($myid === false) {
						echo word("Could not add user"). " " . $_POST['username'] . ".";
						return;
		      }
		    } else {
		    		if (isset($_POST['user_to_edit'])) {
		    			$myid = $_POST['user_to_edit'];
		    		} else {
						$list = $jzUSER->listUsers();
						foreach ($list as $id=>$name) {
							if ($name == $_POST['username']) {
								$myid = $id;
							}
						} 
		    		}
		    		if (!isset($myid)) {
                    	// okay, yes,  this is the worst piece of code in Jinzora.
                        $myid = $jzUSER->lookupUID(NOBODY);
                    }
					
		      if ($_POST['field1'] != "jznoupd") {
						// update password
						$jzUSER->changePassword($_POST['field1'],$jzUSER->lookupName($myid));
		      }
		      // change name
		      if (($oldname = $jzUSER->lookupName($myid)) != $_POST['username']) {
						$jzUSER->changeName($_POST['username'],$oldname);
		      }
		    }
		    $wipe = false;
		    // DETACH
		    if ($_POST['templatetype'] == "detach") {
		      if ($_POST['userclass'] == "jznewtemplate") {
						echo "Cannot base a user only on a blank template.";
						return;
		      } else {
						$classes = $be->loadData('userclasses');
						$settings = $classes[$_POST['userclass']];
						$settings['template'] = "";
						$wipe = true;
		      }
		      // STICKY
		    } else if ($_POST['templatetype'] == "sticky") {
		      if ($_POST['userclass'] == "jznewtemplate") {
						echo "Cannot stick user to a blank template.";
						return;
		      } else {
						$settings = array();
						$settings['template'] = $_POST['userclass'];
						$wipe = true;
		      }
		      // CUSTOMIZE
		    } else if ($_POST['templatetype'] == "customize") {
		      $settings = $this->userPullSettings();
		      $settings['template'] = "";
		    } else {
		      echo "Sorry, I don't know how to manage the user.";
		      return;
		    }
		    
		    $un = ($_POST['username'] != "") ? $_POST['username'] : word('anonymous');
			if (isset($settings['home_dir'])) {
		    	$settings['home_dir'] = str_replace('USERNAME',$un,$settings['home_dir']);
			}
				
		    $jzUSER->setSettings($settings,$myid, $wipe);
		    		    
		    echo word("User"). ": ". $un ." ". word("updated");
				echo "<br><br><center>";
				echo $MENU_BUTTON . '&nbsp;';
				$this->closeButton();
		    $this->closeBlock();
		    return;


		} else if ($_GET['subaction'] == "handleclass") {
		  $settings = $this->userPullSettings();
		    $classes = $be->loadData("userclasses");
		    if (!is_array($classes)) {
		      $classes = array();
		    }
		    $classes[$_POST['classname']] = $settings;
		    $be->storeData('userclasses',$classes);

		    echo word("Template updated.");
				echo "<br><br><center>";
				echo $MENU_BUTTON . '&nbsp;';
				$this->closeButton();
		    $this->closeBlock();
		    return;
		}
		/** ** ** ** **/
		// USER CLASS MANAGER
		//
		
		if ($_GET['subaction'] == "editclasses") {
		  $urla = array();
		  $urla['action'] = "popup";
		  $urla['ptype'] = "usermanager";
		  $urla['subaction'] = "editclasses";

		  if (!isset($_GET['subsubaction'])) {
		    echo '<table>';		    
		    echo '<tr><td>';
		    $urla['subsubaction'] = "add";
		    echo '<a href="' . urlize($urla) . '">'. word("Add a template"). '</a>';
		    echo "</td></tr>";

		    $classes = $be->loadData('userclasses');
		    if (!(!is_array($classes) || sizeof($classes) == 0)) {
		      echo '<tr><td>';
		      $urla['subsubaction'] = "edit";
		      echo '<a href="' . urlize($urla) . '">'. word("Edit a template"). '</a>';
		      echo "</td></tr>";
		    
		      echo '<tr><td>';
		      $urla['subsubaction'] = "remove";
		      echo '<a href="' . urlize($urla) . '">'. word("Remove a template"). '</a>';
		      echo "</td></tr>";
		    }
		    echo '</table>';

		  } else if ($_GET['subsubaction'] == "edit") {
		    $urla['subsubaction'] = "edit2";
		    echo '<table>';
		    echo '<form method="POST" action="'.urlize($urla).'">';
		    ?><input type="hidden" name="update_settings" value="true"><?php
		    echo '<tr><td>'.word("Template:");
		    echo '</td><td>';
		    echo '<select name="classname" class="jz_select">';
		    $classes = $be->loadData('userclasses');
		    $keys = array_keys($classes);
		    foreach ($keys as $key) {
		      echo '<option value="'.$key.'">'.$key;
		    }
		    echo '</select>';
		    echo '</td></tr>';
		    echo '<tr colspan="2"><td>';
		    echo '<input type="submit" class="jz_submit" name="submit" value="'.word('Go').'">';
		    echo '</td></tr></form></table>';


		  } else if ($_GET['subsubaction'] == "add" || $_GET['subsubaction'] == "edit2") {
		    if ($_GET['subsubaction'] == "add") {
		      $settings = array();
		      $settings['view'] = true;
		      $settings['stream'] = true;
		      $settings['powersearch'] = true;
		      $settings['edit_prefs'] = true;
		      $settings['frontend'] = $frontend;
		      $settings['theme'] = $jinzora_skin;
		      $settings['language'] = "english";
		      $settings['playlist_type'] = "m3u";		      
		      $this->userManSettings("new",$settings);
		    } else {
		      $classes = $be->loadData('userclasses');
		      if (!isset($classes[$_POST['classname']])) {
						die("Invalid user template.");
		      }
		      $settings = $classes[$_POST['classname']];
		      $this->userManSettings("update",$settings);
		    }

		  } else if ($_GET['subsubaction'] == "remove") {
		    if (!isset($_POST['class_to_remove'])) {
		    $list = $jzUSER->listUsers();
		    $url_array = array();
		    $url_array['action'] = "popup";
		    $url_array['ptype'] = "usermanager";
		    $url_array['subaction'] = "editclasses";
		    $url_array['subsubaction'] = "remove";

		    echo '<form action="' . urlize($url_array) . '" method="POST">';
		    echo '<input type="hidden" name="update_settings" value="true">';
		    echo word("Template:") . '&nbsp';
		    echo '<select name="class_to_remove" class="jz_input">';
		    $classes = $be->loadData('userclasses');
		    $keys = array_keys($classes);
		    foreach ($keys as $key) {
		      if ($key != NOBODY) {
						echo '<option value="' . $key . '">' . $key . '</option>';
		      }
		    }
		    echo "</select>";
		    echo '&nbsp;<input type="submit" class="jz_submit" value="Go">';
		    echo '</form>';
		  } else {
		    $classes = $be->loadData('userclasses');
		    unset($classes[$_POST['class_to_remove']]);
		    $be->storeData('userclasses',$classes);
		    echo $_POST['class_to_remove'] . word(" has been removed.");
		    echo '<br><br><center>';
		    echo $MENU_BUTTON;
		    echo '&nbsp;';
		    $this->closeButton();
		  }
		  $this->closeBlock();
		  return;
		  }
		  
		  
		  $this->closeBlock();
		  return;
		}

		// SELF-REGISTRATION:
		if ($_GET['subaction'] == "registration") {
		  $be = new jzBackend();
		  $data = $be->loadData('registration');
		  if (!is_array($data)) {
		    $data = array();
		  }
		  $classes = $be->loadData('userclasses');
		  
		  if (!is_array($classes)) {
		  	$urla = array();
			$urla['action'] = "popup";
			$urla['ptype'] = "usermanager";
			$urla['subaction'] = "editclasses";
			$urla['subsubaction'] = "add";
		  	echo word("<p>You must set up a user template before enabling user registration.</p>");
		  	echo '<p><a href="' . urlize($urla) . '">' . word("Click here to do add a user template.") . '</a></p>';
		  	
		  	return;
		  }
		  
		  if (isset($_POST['update_postsettings'])) {
		    echo word("Settings updated"). "<br>";
		  }
		  $page_array = array();
		  $page_array['action'] = "popup";
		  $page_array['ptype'] = "usermanager";
		  $page_array['subaction'] = "registration";
		  $display->openSettingsTable(urlize($page_array));
		  $display->settingsCheckbox(word("Allow Self-Registration"). ":",'allow_registration',$data);

		  
		  $keys = array_keys($classes);
		  $display->settingsDropdown(word("User Template:"),'classname',$keys,$data);

		  $display->closeSettingsTable(true);
		  if (isset($_POST['update_postsettings'])) {
		    $be->storeData('registration',$data);
		  }
		  return;
		}


		// * * * * * * * * //
		// ANONYMOUS USER SUBSECTION
		// * * * * * * * * //
		if ($_GET['subaction'] == "default_access") {
		  $_GET['subaction'] = "edituser";
		  $_POST['user_to_edit'] = $jzUSER->lookupUID(NOBODY);
		}

		// * * * * * * * * //
		// EDIT USER SUBSECTION
		// * * * * * * * * //
		if ($_GET['subaction'] == "edituser") {
		  if (!isset($_POST['user_to_edit'])) {
		    $list = $jzUSER->listUsers();
		    $my_id = $jzUSER->getID();
		    $url_array = array();
		    $url_array['action'] = "popup";
		    $url_array['ptype'] = "usermanager";
		    $url_array['subaction'] = "edituser";
		    echo '<form action="' . urlize($url_array) . '" method="POST">';
		    echo '<input type="hidden" name="update_settings" value="true">';
		    echo word("User"). ": ";
		    echo '<select name="user_to_edit" class="jz_input">';
		    foreach ($list as $id=>$name) {
		      if ($name != NOBODY && $id != $my_id) {
						echo '<option value="' . $id . '">' . $name . '</option>';
		      }
		    }
		    echo "</select>";
		    echo '&nbsp;<input type="submit" class="jz_submit" value="Go">';
		    echo '</form>';
		    return;
		  }
		}


		if ($_GET['subaction'] == "removeuser") {
		  if (!isset($_POST['user_to_remove'])) {
		    $list = $jzUSER->listUsers();
		    $url_array = array();
		    $url_array['action'] = "popup";
		    $url_array['ptype'] = "usermanager";
		    $url_array['subaction'] = "removeuser";
		    echo '<form action="' . urlize($url_array) . '" method="POST">';
		    echo '<input type="hidden" name="update_settings" value="true">';
		    echo word("User"). ": ";
		    echo '<select name="user_to_remove" class="jz_input">';
		    foreach ($list as $id=>$name) {
		      if ($name != NOBODY) {
						echo '<option value="' . $id . '">' . $name . '</option>';
		      }
		    }
		    echo "</select>";
		    echo '&nbsp;<input type="submit" class="jz_submit" value="Go">';
		    echo '</form>';
		  } else {
		    $name = $jzUSER->lookupName($_POST['user_to_remove']);
		    $jzUSER->removeUser($_POST['user_to_remove']);
		    echo $name . word(" has been removed.");
		    echo '<br><br><center>';
		    echo $MENU_BUTTON . '&nbsp;';
		    $this->closeButton();
		  }
		  $this->closeBlock();
		  return;
		}

		// * * * * * * * * * * * //
		// ADD A USER SUBSECTION
		// * * * * * * * * * * * //
		// Let's show the form for this
		    $url_array = array();
		    $url_array['action'] = "popup";
		    $url_array['ptype'] = "usermanager";
		    $url_array['subaction'] = "handleuser";
		    
		    if (!isset($_POST['user_to_edit'])) {
		      $url_array['usermethod'] = "add";
		      $edit_guest = false;
		      $mid = $jzUSER->lookupUID(NOBODY);
		    } else {
		      $url_array['usermethod'] = "update";
		      if ($_POST['user_to_edit'] == $jzUSER->lookupUID(NOBODY)) {
						$edit_guest = true;
						$mid = $_POST['user_to_edit'];
		      } else {
						$edit_guest = false;
						$mid = $_POST['user_to_edit'];
		      }
		    }
		    
		    $jzUSER2 = new jzUser(false,$mid);

		    if ($_GET['subaction'] == "adduser") {
		      // set some settings manually.
		      $jzUSER2->settings['view'] = true;
		      $jzUSER2->settings['stream'] = true;
		      $jzUSER2->settings['lofi'] = true;
		    }
		    echo '<form action="'. urlize($url_array). '" method="POST">';

		    if (isset($_POST['user_to_edit'])) {
		      echo '<input type="hidden" name="user_to_edit" value="' . $_POST['user_to_edit'] . '">';
		    }
		      ?>
		      <input type="hidden" name="update_settings" value="true">
			 <table width="100%" cellpadding="3">
			 <?php if ($edit_guest === false) { ?>
			 <tr>
			 <td width="30%" align="right">
			 Username:
		    </td>
			 <td width="70%">
			<?php
			if ($_GET['subaction'] == "adduser") {
				// Now let's return our tooltip													
				?>
				<input type="input" name="username" class="jz_input">
				<?php
			} else {
				?>
				<input type="input" name="username" class="jz_input" value="<?php echo $jzUSER2->getName(); ?>">
				<?php
			}
			?>
			 </td>
			 </tr><?php if ($cms_mode == "false") { ?>
			 <tr>
			 <td width="30%" valign="top" align="right">
			 <?php echo word("Password"); ?>:
		    </td>
			 <td width="70%">
			<?php
			 if ($_GET['subaction'] == "adduser") {
				 ?>
				 <input type="password" name="field1" class="jz_input"><br>
				 <input type="password" name="field2" class="jz_input">
				 <?php
			 } else {
				 ?>
				 <input type="password" name="field1" class="jz_input" value="jznoupd"><br>
				 <input type="password" name="field2" class="jz_input" value="jznoupd">
				 <?php
			 }
?>
			 </td>
			 </tr><?php } else { ?> <input type="hidden" name="field1" value="jznoupd"> <?php } ?>
			 <tr>
			 <td width="30%" valign="top" align="right">
			 <?php echo word("Full Name"); ?>:
			</td>
			 <td width="70%">
			 <input type="input" name="fullname" class="jz_input" value="<?php echo $jzUSER2->getSetting('fullname'); ?>">
			 </td>
			 </tr>
			 <tr>
			 <td width="30%" valign="top" align="right">
			 <?php echo word("Email Address"); ?>:
			</td>
			 <td width="70%">
			 <input type="input" name="email" class="jz_input" value="<?php echo $jzUSER2->getSetting('email'); ?>">
			 </td>
			 </tr>
			 <?php } // the above is not available for the guest. ?>
			 <tr>
			    <td width-"30%" valign="top" align="right">
			    <?php echo word("Template:"); ?>
			    </td>
			    <td width="70%">
						<?php
							echo '<select name="userclass" class="jz_select">';
							echo "<option value=\"jznewtemplate\">".word('Blank Template');
							$classes = $be->loadData('userclasses');
							if (is_array($classes)){
								$keys = array_keys($classes);
								$set = $jzUSER2->loadSettings();
								if (isset($set['template'])) {
									$t = $set['template'];
								} else {
									$t = $keys[0];
								}
								foreach ($keys as $key) {
									echo "<option value=\"$key\"";
									if ($key == $t) {
										echo ' SELECTED';
									}
									echo ">$key";
								}
							}
							?>
		      </select>
			  </td></tr>
			  <tr><td width="30%" valign="top" align="right"><?php echo word('Management:'); ?></td><td>
		          <input type="radio" name="templatetype" value="sticky"><?php echo word('Update user when template is updated'); ?>
			  </td></tr>
			  <tr><td></td><td>
		          <!--<input type="radio" name="templatetype" value="detach"><?php echo word('Detach user from template'); ?>
			  </td></tr>
			  <tr><td></td><td>-->
			  <input type="radio" name="templatetype" value="customize" checked><?php echo word("Customize this user's settings"); ?>
			  </td></tr>
			  <tr>
			  <td width="30%" valign="top">
			  </td>
			  <td width="70%">
			  <input type="submit" name="handleUser" value="<?php echo word("Go"); ?>" class="jz_submit">
			  </td>
			  </tr>


			</table>
			<?php
			echo '</form>';
		  $this->closeBlock();
	}

  /*
   * Pulls the user settings from POST to a settings array.
   * @author Ben Dodson
   * @since 12/7/05
   * @version 12/7/05
   **/
  function userPullSettings() {
    $settings = array();
    
    $settings['language'] = $_POST['usr_language'];		 
    $settings['theme'] = $_POST['usr_theme'];
    $settings['frontend'] = $_POST['usr_interface'];      
    $settings['home_dir'] = $_POST['home_dir'];
    if (isset($_POST['home_read'])) {
      $settings['home_read'] = "true";
    } else {
      $settings['home_read'] = "false";
    }
    if (isset($_POST['home_admin'])) {
      $settings['home_admin'] = "true";
    } else {
      $settings['home_admin'] = "false";
    }
    if (isset($_POST['home_upload'])) {
      $settings['home_upload'] = "true";
    } else {
      $settings['home_upload'] = "false";
    }
    
    $settings['cap_limit'] = $_POST['cap_limit'];
    $settings['cap_duration'] = $_POST['cap_duration'];
    $settings['cap_method'] = $_POST['cap_method'];
    
    $settings['player'] = $_POST['player'];
    
    $settings['resample_rate'] = $_POST['resample'];
    
    if (isset($_POST['lockresample'])) {
      $settings['resample_lock'] = "true";
    } else {
      $settings['resample_lock'] = "false";
    }

    if (isset($_POST['view'])) {
      $settings['view'] = "true";
    } else {
      $settings['view'] = "false";
    }
    
    if (isset($_POST['stream'])) {
      $settings['stream'] = "true";
    } else {
      $settings['stream'] = "false";
    }
    
    if (isset($_POST['download'])) {
      $settings['download'] = "true";
    } else {
      $settings['download'] = "false";
    }
    
    if (isset($_POST['lofi'])) {
      $settings['lofi'] = "true";
    } else {
      $settings['lofi'] = "false";
    }
    
    if (isset($_POST['jukebox_admin'])) {
      $settings['jukebox_admin'] = "true";
      $settings['jukebox'] = "true";
    } else {
      $settings['jukebox_admin'] = "false";
    }
    
    if (isset($_POST['jukebox_queue'])) {
      $settings['jukebox_queue'] = "true";
      $settings['jukebox'] = "true";
    } else {
      $settings['jukebox_queue'] = "false";
    }
    
    
    if (isset($_POST['powersearch'])) {
      $settings['powersearch'] = "true";
    } else {
      $settings['powersearch'] = "false";
    }
    
    if (isset($_POST['admin'])) {
      $settings['admin'] = "true";
    } else {
      $settings['admin'] = "false";
    }
    
    if (isset($_POST['edit_prefs'])) {
      $settings['edit_prefs'] = "true";
    } else {
      $settings['edit_prefs'] = "false";
    }
    $settings['playlist_type'] = $_POST['pltype'];

		if (isset($_POST['fullname'])) {
      $settings['fullname'] = $_POST['fullname'];
    }
    
    if (isset($_POST['email'])) {
      $settings['email'] = $_POST['email'];
    }

    return $settings;
  }



  /*
   * Displays the user/template settings page
   * @param purpose: Why the function is being called:
   * One of: new|update|custom
   * @param settings: the preloaded settings
   * @author Ben Dodson
   **/

  function userManSettings($purpose, $settings = false, $subaction = false, $post = false) {
    global $jzSERVICES,$resampleRates,$include_path;
    $be = new jzBackend();
    $display = new jzDisplay();
    $url_array = array();
    $url_array['action'] = "popup";
    $url_array['ptype'] = "usermanager";
    if ($subaction === false) {
      $url_array['subaction'] = "handleclass";
    } else {
      $url_array['subaction'] = $subaction;
    }

    // Why PHP pisses me off.
    foreach ($settings as $k=>$v) {
      if ($v == "true") {
	$settings[$k] = true;
      } else if ($v == "false") {
	$settings[$k] = false;
      } else {
	$settings[$k] = $v;
      }
    }
      ?>
      <form method="POST" action="<?php echo urlize($url_array); ?>">
	 <input type="hidden" name="update_settings" value="true">
	 <?php 
	 if (is_array($post)) {
	   foreach ($post as $p => $v) {
	     echo '<input type="hidden" name="'.$p.'" value="'.$v.'">';
	   }
	 }
	?>
	 <table>
	 <?php if ($purpose != "custom") { ?>
	 <tr><td width="30%" valign="top" align="right">
	 <?php echo word("Template:"); ?>
	 </td><td width="70%">
	     <?php
	     if ($purpose == "new") {
	       ?>
	       <input name="classname" class="jz_input">
	       <?php
	     } else if ($purpose == "update") {
	       echo '<input type="hidden" name="classname" class="jz_input" value="'.$_POST['classname'].'">';
	       echo $_POST['classname'];
	     }
	   ?>
	     </td></tr><tr><td>&nbsp;</td><td>&nbsp;</td></tr>
					   <?php } ?>
							<tr>
							<td width="30%" valign="top" align="right">
							<?php echo word("Interface"); ?>:
	       </td>
		   <td width="70%">
		   <?php
		   $overCode = $display->returnToolTip(word("INTERFACE_NOTE"), word("Default Interface"));
		 ?>
		   <select <?php echo $overCode; ?> name="usr_interface" class="jz_select" style="width:135px;">
			 <?php
			 // Let's get all the interfaces
			 $retArray = readDirInfo($include_path. "frontend/frontends","dir");
		    sort($retArray);
		    for($i=0;$i<count($retArray);$i++){
		      echo '<option ';
		      if ($settings['frontend'] == $retArray[$i]) { echo 'selected '; }
		      echo 'value="'. $retArray[$i]. '">'. $retArray[$i]. '</option>'. "\n";
		    }
		      ?>
			</select>
			</td>
			</tr>
			<tr>
			<td width="30%" valign="top" align="right">
			<?php echo word("Theme"); ?>:
			</td>
			<td width="70%">
			<?php
			$overCode = $display->returnToolTip(word("THEME_NOTE"), word("Default Theme"));
			 ?>
			<select <?php echo $overCode; ?> name="usr_theme" class="jz_select" style="width:135px;">
			<?php
			// Let's get all the interfaces
			$retArray = readDirInfo($include_path. "style","dir");
		    sort($retArray);
		    for($i=0;$i<count($retArray);$i++){
		      if ($retArray[$i] == "images"){continue;}
		      echo '<option ';
		      if ($settings['theme'] == $retArray[$i]) { echo 'selected '; }
		      echo 'value="'. $retArray[$i]. '">'. $retArray[$i]. '</option>'. "\n";
		    }
		      ?>
			</select>
			</td>
			</tr>
			<tr>
			<td width="30%" valign="top" align="right">
			<?php echo word("Language"); ?>:
			</td>
			<td width="70%">
			<?php
				$overCode = $display->returnToolTip(word("LANGUAGE_NOTE"), word("Default Language"));
			 ?>
			<select <?php echo $overCode; ?> name="usr_language" class="jz_select" style="width:135px;">
			<?php
			// Let's get all the interfaces
			$languages = getLanguageList();
		    for($i=0;$i<count($languages);$i++){
		      echo '<option ';
		      if ($languages[$i] == $settings['language']){echo ' selected '; }
		      echo 'value="'.$languages[$i]. '">'.$languages[$i]. '</option>'. "\n";
		    }
		      ?>
							</select>
							    </td>
							    </tr>
							    <tr>
							    <td width="30%" valign="top" align="right">
							    <?php echo word("Home Directory"); ?>:
							  </td>
							    <td width="70%">
								<?php
								$overCode = $display->returnToolTip(word("HOMEDIR_NOTE"), word("User Home Directory"));
								 ?>
							    <input <?php echo $overCode; ?> type="input" name="home_dir" class="jz_input" value="<?php echo $settings['home_dir']; ?>">
							    </td>
							    </tr>
							    <tr>
							    <td width="30%" valign="middle" align="right">
							    <?php echo word("Home Permissions"); ?>:
							  </td>
							    <td width="70%">
							    <br>
								<?php
									$overCode = $display->returnToolTip(word("HOMEREAD_NOTE"), word("Read Home Directory"));
									$overCode2 = $display->returnToolTip(word("HOMEADMIN_NOTE"), word("Admin Home Directory"));
									$overCode3 = $display->returnToolTip(word("HOMEUPLOAD_NOTE"), word("Home Directory Upload"));
								 ?>
							    <input <?php echo $overCode; ?> type="checkbox" name="home_read" class="jz_input" <?php if ($settings['home_read'] == true) { echo 'CHECKED'; } ?>> Read only from home directory<br>
							    <input <?php echo $overCode2; ?> type="checkbox" name="home_admin" class="jz_input" <?php if ($settings['home_admin'] == true) { echo 'CHECKED'; } ?>> Home directory admin<br>
							    <input <?php echo $overCode3; ?> type="checkbox" name="home_upload" class="jz_input" <?php if ($settings['home_upload'] == true) { echo 'CHECKED'; } ?>> Upload to home directory
							    <br><br>
							    </td>
							    </tr>
							    
							    <tr>
							    <td width="30%" valign="middle" align="right">
							    <?php echo word("User Rights"); ?>:
							  </td>
							    <td width="70%">
								<?php
									$overCode = $display->returnToolTip(word("VIEW_NOTE"), word("User can view media"));
									$overCode2 = $display->returnToolTip(word("STREAM_NOTE"), word("User can stream media"));
									$overCode3 = $display->returnToolTip(word("LOFI_NOTE"), word("User can access lo-fi tracks"));
									$overCode4 = $display->returnToolTip(word("DOWNLOAD_NOTE"), word("User can download"));
									$overCode5 = $display->returnToolTip(word("POWERSEARCH_NOTE"), word("User can power search"));
									$overCode6 = $display->returnToolTip(word("JUKEBOXQ_NOTE"), word("User can queue jukebox"));
									$overCode7 = $display->returnToolTip(word("JUKEBOXADMIN_NOTE"), word("User can admin jukebox"));
									$overCode8 = $display->returnToolTip(word("SITE_NOTE"), word("Site Admin"));
									$overCode9 = $display->returnToolTip(word("EDIT_NOTE"), word("Edit Preferences"));
								 ?>
							    <input <?php echo $overCode; ?> type="checkbox" name="view" class="jz_input" <?php if ($settings['view'] == true) { echo 'CHECKED'; } ?>> View
							    <input <?php echo $overCode2; ?> type="checkbox" name="stream" class="jz_input" <?php if ($settings['stream'] == true) { echo 'CHECKED'; } ?>> Stream
							    <input <?php echo $overCode3; ?> type="checkbox" name="lofi" class="jz_input" <?php if ($settings['lofi'] == true) { echo 'CHECKED'; } ?>> Lo-Fi<br>
							    <input <?php echo $overCode4; ?> type="checkbox" name="download" class="jz_input" <?php if ($settings['download'] == true) { echo 'CHECKED'; } ?>> Download
							    <input <?php echo $overCode5; ?> type="checkbox" name="powersearch" class="jz_input" <?php if ($settings['powersearch'] == true) { echo 'CHECKED'; } ?>> Power Search<br>
							    <input <?php echo $overCode6; ?> type="checkbox" name="jukebox_queue" class="jz_input" <?php if ($settings['jukebox_queue'] == true) { echo 'CHECKED'; } ?>> Jukebox Queue
							    <input <?php echo $overCode7; ?> type="checkbox" name="jukebox_admin" class="jz_input" <?php if ($settings['jukebox_admin'] == true) { echo 'CHECKED'; } ?>> Jukebox Admin<br>
							    <input <?php echo $overCode8; ?> type="checkbox" name="admin" class="jz_input" <?php if ($settings['admin'] == true) { echo 'CHECKED'; } ?>> Site Admin
						        <input <?php echo $overCode9; ?> type="checkbox" name="edit_prefs" class="jz_input" <?php if ($settings['edit_prefs'] == true) { echo 'CHECKED'; } ?>> Edit Prefs
							    <br><br>
							    </td>
							    </tr>
							    <tr>
								<td width="30%" valign="top" align="right">
							    <?php echo word("Playlist Type"); ?>:
								</td><td width="70%">
								<?php
								$overCode = $display->returnToolTip(word("PLAYLIST_NOTE"), word("Playlist Type"));
								 ?>
								<select <?php echo $overCode; ?> name="pltype" class="jz_select" style="width:135px;">
							 <?php
						 $list = $jzSERVICES->getPLTypes();
						foreach ($list as $p=>$desc) {
						  echo '<option value="' . $p . '"';
						  if ($p == $settings['playlist_type']) {
						    echo ' selected';
						  }
						  echo '>' . $desc . '</option>';
						} ?>
				    </select></td></tr>

							    <tr>
							    <td width="30%" valign="top" align="right">
							    <?php echo word("Resample Rate"); ?>:
							  </td>
					<td width="70%">
					<?php
						$overCode = $display->returnToolTip(word("RESAMPLE_NOTE"), word("Resample Rate"));
						$overCode2 = $display->returnToolTip(word("LOCK_NOTE"), word("Resample Rate Lock"));
					 ?>
						<select <?php echo $overCode; ?> name="resample" class="jz_select" style="width:50px;">
							<option value="">-</option>
							<?php
								// Now let's create all the items based on their settings
								$reArr = explode("|",$resampleRates);
								for ($i=0; $i < count($reArr); $i++){
									echo '<option value="'. $reArr[$i]. '"';
									if ($settings['resample_rate'] == $reArr[$i]) {
									  echo ' selected';
									}
									echo '>'. $reArr[$i]. '</option>'. "\n";
								}
							?>
						</select> 
						    <input <?php echo $overCode2; ?> type="checkbox" name="lockresample" class="jz_input" <?php if ($settings['resample_lock'] == true) { echo 'CHECKED'; } ?>> <?php echo word('Locked'); ?>
					</td>
				</tr>
				<tr>
					<td width="30%" valign="top" align="right">
						<?php echo word("External Player"); ?>:
					</td>
					<td width="70%">
						<?php
						 $overCode = $display->returnToolTip(word("PLAYER_NOTE"), word("External Player"));
						?>
						<select <?php echo $overCode; ?> name="player" class="jz_select" style="width:135px;">
							<option value=""> - </option>
							<?php
								// Let's get all the interfaces
								$retArray = readDirInfo($include_path. "services/services/players","file");
								sort($retArray);
								for($i=0;$i<count($retArray);$i++){
									if (!stristr($retArray[$i],".php") and !stristr($retArray[$i],"qt.")){continue;}
									$val = substr($retArray[$i],0,-4);
									echo '<option value="'. $val. '"';
									if ($settings['player'] == $val) {
									  echo ' selected';
									}
									echo '>'. $val. '</option>'. "\n";
								}
							?>
						</select>
					</td>
				</tr>
				<tr>
					<td width="30%" valign="top" align="right">
						<?php echo word("Playback Limit"); ?>:
					</td>
					<td width="70%"><td></tr><tr><td></td><td>
					    <table><tr><td>
					    
						<?php
					    echo word("Limit:"); 
								echo '</td><td>';
					                        $overCode = $display->returnToolTip(word("Sets a streaming limit for users based on the size or number of songs played."), word("Playback Limit"));
								$cap_limit = $settings['cap_limit'];
								if (isNothing($cap_limit)) { $cap_limit = 0; }
						?>
					        <input <?php echo $overCode; ?> name="cap_limit" class="jz_select" style="width:35px;" value="<?php echo $cap_limit; ?>">
					</td></tr>
                                        <tr><td>					    
						<?php
					    echo word("Method:"); 
								echo '</td><td>';
					                        $overCode = $display->returnToolTip(word("Sets the method for limiting playback"), word("Limiting method"));
								$cap_method = $settings['cap_method'];
						?>
					        <select name="cap_method" class="jz_select" <?php echo $overCode; ?>>
					       <option value="size"<?php if ($cap_method == "size") { echo ' selected'; } ?>><?php echo word('Size (MB)');?></option>
					       <option value="number"<?php if ($cap_method == "number") { echo ' selected'; } ?>><?php echo word('Number');?></option>
					</td></tr>
                                        <tr><td>
					    
						<?php
					    echo word("Duration:"); 
								echo '</td><td>';
					                        $overCode = $display->returnToolTip(word("How long the limit lasts, in days."), word("Limit duration"));
								$cap_duration = $settings['cap_duration'];
								if (isNothing($cap_duration)) { $cap_duration = 30; }
						?>
					        <input <?php echo $overCode; ?> name="cap_duration" class="jz_select" style="width:35px;" value="<?php echo $cap_duration; ?>">
					</td></tr>
										  </table>
				</tr>
								
				
				<tr>
					<td width="30%" valign="top">
					</td>
					<td width="70%">
					<input type="submit" name="handlUpdate" value="<?php echo word("Save"); ?>" class="jz_submit">
					</td>
				</tr>
						    </table>
<?php
  }



function userPreferences() {
  global $include_path, $jzUSER, $jzSERVICES, $cms_mode, $enable_audioscrobbler, $as_override_user, $as_override_all;
	
	$this->displayPageTop("",word("User Preferences"));
	$this->openBlock();
	// Now let's show the form for it
	if (isset($_POST['update_settings'])) {
	  if (strlen($_POST['field1']) > 0 && $_POST['field1'] != "jznoupd") {
	    if ($_POST['field1'] == $_POST['field2']) {
	      // update the password:
	      $jzUSER->changePassword($_POST['field1']);
	    }
	  }

	  $arr = array();
	  $arr['email'] = $_POST['email'];
	  $arr['fullname'] = $_POST['fullname'];
	  $arr['frontend'] = $_POST['def_interface'];
	  $arr['theme'] = $_POST['def_theme'];
	  $arr['language'] = $_POST['def_language'];
	  $arr['playlist_type'] = $_POST['pltype'];
		$arr['asuser'] = $_POST['asuser'];
		$arr['aspass'] = $_POST['aspass'];
	  $jzUSER->setSettings($arr);

	  if (isset($_SESSION['theme'])) {
	    unset($_SESSION['theme']);
	  }
	  if (isset($_SESSION['frontend'])) {
	    unset($_SESSION['frontend']);
	  }
	  if (isset($_SESSION['language'])) {
	    unset($_SESSION['language']);
	  }
		
		?>
			<script language="javascript">
			opener.location.reload(true);
			-->
			</SCRIPT>
		<?php

    //$this->closeWindow(true);
	  //return;
	}

	$url_array = array();
	$url_array['action'] = "popup";
	$url_array['ptype'] = "preferences";	
	echo '<form action="'. urlize($url_array). '" method="POST">';
	?>
	<table width="100%" cellpadding="3">
<?php	if ($cms_mode == "false") { ?>
		<tr>
			<td width="30%" valign="top" align="right">
				<?php echo word("Password"); ?>:
			</td>
			<td width="70%">
				<input type="password" name="field1" class="jz_input" value="jznoupd"><br>
				<input type="password" name="field2" class="jz_input" value="jznoupd">
			</td>
		</tr><?php } else { ?> <input type="hidden" name="field1" value="jznoupd"> <?php } ?>
		<tr>
			<td width="30%" valign="top" align="right">
				<?php echo word("Full Name"); ?>:
			</td>
			<td width="70%">
				<input name="fullname" class="jz_input" value="<?php echo $jzUSER->getSetting('fullname'); ?>">
			</td>
		</tr>
		<tr>
			<td width="30%" valign="top" align="right">
				<?php echo word("Email"); ?>:
			</td>
			<td width="70%">
				<input name="email" class="jz_input" value="<?php echo $jzUSER->getSetting('email'); ?>">
			</td>
		</tr>
		
		
		<?php
			// Did they enable audioscrobbler?
			if ($enable_audioscrobbler == "true" and ($as_override_user == "" or $as_override_all == "false")){
				?>
				<tr>
					<td width="30%" valign="top" align="right">
						<?php echo word("AS User"); ?>:
					</td>
					<td width="70%">
						<input name="asuser" class="jz_input" value="<?php echo $jzUSER->getSetting('asuser'); ?>">
					</td>
				</tr>
				<tr>
					<td width="30%" valign="top" align="right">
						<?php echo word("AS pass"); ?>:
					</td>
					<td width="70%">
						<input type="password" name="aspass" class="jz_input" value="<?php echo $jzUSER->getSetting('aspass'); ?>">
					</td>
				</tr>
				<?php
			}
		?>
		
		
		<tr>
			<td width="30%" valign="top" align="right">
				<?php echo word("Interface"); ?>:
			</td>
			<td width="70%">
				<select name="def_interface" class="jz_select" style="width:135px;">
					<?php
						// Let's get all the interfaces
						$retArray = readDirInfo($include_path. "frontend/frontends","dir");
						sort($retArray);
						for($i=0;$i<count($retArray);$i++){
							echo '<option ';
							if ($retArray[$i] == $jzUSER->getSetting("frontend")){echo ' selected '; }
							echo 'value="'. $retArray[$i]. '">'. $retArray[$i]. '</option>'. "\n";
						}
					?>
				</select>
			</td>
		</tr>
		<tr>
			<td width="30%" valign="top" align="right">
				<?php echo word("Theme"); ?>:
			</td>
			<td width="70%">
				<select name="def_theme" class="jz_select" style="width:135px;">
					<?php
						// Let's get all the interfaces
						$retArray = readDirInfo($include_path. "style","dir");
						sort($retArray);
						for($i=0;$i<count($retArray);$i++){
							if ($retArray[$i] == "images"){continue;}
							echo '<option ';
							if ($retArray[$i] == $jzUSER->getSetting('theme')) {echo ' selected '; }
							echo 'value="'. $retArray[$i]. '">'. $retArray[$i]. '</option>'. "\n";
						}
					?>
				</select>
			</td>
		</tr>
		<tr>
			<td width="30%" valign="top" align="right">
				<?php echo word("Language"); ?>:
			</td>
			<td width="70%">
				<select name="def_language" class="jz_select" style="width:135px;">
					<?php
						// Let's get all the interfaces
			                        $languages = getLanguageList();
						for($i=0;$i<count($languages);$i++){
						  echo '<option ';
							if ($languages[$i] == $jzUSER->getSetting('language')){echo ' selected '; }
							echo 'value="'.$languages[$i].'">'.$languages[$i]. '</option>'. "\n";
						}
					?>
				</select>
			</td>
		</tr>
				    <tr>
			<td width="30%" valign="top" align="right">
				<?php echo word("Playlist Type"); ?>:
			</td>
			<td width="70%">
				<select name="pltype" class="jz_select" style="width:135px;">
				    <?php
				    $list = $jzSERVICES->getPLTypes();
						foreach ($list as $p=>$desc) {
						  echo '<option value="' . $p . '"';
						  if ($jzUSER->getSetting('playlist_type') == $p) {
						    echo " selected";
						  }
						  echo '>' . $desc . '</option>';
						} ?>
				    </select>
			</td>
		</tr>
	</table>
	<br><center>
		<input type="submit" name="update_settings" value="<?php echo word("Update Settings"); ?>" class="jz_submit">
		<?php $this->closeButton(); ?> 
	</center>
	<br>
	</form>
	<?php

  $this->closeBlock();
}
	
	/**
	* Shows the documentation system
	* 
	* @author Ross Carlson
	* @version 01/19/05
	* @since 01/19/05
	*/
	function showDocs(){
		global $root_dir, $jz_lang_file;
		
		// Let's refresh
		echo '<META HTTP-EQUIV=Refresh CONTENT="0; URL='. $root_dir. "/docs/". $jz_lang_file. "/index.html". '">';
	}
	

	/**
	* Rates the currently viewed item
	* 
	* @author Ross Carlson
	* @version 01/19/05
	* @since 01/19/05
	* @param $node The node that we are viewing
	*/
	function userRateItem($node){

		// Let's see if they rated it?
		if (isset($_POST['itemRating'])){
			// Ok, let's rate and close
			$node->addRating($_POST['itemRating']);
			$this->closeWindow(true);
		}
		
		// First let's display the top of the page and open the main block
		$this->displayPageTop("",word("Rate Item"). "<br>". $node->getName());
		$this->openBlock();
		
		// Now let's setup the values
		$url_array = array();
		$url_array['jz_path'] = $node->getPath("String");
		$url_array['action'] = "popup";
		$url_array['ptype'] = "rateitem";
		
		echo '<form action="'. urlize($url_array). '" method="POST">';
		echo '<center><br>'. word("Rating"). ': ';
		echo '<select name="' . jz_encode('itemRating') . '" class="jz_select">';
		for ($i=5; $i > 0;){
			echo '<option value="'. jz_encode($i). '">'. $i. '</option>';
			$i=$i-.5;
		}
		echo '</select>';
		echo '<br><br><input type="submit" name="' . jz_encode('submitRating') . '" value="'. word("Rate Item"). '" class="jz_submit">';
		echo " ";
		$this->closeButton();
		echo '</center>';
		echo '</form>';
		
		// Now let's close out
		$this->closeBlock();				
	}

	/**
	* Removes the selected node to the featured list
	* 
	* @author Ross Carlson
	* @version 01/19/05
	* @since 01/19/05
	* @param $node The node that we are viewing
	*/
	function removeFeatured($node){
		// First let's display the top of the page and open the main block
		$this->displayPageTop("",word("Removing from featured"). "<br>". $node->getName());
		$this->openBlock();
		
		// Now let's add this puppy
		$node->removeFeatured();
		
		// Let's display status
		echo "<br>". word("Remove complete!");
		
		// Now let's close out
		$this->closeBlock();		
		flushDisplay();
		
		sleep(3);
		$this->closeWindow(true);
	}
	
	/**
	* Adds the selected node to the featured list
	* 
	* @author Ross Carlson
	* @version 01/19/05
	* @since 01/19/05
	* @param $node The node that we are viewing
	*/
	function addToFeatured($node){
		
		// First let's display the top of the page and open the main block
		$this->displayPageTop("",word("Adding to featured"). "<br>". $node->getName());
		$this->openBlock();
		
		// Now let's add this puppy
		$node->addFeatured();
		
		// Let's display status
		echo "<br>". word("Add complete!");
		
		// Now let's close out
		$this->closeBlock();		
		flushDisplay();
		
		sleep(3);
		$this->closeWindow(true);
	}
	
	/**
	* Displays the read more information on an artist from a popup
	* 
	* @author Ross Carlson
	* @version 01/19/05
	* @since 01/19/05
	* @param $node The node that we are viewing
	*/
	function displayReadMore($node){
		global $cms_mode;
	
		// Let's setup our objects
		$display = new jzDisplay();
		
		// First let's display the top of the page and open the main block
		$this->displayPageTop("",word("Profile"). ": ". $node->getName());
		$this->openBlock();
		
		// Now let's display the artist image and short description
		if (($art = $node->getMainArt("200x200")) <> false) {
			$display->image($art,$node->getName(),200,200,"limit",false,false,"left","5","5");
		}
		if ($cms_mode == "false"){
			echo '<span class="jz_artistDesc">';
		}
		echo fixAMGUrls($node->getDescription());
		if ($cms_mode == "false"){
			echo '</span>';		
		}

		$this->closeBlock();
	}
	
	/**
	* Displays the top of the page for the popup window
	* 
	* @author Ross Carlson
	* @version 01/18/05
	* @since 01/18/05
	* @param $bg_color a hex value for the background color, IF we want one
	* @param $headerTitle The title for the page
	*/
	function displayPageTop($bg_color = "", $headerTitle = "", $js = true){
		global $row_colors, $web_root, $root_dir, $skin, $cms_mode, $cms_type, $cur_theme, $css;
		$display = new jzDisplay();
		//handleSetTheme();
		// AJAX:
		$display->handleAJAX();

		// Let's include the javascript
		if ($js){
			echo '<script type="text/javascript" src="'. $root_dir. '/lib/jinzora.js"></script>'. "\n";
			echo '<script type="text/javascript" src="'. $root_dir. '/lib/overlib.js"></script>';
			echo '<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>';
		}
		
		// Let's start our page
		echo '<title>Jinzora</title>'. "\n";
		
		// Let's output the Jinzora style sheet
		//include_once($css);
		echo '<link rel="stylesheet" title="'. $skin. '" type="text/css" media="screen" href="'. $css. '">'. "\n";

		// Now let's see if they wanted a different background color
		if ($bg_color <> ""){
			echo '<span style="font-size:0px">.</span><body marginwidth=0 marginheight=0 style="margin: 0px" style="background-color:'. $bg_color. '">'. "\n";
		}
			 
		// Now let's output the CMS style sheet, if necessary
		if ($cms_mode <> "false"){
			switch ($cms_type){
				case "postnuke" :
				case "phpnuke" :
				case "cpgnuke" :
				case "mdpro" :
					echo '<LINK REL="StyleSheet" HREF="'. $_SESSION['cms-style']. '" TYPE="text/css">';
					
					// Now let's get the data we need from the session var
					$cArr = explode("|",urldecode($_SESSION['cms-theme-data']));
					echo "<style type=\"text/css\">" .
						 ".jz_row1 { background-color:". $cArr[0]. "; }".
						 ".jz_row2 { background-color:". $cArr[1]. "; }".
						 ".and_head1 { background-color:". $cArr[0]. "; }".
						 ".and_head2 { background-color:". $cArr[1]. "; }".
						 "</style>";
				break;
				case "mambo" :
					echo '<LINK REL="StyleSheet" HREF="'. $_SESSION['cms-style']. '" TYPE="text/css">'. "\n";
					$row_colors = array('sectiontableentry2','tabheading');
				break;
			}
		}
		if (stristr($skin,"/")){
			$img_path = $root_dir. "/". $skin;
		} else {
			$img_path = $root_dir. "/style/". $skin;
		}

		// Now let's show the page title
		if ($headerTitle <> ""){
			?>
			<table width="100%" cellpadding="3" cellspacing="0" border="0"><tr><td>
			<table width="100%" cellpadding="3" cellspacing="0" border="0">
				<tr>
					<td width="6" height="6" style="background: url(<?php echo $img_path; ?>/inner-block-top-left.gif); background-repeat:no-repeat"></td>
					<td width="99%" height="6" style="background: url(<?php echo $img_path; ?>/inner-block-top-middle.gif);"></td>
					<td width="6" height="6" style="background: url(<?php echo $img_path; ?>/inner-block-top-right.gif); background-repeat:no-repeat"></td>
				</tr>
				<tr>
					<td width="6" style="background: url(<?php echo $img_path; ?>/inner-block-left.gif); background-repeat:repeat"></td>
					<td width="99%">
						<table width="100%" cellpadding="0" cellspacing="0" border="0">
							<tr>
								<td width="100%">
									<font size="1" color="<?php echo jz_font_color; ?>">
										<strong><?php echo $headerTitle; ?></strong>
									</font>
								</td>
							</tr>
						</table>
					</td>
					<td width="6" style="background: url(<?php echo $img_path; ?>/inner-block-right.gif); background-repeat:repeat"></td>
				</tr>
				<tr>
					<td width="6" height="6" style="background: url(<?php echo $img_path; ?>/inner-block-bottom-left.gif); background-repeat:no-repeat"></td>
					<td width="99%" height="6" style="background: url(<?php echo $img_path; ?>/inner-block-bottom-middle.gif);"></td>
					<td width="6" height="6" style="background: url(<?php echo $img_path; ?>/inner-block-bottom-right.gif); background-repeat:no-repeat"></td>
				</tr>
			</table>
			</td></tr></table>
			<?php
		}
		flushDisplay();
	}
	
	/**
	* Opens a block to have rounded corners
	* 
	* @author Ross Carlson
	* @version 01/18/05
	* @since 01/18/05
	*/
	function openBlock(){
		global $root_dir;
		?>
		<table width="100%" cellpadding="5" cellspacing="0" border="0"><tr><td>
		<?php
	}
	
	/**
	* Closes a block to have rounded corners
	* 
	* @author Ross Carlson
	* @version 01/18/05
	* @since 01/18/05
	*/
	function closeBlock(){
		echo "</td></tr></table>". "\n";
		flushdisplay();
	}
	
	
	/**
	* Closes the popup window for us
	* 
	* @author Ross Carlson
	* @version 01/18/05
	* @since 01/18/05
	* @param $parent_reload Should we refresh the calling page (defaults to true)?
	*/
	function closeWindow($parent_reload = true){	
		if ($parent_reload){
			?>
			<script language="javascript">
			opener.location.reload(true);
			window.close();
			-->
			</SCRIPT>
			<?php
		} else {	
			?>
			<script language="javascript">
			window.close();
			-->
			</SCRIPT>
			<?php
		}
	}	

	/**
	* Scans the users system for newly added media
	* 
	* @author Ross Carlson
	* @version 01/18/05
	* @since 01/18/05
	* @param $node The node we are looking at
	*/
	function scanForMedia($node){
	  global $backend;

		if ($backend == "id3-cache" || $backend == "id3-database") {
		  $node = new jzMediaNode(); // Root only, just to be sure...
		}

		// First let's display the top of the page and open the main block
		$title = $node->getName();
		if ($title == ""){$title = word("Root Level"); }
		$this->displayPageTop("","Scanning for new media in: ". $title);
		$this->openBlock();
		
		// Let's show them the form
		if (!isset($_POST['edit_scan_now'])){
			$url_array = array();
			$url_array['action'] = "popup";
			$url_array['ptype'] = "scanformedia";
			$url_array['jz_path'] = $_GET['jz_path'];
			$i=0;
			?>
			<form action="<?php echo urlize($url_array); ?>" method="post">
			   <?php
			   if (!($backend == "id3-cache" || $backend == "id3-database")) {
			     ?>
				<input name="edit_scan_where" value="only" checked type="radio"> <?php echo word("This level only"); ?><br>
				<input name="edit_scan_where" value="all" type="radio"> <?php echo word("All sub items (can be very slow)"); ?><br><br>
			     <?php
			   } else {
			     ?>
 				<input name="edit_scan_where" value="all" type="hidden">
                             <?php
			   }
			  ?>
                                <input name="edit_force_scan" value="true" type="checkbox"> <?php echo word("Ignore file modification times (slow)"); ?><br>
				<br>
				&nbsp; &nbsp; &nbsp; <input type="submit" name="edit_scan_now" value="<?php echo word("Scan Now"); ?>" class="jz_submit">
			</form>		
			<?php
			exit();
		}
		
		// Ok, let's do it...		
		echo "<b>". word("Scanning"). ":</b>";
		echo '<div id="importStatus"></div>';
		?>
		<script language="javascript">
		d = document.getElementById("importStatus");
		-->
		</SCRIPT>
		<?php
		set_time_limit(0);
		flushdisplay();
		
		// Now how to scan?
		if ($_POST['edit_scan_where'] == "only"){
			$recursive = false;
		} else {
			$recursive = true;
		}

		// Let's scan...
		if (isset($_POST['edit_force_scan'])) {
		  $force_scan = true;
		} else {
		  $force_scan = false;
		}

		updateNodeCache($node,$recursive,true,$force_scan);
		
		echo "<br><br><b>". word("Complete!"). "</b>";
		$this->closeBlock();
		flushdisplay();
		
		// Now let's close out
		echo "<br><br><center>";
		$this->closeButton();
	}
	function readAllDirs2($dirName, &$readCtr){
		global $audio_types, $video_types;

		// Let's up the max_execution_time
		ini_set('max_execution_time','6000');
		// Let's look at the directory we are in		
		if (is_dir($dirName)){
			$d = dir($dirName);
			if (is_object($d)){
				while($entry = $d->read()) {
					// Let's make sure we are seeing real directories
					if ($entry == "." || $entry == "..") { continue;}
					if ($readCtr % 100 == 0){ 
						?>
						<script language="javascript">
							p.innerHTML = '<b><?php echo $readCtr. " ". word("files analyzed"); ?></b>';									
							-->
						</SCRIPT>
						<?php 
						@flush(); @ob_flush();
					}
					// Now let's see if we are looking at a directory or not
					if (filetype($dirName. "/". $entry) <> "file"){
						// Ok, that was a dir, so let's move to the next directory down
						readAllDirs2($dirName. "/". $entry, $readCtr);
					} else {
						if (preg_match("/\.($audio_types|$video_types)$/i", $entry)){
							$readCtr++;
							$_SESSION['jz_full_counter']++;
						}							
					}			
				}
				// Now let's close the directory
				$d->close();
			}
		}		
	}

	/**
	* Searches for meta data of the given node but shows the results step by step
	* 
	* @author Ross Carlson
	* @version 01/18/05
	* @since 01/18/05
	* @param $node The node we are looking at
	*/
	function stepMetaSearch($node){
		global $jzSERVICES, $row_colors, $allow_id3_modify, $include_path, $allow_filesystem_modify; 
		
		echo '<div id="artist"></div>';
		echo '<div id="arStatus"></div>';
		echo '<div id="count"></div>';
		echo '<div id="art"></div>';
		?>
		<SCRIPT LANGUAGE=JAVASCRIPT><!--\
			ar = document.getElementById("artist");
			ars = document.getElementById("arStatus");
			c = document.getElementById("count");
			i = document.getElementById("art");
			-->
		</SCRIPT>
		<?php
		
		flushdisplay();
		// Now let's search, first we need to get all the nodes from here down
		$nodes = $node->getSubNodes("nodes",-1);
		
		// Now let's add the node for what we are viewing
		$nodes = array_merge(array($node),$nodes);
		$total = count($nodes);$c=0;$start=time();
		
		foreach($nodes as $item){
			?>
			<SCRIPT LANGUAGE=JAVASCRIPT><!--\
				ar.innerHTML = '<nobr><?php echo word("Item"); ?>: <?php echo $item->getName(); ?></nobr>';					
				ars.innerHTML = '<?php echo word("Status: Searching..."); ?>';
				-->
			</SCRIPT>
			<?php
			flushdisplay();
			
			// Is this an artist?
			if ($item->getPType() == 'artist'){
				
			}
			// Is this an album?
			if ($item->getPType() == 'album'){
				// Now let's loop all the services
				$sArr = array("jinzora", "yahoo","rs","musicbrainz","google");
				foreach ($sArr as $service){
					?>
					<SCRIPT><!--\
						ars.innerHTML = '<?php echo word("Searching"). ": ". $service; ?>'					
						-->
					</SCRIPT>
					<?php
					flushdisplay();
					include_once($include_path. "services/services/metadata/". $service. ".php");
					$func = "SERVICE_GETALBUMMETADATA_". $service;
					$itemData = $func($item, false, "array");
					
					if ($itemData['image'] <> ""){
						echo '<table width="100%" cellpadding="3" cellspacing="0" border="0"><tr><td>';
								
						echo '<img width="75" align="left" src="'. $itemData['image']. '" border="0">';
						if (!isNothing($itemData['year'])){
							echo $itemData['year']. "<br>";
						}
						if (!isNothing($itemData['rating'])){
							echo $itemData['rating'];
						}
						echo $itemData['review'];
						
						echo '</td></tr><tr><td><input class="jz_submit" type="button" name="edit_download_image" value="'. word("Download"). " - ". $service. '">';
						echo "<br><br></td></tr></table>";
					}
					unset($itemData);
					flushdisplay();
				}
			}
		}
		?>
		<br>
		<SCRIPT LANGUAGE=JAVASCRIPT><!--\
			ars.innerHTML = '&nbsp;';					
			c.innerHTML = '&nbsp;';								
			-->
		</SCRIPT>
		<?php
		echo "<br><center>";
		$this->closeButton(true);
		exit();
	}
	
	
	/**
	* Searches for meta data of the given node
	* 
	* @author Ross Carlson
	* @version 01/18/05
	* @since 01/18/05
	* @param $node The node we are looking at
	*/
	function searchMetaData($node){
		global $jzSERVICES, $row_colors, $allow_id3_modify, $include_path, $allow_filesystem_modify; 
		
		set_time_limit(0);
		
		// First let's display the top of the page and open the main block
		$this->displayPageTop("",word("Retrieving meta data for"). ":<br>". $node->getName());
		$this->openBlock();
		
		// Let's show them the form so they can pick what they want to do
		if (!isset($_POST['metaSearchSubmit']) and !isset($_POST['edit_meta_search_step'])){			
			$url_array = array();
			$url_array['action'] = "popup";
			$url_array['ptype'] = "getmetadata";
			$url_array['jz_path'] = $_GET['jz_path'];
			$i=0;
			?>
			<form action="<?php echo urlize($url_array); ?>" method="post">
				<?php echo word("Search for"); ?>:<br>
				<input checked type="checkbox" name="edit_search_all_albums"> <?php echo word("Album data"); ?>
				<br>
				<?php
					if ($node->getPType() <> "album"){
						?>
						<input checked type="checkbox" name="edit_search_all_artists"> <?php echo word("Artist data"); ?><br>
						<?php
					} else {
						echo '<input type="hidden" name="edit_search_all_artists" value="off">';
					}
				?>
				<br>
				<?php echo word("Data to retrieve"); ?>:<br>
				<table width="100%" cellpadding="3" cellspacing="0">
					<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
						<td valign="top" width="10%">
							<nobr>
							<input checked type="checkbox" name="edit_search_images"> <?php echo word("Images"); ?>
							<nobr>
						</td>
						<td valign="top" width="90%">
							<input value="miss" checked type="radio" name="edit_search_images_miss"> <?php echo word("If missing"); ?><br>
							<input value="always" type="radio" name="edit_search_images_miss"> <?php echo word("Always"); ?>
						</td>
					</tr>
					<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
						<td valign="top" width="10%">
							<nobr>
							<input checked type="checkbox" name="edit_search_desc"> <?php echo word("Descriptions"); ?>
							<nobr>
						</td>
						<td valign="top" width="90%">
							<input value="miss" checked type="radio" name="edit_search_desc_miss"> <?php echo word("If missing"); ?><br>
							<input value="always" type="radio" name="edit_search_desc_miss"> <?php echo word("Always"); ?>
						</td>
					</tr>
					<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
						<td valign="top" width="10%">
							<nobr>
							<input checked type="checkbox" name="edit_search_rating"> <?php echo word("Rating"); ?>
							<nobr>
						</td>
						<td valign="top" width="90%">
							<input value="miss" checked type="radio" name="edit_search_rating_miss"> <?php echo word("If missing"); ?><br>
							<input value="always" type="radio" name="edit_search_rating_miss"> <?php echo word("Always"); ?>
						</td>
					</tr>
					<tr class="<?php echo $row_colors[$i]; $i = 1 - $i;?>">
						<td valign="top" width="10%">
							<nobr>
							<input checked type="checkbox" name="edit_search_year"> <?php echo word("Year"); ?>
							<nobr>
						</td>
						<td valign="top" width="90%">
							<input value="miss" checked type="radio" name="edit_search_year_miss"> <?php echo word("If missing"); ?><br>
							<input value="always" type="radio" name="edit_search_year_miss"> <?php echo word("Always"); ?>
						</td>
					</tr>
				</table>
				<br>
				<input type="submit" name="<?php echo jz_encode("metaSearchSubmit"); ?>" value="<?php echo word("Search"); ?>" class="jz_submit">
				<!--<input type="submit" name="<?php echo jz_encode("edit_meta_search_step"); ?>" value="<?php echo word("Search"); ?>" class="jz_submit">-->
			</form>
			<?php
			$this->closeButton();
			$this->closeBlock();
			exit();
		}
		
		// Did they want to verify the search?
		if (isset($_POST['edit_meta_search_step'])){
			$this->stepMetaSearch($node);
		}
		
		?>
		<SCRIPT LANGUAGE=JAVASCRIPT TYPE="TEXT/JAVASCRIPT"><!--\
			window.resizeTo(500,800)
		-->
		</SCRIPT>
		<?php	
		flushdisplay();
		
		// Ok, they submitted the form, let's do what they wanted						
		echo word("Searching, please wait..."). "<br><br>";
		echo '<div id="artist"></div>';
		echo '<div id="arStatus"></div>';
		echo '<div id="count"></div>';
		echo '<div id="art"></div>';
		
		?>
		<SCRIPT LANGUAGE=JAVASCRIPT><!--\
			ar = document.getElementById("artist");
			ars = document.getElementById("arStatus");
			c = document.getElementById("count");
			i = document.getElementById("art");
			-->
		</SCRIPT>
		<?php
		
		flushdisplay();
		// Now let's search, first we need to get all the nodes from here down
		$nodes = $node->getSubNodes("nodes",-1);
		
		// Now let's add the node for what we are viewing
		$nodes = array_merge(array($node),$nodes);
		$total = count($nodes);$c=0;$start=time();
		
		foreach ($nodes as $item){
			// Now let's see if this is an artist
			if ($item->getPType() == 'artist'){
				// Now do we want to look at this?
				if ($_POST['edit_search_all_artists'] == "on"){
					// Ok, let's get data for this artist
					?>
					<SCRIPT LANGUAGE=JAVASCRIPT><!--\
						ar.innerHTML = '<nobr><?php echo word("Artist"); ?>: <?php echo $item->getName(); ?></nobr>';					
						ars.innerHTML = '<?php echo word("Status: Searching..."); ?>';
						-->
					</SCRIPT>
					<?php
					flushdisplay();
					// Now let's get the data IF we should
					if ($_POST['edit_search_images_miss'] == "always" 
						or ($item->getMainArt() == "")
						or $_POST['edit_search_desc_miss'] == "always" 
						or ($item->getDescription() == "")){
						
						$arr = array();
						$arr = $jzSERVICES->getArtistMetadata($item, true, "array");
						// Now let's see if they want to get art or need to
						if (($_POST['edit_search_images_miss'] == "always" or $item->getMainArt() == "") and $arr['image'] <> ""){
							?>
							<SCRIPT LANGUAGE=JAVASCRIPT><!--\
								i.innerHTML = '<br><center><?php echo word("Last Image Found"). "<br>". $item->getName();?><br><img src="<?php echo $arr["image"]; ?>"><center>';					
								-->
							</SCRIPT>
							<?php
							flushdisplay();
							// Ok, we want the art
							writeArtistMetaData($item, $arr['image'], false, true);
						}
						if (($_POST['edit_search_desc_miss'] == "always" or $item->getDescription() == "") and $arr['bio'] <> ""){
							writeArtistMetaData($item, false, $arr['bio'], true);
						}
					}
				}
			}
			// Now let's look at the album
			if ($item->getPType() == 'album'){
				$parent = $item->getParent();
				$artist = $parent->getName();
				if ($_POST['edit_search_all_albums'] == "on"){
					?>
					<SCRIPT LANGUAGE=JAVASCRIPT><!--\
						ar.innerHTML = '<nobr><?php echo word("Album"); ?>: <?php echo $item->getName(). "<br>". word("Artist"). ": ". $artist; ?></nobr>';			
						ars.innerHTML = '<?php echo word("Status: Searching..."); ?>';					
						-->
					</SCRIPT>
					<?php
					flushdisplay();
					
					$arr = array();
					$arr = $jzSERVICES->getAlbumMetadata($item,true,"array");
					
					// Ok, now should we do the art?
					if (($_POST['edit_search_images_miss'] == "always" or $item->getMainArt() == "") and $arr['image'] <> ""){
						?>
						<SCRIPT LANGUAGE=JAVASCRIPT><!--\
							ars.innerHTML = '<?php echo word("Status: Writing image"); ?>';		
							i.innerHTML = '<br><center><?php echo word("Last Image Found"). "<br>". $item->getName();?><br><img src="<?php echo $arr["image"]; ?>"></center>';			
							-->
						</SCRIPT>
						<?php
						flushdisplay();
						writeAlbumMetaData($item, false, $arr['image']);
					} else {
						unset($arr['image']);
					}
					// Ok, now should we do the description?
					if (($_POST['edit_search_desc_miss'] == "always" or $item->getDescription() == "") and $arr['review'] <> ""){							
						?>
						<SCRIPT LANGUAGE=JAVASCRIPT><!--\
							ars.innerHTML = '<?php echo word("Status: Writing review"); ?>';					
							-->
						</SCRIPT>
						<?php
						flushdisplay();
						writeAlbumMetaData($item, false, false, false, $arr['review']);
						usleep(250000);
					} else {
						unset($arr['review']);
					}
					// Ok, now should we do the year?
					if ($_POST['edit_search_year_miss'] == "always" or $item->getYear() == ""){							
					} else {
						unset($arr['year']);
					}
					// Ok, now should we do the rating?
					if ($_POST['edit_search_rating_miss'] == "always" and $arr['rating'] <> ""){							
						?>
						<SCRIPT LANGUAGE=JAVASCRIPT><!--\
							ars.innerHTML = '<?php echo word("Status: Writing rating"); ?>';					
							-->
						</SCRIPT>
						<?php
						flushdisplay();
						writeAlbumMetaData($item, false, false, false, false, $arr['rating']);
						usleep(250000);
					}
					
					// Now let's write the ID to the database
					if ($arr['id'] <> "" and $arr['id'] <> "NULL"){							
						?>
						<SCRIPT LANGUAGE=JAVASCRIPT><!--\
							ars.innerHTML = '<?php echo word("Status: Updating Amazon ID"); ?>';					
							-->
						</SCRIPT>
						<?php
						if ($allow_filesystem_modify == "true"){
							$fName = $item->getDataPath("String"). "/album.id";
							$handle = @fopen ($fName, "w");
							@fwrite($handle,$arr['id']);				
							@fclose($handle);
						}
						$item->setID($arr['id']);
					}					
					
					// Did they want to write this to the id3 tags?
					if ($allow_id3_modify == "true" and (isset($arr['year']) or isset($arr['image']))){
						?>
						<SCRIPT LANGUAGE=JAVASCRIPT><!--\
							ars.innerHTML = '<?php echo word("Status: Updating tracks..."); ?>';					
							-->
						</SCRIPT>
						<?php
						flushdisplay();

						// Now let's set the meta fields so they get updated for all the tracks
						if (isset($arr['year'])){ $meta['year'] = $arr['year']; }
						if (isset($arr['image'])){
							// Ok, now let's resize this first
							// If art is too big it looks like shit in the players
							$imageData = file_get_contents($arr['image']);

							// but only if the image is larger than 200x200
							$image_info = getimagesize( $arr['image'] );

							// From getimagesize(); 1 = jpeg, 2 = gif
							$mimeType = null;
							$picExt = null;
							if( $image_info[2] == 1 ) {
								$mimeType = "image/jpeg";
								$picExt = "jpg";
							} else if( $image_info[2] == 2 ) { 
								$mimeType = "image/gif";
								$picExt = "gif";
							}
			
							$needsResizing = ( $image_info[0] > 200 || $image_info[1] > 200 );

							if( $needsResizing ) {

								// Now let's write it out
								$file = $include_path. 'temp/tempimage.jpg';
								$dest = $include_path. 'temp/destimage.jpg';
								$handle = fopen($file, "w");
								fwrite($handle,$imageData);
								fclose ($handle);
	
								// Now let's resize; do this for all standard images larger than 200x200
								// Note that if this fails, we just use the original image in the tag
								if ( strcmp( $jzSERVICES->resizeImage($file, "200x200", $dest), $imgName ) != 0 ){
									// Now let's get new data for the tag writing
									unset($imageData);
									$imageData = file_get_contents($dest);
			
									// Reset the mime type, since we're probably converting the image to a jpg
									// regardless of the input type
									$new_image_info = getimagesize( $dest );
									$mimeType = null;
									$picExt = null;
			
									// From getimagesize(); 1 = jpeg, 2 = gif
									if( $new_image_info[2] == 1 ) {
										$mimeType = "image/jpeg";
										$picExt = "jpg";
									} else if( $new_image_info[2] == 2 ) { 
										$mimeType = "image/gif";
										$picExt = "gif";
									} else {
										// currently unsupported type
										$mimeType = null;
									}
								}
	
								// Now let's clean up
								@unlink($file);
								@unlink($dest);
							}

							// Now let's make sure that was valid
							if (strlen($imageData) < 2000 or !$mimeType ){
								$imageData = ""; 
							} else {

								$imgShortName = $item->getName(). ".jpg";
							
								$meta['pic_mime'] = 'image/jpeg';							
								$meta['pic_data'] = $imageData;
								$meta['pic_ext'] = "jpg";
								$meta['pic_name'] = $imgShortName;
							}
						}

						if (isset($arr['image']) or $meta['year'] <> ""){
							// Now let's update
							$item->bulkMetaUpdate($meta,false,false);
						}
					}

					?>
					<SCRIPT LANGUAGE=JAVASCRIPT><!--\
						ars.innerHTML = '<?php echo word("Status: Complete!"); ?>';					
						-->
					</SCRIPT>
					<?php
					flushdisplay();
					unset($arr);
				}
			}
			// Now let's figure out the progress
			$c++;
			?>
			<SCRIPT LANGUAGE=JAVASCRIPT><!--\
				c.innerHTML = '<?php echo word("Progress: "). $c. "/". $total; ?>';					
				-->
			</SCRIPT>
			<?php
			flushdisplay();
		}
		
		// Now let's purge the cache
		?>
		<SCRIPT LANGUAGE=JAVASCRIPT><!--\
			ars.innerHTML = '&nbsp;';					
			c.innerHTML = '&nbsp;';					
			ar.innerHTML = '<?php echo word("Purging cache"). "..."; ?>';			
			i.innerHTML = '&nbsp;';					
			-->
		</SCRIPT>
		<?php
		flushdisplay();
		$display = new jzDisplay();
		if ($node->getPType() == "artist"){
			$display->purgeCachedPage($node);
		} else {
			$parent = $node->getAncestor("artist");
			$display->purgeCachedPage($parent);
		}
				
		?>
		<SCRIPT LANGUAGE=JAVASCRIPT><!--\
			ars.innerHTML = '&nbsp;';					
			c.innerHTML = '&nbsp;';					
			ar.innerHTML = '<?php echo word("Complete!"); ?>';			
			i.innerHTML = '&nbsp;';					
			-->
		</SCRIPT>
		<?php
		echo "<br><center>";
		$this->closeButton(true);
		exit();
	}

	/**
	* Searches for lyrics of the given node
	* 
	* @author Ross Carlson
	* @version 01/18/05
	* @since 01/18/05
	* @param $node The node we are looking at
	*/
	function searchLyrics($node){
		global $jzSERVICES;

		if (!isset($_POST['edit_search_lyrics'])){
			$this->displayPageTop("",word("Retrieving lyrics for"). ":<br>". $node->getName());
			$this->openBlock();
			
			$arr = array();
			$arr['action'] = "popup";
			$arr['ptype'] = "searchlyrics";
			$arr['jz_path'] = $node->getPath("String");
			echo '<form name="lyrics" action="'. urlize($arr). '" method="POST">';
			echo word("Get lyrics for"). ": "; 
			echo '<select name="edit_lyrics_get" class="jz_select">';
			echo '<option value="missing">'. word("Tracks Missing Lyrics	"). '</option>';
			echo '<option value="all">'. word("All Tracks"). '</option>';
			echo '</select>';
			echo '<input type="submit" name="edit_search_lyrics" value="'. word("Go"). '" class="jz_submit">';
			echo '</form>';
			
			$this->closeBlock();			
			exit();
		}
		
		
		
		// First let's display the top of the page and open the main block
		$this->displayPageTop("",word("Retrieving lyrics for"). ":<br>". $node->getName());
		$this->openBlock();
		
		echo '<div id="group"></div>';
		echo '<div id="album"></div>';
		echo '<div id="artist"></div>';
		echo '<div id="current"></div>';
		echo '<div id="status"></div>';
		echo '<div id="progress"></div>';
		
		?>
		<SCRIPT LANGUAGE=JAVASCRIPT><!--\
			gr = document.getElementById("group");
			c = document.getElementById("current");
			s = document.getElementById("status");
			a = document.getElementById("album");
			ar = document.getElementById("artist");
			p = document.getElementById("progress");
			c.innerHTML = '<?php echo word("Please wait while we load the track data..."); ?>';					
			-->
		</SCRIPT>
		<?php
		flushDisplay();
		
		// Now let's see what grouping were on
		$length = 50;
		if (!isset($_SESSION['jz_retag_group'])){
			$_SESSION['jz_retag_group'] = 0;
		} else {
			$_SESSION['jz_retag_group'] = $_SESSION['jz_retag_group'] + $length;
		}
		$allTracks = $node->getSubNodes("tracks",-1);
		?>
		<SCRIPT LANGUAGE=JAVASCRIPT><!--\
			gr.innerHTML = '<nobr><?php echo word("Files"); ?>: <?php echo $_SESSION['jz_retag_group']. " - ". ($_SESSION['jz_retag_group'] + $length). "/". count($allTracks); ?></nobr>';
			-->
		</SCRIPT>
		<?php	
		flushdisplay();
		
		$tracks = array_slice($allTracks,$_SESSION['jz_retag_group'],$length);
		$total = count($tracks); $i=0; $a=0;$start=time();$c=0;
		
		// Now let's add the node for what we are viewing
		$totalCount = 0;
		foreach ($tracks as $track) {
			// Let's give status
			$parent = $track->getParent();
			$album = str_replace("'","",$parent->getName());
			$gparent = $parent->getParent();
			$artist = str_replace("'","",$gparent->getName());
			?>
			<SCRIPT LANGUAGE=JAVASCRIPT><!--\
				c.innerHTML = '<?php echo word("Track"); ?>: <?php echo $track->getName(); ?>';					
				a.innerHTML = '<nobr><?php echo word("Item"); ?>: <?php echo $album; ?> - <?php echo $artist; ?></nobr>';					
				-->
			</SCRIPT>
			<?php
			flushDisplay();
			
			$meta = array();
			$echoVal = "<nobr><b>". word("Track"). ":</b> " . $track->getName();
			
			// Do we want all track or just those missing?
			if ($_POST['edit_lyrics_get'] == "missing"){
				$metaData = $track->getMeta();
				if ($metaData['lyrics'] == ""){
					$meta['lyrics'] = $jzSERVICES->getLyrics($track);
				} else {
					$meta['lyrics'] = "EXIST";
				}
			} else {
				$meta['lyrics'] = $jzSERVICES->getLyrics($track);
			}
			if ($meta['lyrics'] == "EXIST"){
				?>
				<SCRIPT LANGUAGE=JAVASCRIPT><!--\
					s.innerHTML = '<nobr><?php echo word("Status: Exists"); ?></nobr>';
					-->
				</SCRIPT>
				<?php	
			} else if ($meta['lyrics'] <> ""){
				$track->setMeta($meta);
				?>
				<SCRIPT LANGUAGE=JAVASCRIPT><!--\
					s.innerHTML = '<nobr><?php echo word("Status: Found"); ?></nobr>';
					-->
				</SCRIPT>
				<?php				
			} else {
				?>
				<SCRIPT LANGUAGE=JAVASCRIPT><!--\
					s.innerHTML = '<nobr><?php echo word("Status: Not Found"); ?></nobr>';
					-->
				</SCRIPT>
				<?php
			}
			flushDisplay();
			
			// Now let's get the progress
			$progress = round(($c / $total) * 100);
			$totalCount++;
			$progress = $c. "/". $total. " - ". $progress. "%";
			// now let's write it
			?>
			<SCRIPT LANGUAGE=JAVASCRIPT><!--\
				p.innerHTML = '<nobr><?php echo word("Progress"); ?>: <?php echo $progress; ?></nobr>';
				-->
			</SCRIPT>
			<?php
			flushdisplay();
			$c++;
		}
		
		// Now are we done or do we continue?
		if (count($allTracks) < $_SESSION['jz_retag_group']){
			?>
			<SCRIPT LANGUAGE=JAVASCRIPT><!--\
				c.innerHTML = '<?php echo word("Process Complete!"); ?>';
				a.innerHTML = '&nbsp;';
				gr.innerHTML = '&nbsp;';
				ar.innerHTML = '&nbsp;';
				s.innerHTML = '&nbsp;';	
				p.innerHTML = '&nbsp;';	
			</SCRIPT>
			<?php
			unset($_SESSION['jz_retag_group']);
			$this->closeBlock();
			flushDisplay();
			echo '<br><br><center>';
			$this->closeButton();
		} else {
			?>
			<SCRIPT LANGUAGE=JAVASCRIPT><!--\
				c.innerHTML = '<?php echo word("Proceeding, please stand by..."); ?>';
				a.innerHTML = '&nbsp;';
				gr.innerHTML = '&nbsp;';
				ar.innerHTML = '&nbsp;';
				s.innerHTML = '&nbsp;';	
				p.innerHTML = '&nbsp;';		
				-->
			</SCRIPT>
			<?php
			flushdisplay();
			// Now we need to setup our bogus form
			$arr = array();
			$arr['action'] = "popup";
			$arr['ptype'] = "searchlyrics";
			$arr['jz_path'] = $node->getPath("String");
			echo '<form name="lyrics" action="'. urlize($arr). '" method="POST">';
			
			$PostArray = $_POST;
			foreach ($PostArray as $key => $val) {
				echo '<input type="hidden" name="' . htmlentities($key) . '" value="' . htmlentities($val) .'">'. "\n";
			}
			echo '</form>';
			
			?>
			<SCRIPT language="JavaScript">
			document.lyrics.submit();
			</SCRIPT>
			<?php
			exit();
		}
			
			
		
		
		// Now let's close out
		//sleep(3);
		//$this->closeWindow(false);
	}
	
	// This function figures out the page to return too
	// Added 4.6.04 by Ross Carlson
	// Returns the page to go back to
	function returnGoBackPage($page){
		global $row_colors, $cms_mode;
		
		// Now let's split this into an array so we can get all the paramaters
		$pageArray = explode("&",$page);
		
		// Let's split the page name from the paramaters
		$splitArray = explode("?",$pageArray[0]);		
		$pageName = $splitArray[0];

		// Now let's fix up the first one, so we'll have just the URL
		$pageArray[0] = $splitArray[1];
		for ($i=0; $i < count($pageArray); $i++){
			// now let's fix it up
			if (stristr($pageArray[$i],"path")){
				$pageArray[$i] = "";
			}
		}
		// Now let's put it back together
		$page = implode("&",$pageArray);
		
		// Now let's remove any &&
		while (stristr($page,"&&")){
			$page = str_replace("&&","",$page);
		}
		$page = $pageName . "?". $page;
		
		return $page;
	}
	
	// This function will display the complete list of Genres
	// Added 4.6.04 by Ross Carlson
	function displayAllGenre(){
	  global $this_page;
		global $row_colors, $web_root, $root_dir;
		
		// Let's display the top of our page	
		$this->displayPageTop("",word("All Genres"));
		$this->openBlock();
		
		echo "<center>";

		// Now let's give them a list of choices
				
		// Let's give them a search bar.
		
		$url_array = array();
		$url_array['action'] = "popup";
		$url_array['ptype'] = "genre";

		$search = isset($_POST['query']) ? $_POST['query'] : "";
		echo "<form action=\"".urlize($url_array)."\" method=\"post\" name=\"selectGenre\">";
		echo "<input type=\"text\" class=\"jz_input\" size=\"18\" value=\"$search\" name=\"query\">";
		echo '<input class="jz_submit" type="submit" name="'.jz_encode('lookup').'" value="'. word("Go"). '">';
		echo "</form><br>";
		// That's all for the search bar.
		
		$i=97; $c=2;
		

		$url_array['g'] = "#";
		echo '<a href="'. urlize($url_array).'">1-10</a> | ';
		while ($i < 123){
		  $url_array['g'] = chr($i);
			echo '<a href="'. urlize($url_array). '">'. strtoupper(chr($i)). '</a>';
			if ($c % 9 == 0){ echo "<br>"; } else { echo " | "; }
			$i++;
			$c++;
		}
		echo "<br>";
		
		// Now let's setup our form

		echo '<form action="'. urlize($url_array). '" method="post" name="selectGenre">';
		// Now let's set so we'll know where to go back to
		echo '<input type="hidden" name="return" value="'. $_GET['return']. '">';

		// See if they ran a search.
		if ($search != "") {
			// Now let's get all the genres from our cache file
			$root = &new jzMediaNode();
			$matches = $root->search($search, "nodes", distanceTo("genre"));
			// arrayify search.
			echo '<select name="' . jz_encode("chosenPath") . '"size="18" class="jz_select" style="width: 200px" onChange="submit()">';
			for ($i=0; $i < count($matches); $i++){
				echo '<option value="'. htmlentities(jz_encode($matches[$i]->getPath("String"))).'">'. $matches[$i]->getName();
			}
			echo "</select>";
		}
		// End search stuff.
		
		// Now let's see if they wanted a letter or not
		else if (isset($_GET['g'])){
			// Now let's get all the artists from our cache file
			$root = &new jzMediaNode();
			$matches = $root->getAlphabetical($_GET['g'],"nodes",distanceTo("genre"));
			echo '<select name="' . jz_encode("chosenPath") . '" size="18" class="jz_select" style="width: 200px" onChange="submit()">';
			for ($i=0; $i < count($matches); $i++){
				echo '<option value="'. htmlentities(jz_encode($matches[$i]->getPath("String"))).'">'. $matches[$i]->getName();
			}
			echo '</select>';
		}
		echo "</form>";
		echo "<br><br>";
		$this->closeButton();
		echo "</center>";
		
		$this->closeBlock();
		exit();
	}
		
	function displayAllTrack(){
	  global $this_page;
		global $row_colors, $web_root, $root_dir,$embedded_player,$jzUSER;
		
		// Let's display the top of our page	
		$this->displayPageTop("",word("All Tracks"));
		$this->openBlock();
		
		echo "<center>";

		// Now let's give them a list of choices
				
		// Let's give them a search bar.
		
		$url_array = array();
		$url_array['action'] = "popup";
		$url_array['ptype'] = "track";

		$search = isset($_POST['query']) ? $_POST['query'] : "";
		echo "<form action=\"".urlize($url_array)."\" method=\"post\" name=\"selectTrack\">";
		echo "<input type=\"text\" class=\"jz_input\" size=\"18\" value=\"$search\" name=\"query\">";
		echo '<input class="jz_submit" type="submit" name="'.jz_encode('lookup').'" value="'. word("Go"). '">';
		echo "</form><br>";
		// That's all for the search bar.
		
		$i=97; $c=2;
		

		$url_array['g'] = "#";
		echo '<a href="'. urlize($url_array).'">1-10</a> | ';
		while ($i < 123){
		  $url_array['g'] = chr($i);
			echo '<a href="'. urlize($url_array). '">'. strtoupper(chr($i)). '</a>';
			if ($c % 9 == 0){ echo "<br>"; } else { echo " | "; }
			$i++;
			$c++;
		}
		echo "<br>";
		
		// Now let's setup our form

		echo '<form action="'. urlize($url_array). '" method="post" name="selectTrack"';
		if (checkPermission($jzUSER,'embedded_player') === true) {
		  echo ' target="embeddedPlayer"';
		}
		echo '>';
		// Now let's set so we'll know where to go back to
		echo '<input type="hidden" name="return" value="'. $_GET['return']. '">';

		// See if they ran a search.
		if ($search != "") {
			// Now let's get all the genres from our cache file
			$root = &new jzMediaNode();
			$matches = $root->search($search, "tracks", -1);
			// arrayify search.
			echo '<input type="hidden" name="' . jz_encode('jz_type') . '" value="' . jz_encode('track') . '"';
			if (checkPermission($jzUSER,'embedded_player') === true) {
			  echo '<select name="' . jz_encode("chosenPath") . '"size="18" class="jz_select" style="width: 200px" onChange="openMediaPlayer('."''".',300,150); submit()">';
			} else {
			echo '<select name="' . jz_encode("chosenPath") . '"size="18" class="jz_select" style="width: 200px" onChange="submit()">';
			}
			for ($i=0; $i < count($matches); $i++){
				echo '<option value="'. htmlentities(jz_encode($matches[$i]->getPath("String"))).'">'. $matches[$i]->getName();
			}
			echo "</select>";
		}
		// End search stuff.
		
		// Now let's see if they wanted a letter or not
		else if (isset($_GET['g'])){
			// Now let's get all the artists from our cache file
			$root = &new jzMediaNode();
			$matches = $root->getAlphabetical($_GET['g'],"tracks",-1);
			echo '<input type="hidden" name="' . jz_encode('jz_type') . '" value="' . jz_encode('track') . '"';
			echo '<select name="' . jz_encode("chosenPath") . '" size="18" class="jz_select" style="width: 200px" onChange="submit()">';
			for ($i=0; $i < count($matches); $i++){
				echo '<option value="'. htmlentities(jz_encode($matches[$i]->getPath("String"))).'">'. $matches[$i]->getName();
			}
			echo '</select>';
		}
		echo "</form>";
		echo "<br><br>";
		$this->closeButton();
		echo "</center>";
		
		$this->closeBlock();
		exit();
	}

	// This function will display the complete list of artists
	function displayAllArtists(){
		global $row_colors, $web_root, $root_dir, $this_page;
		
		// Let's display the top of our page	
		$this->displayPageTop("",word("All Artists"));
		$this->openBlock();
		
		echo "<center>";
		
		// Now let's give them a list of choices

		// Let's give them a search bar.
		$ua = array();
		$ua['action'] = "popup";
		$ua['ptype'] = "artist";

		$search = isset($_POST['query']) ? $_POST['query'] : "";
		echo "<form action=\"" . urlize($ua) . "\" method=\"post\" name=\"selectArtist\">";
		echo "<input type=\"text\" class=\"jz_input\" size=\"18\" value=\"$search\" name=\"query\">";
		echo '<input class="jz_submit" type="submit" name="'.jz_encode('lookup').'" value="'. word("Go"). '">';
		echo "</form><br>";
		// That's all for the search bar.

		$i=97; $c=2;
		$ua['i'] = "#";
		echo '<a href="'. urlize($ua).'">1-10</a> | ';
		while ($i < 123){
		  $ua['i'] = chr($i);
			echo '<a href="'. urlize($ua). '">'. strtoupper(chr($i)). '</a>';
			if ($c % 9 == 0){ echo "<br>"; } else { echo " | "; }
			$i++;
			$c++;
		}
		echo "<br>";
		
		// Now let's setup our form
		echo '<form action="'. urlize($ua). '" method="post" name="selectArtist">';
		// Now let's set so we'll know where to go back to
		if (isset($_GET['return'])) {
		  echo '<input type="hidden" name="return" value="'. $_GET['return']. '">';
		}
		
		// See if they ran a search.
		if ($search != "") {
			// Now let's get all the genres from our backend
			$root = &new jzMediaNode();
			$matches = $root->search($search, "nodes", distanceTo("artist"));
			// arrayify search.
			echo '<select name="' . jz_encode("chosenPath") . '"size="18" class="jz_select" style="width: 200px" onChange="submit()">';
			for ($i=0; $i < count($matches); $i++){
				echo '<option value="'. jz_encode($matches[$i]->getPath("String")).'">'. $matches[$i]->getName();
			}
			echo "</select>";
		}
		// End search stuff.
		
		// Now let's see if they wanted a letter or not
		else if (isset($_GET['i'])){
			// Now let's get all the artists from our cache file
			$root = &new jzMediaNode();
			$matches = $root->getAlphabetical($_GET['i'],"nodes",distanceTo("artist"));
			echo '<select name="' . jz_encode("chosenPath") . '"size="18" class="jz_select" style="width: 200px" onChange="submit()">';
			for ($i=0; $i < count($matches); $i++){
				echo '<option value="'. jz_encode($matches[$i]->getPath("String")).'">'. $matches[$i]->getName();
			}
			echo '</select>';
		}
		echo "</form>";
		echo "<br><br>";
		$this->closeButton();
		echo "</center>";
		
		$this->closeBlock();
		exit();
	}
	
	// This function will display the complete list of artists
	function displayAllAlbums(){
		global $row_colors, $web_root, $root_dir, $directory_level;
		
		// Let's display the top of our page	
		$this->displayPageTop("",word("All Albums"));
		$this->openBlock();
		
		echo "<center>";
		
		// Now let's give them a list of choices
		$ua = array();
		$ua['action'] = "popup";
		$ua['ptype'] = "album";

		
		// Let's give them a search bar.
		$search = isset($_POST['query']) ? $_POST['query'] : "";
		echo "<form action=\"".urlize($ua)."\" method=\"post\" name=\"selectAlbum\">";
		echo "<input type=\"text\" class=\"jz_input\" size=\"18\" value=\"$search\" name=\"query\">";
		echo '<input class="jz_submit" type="submit" name="'.jz_encode('lookup').'" value="'. word("Go"). '">';
		echo "</form><br>";
		// That's all for the search bar.
		
		$i=97; $c=2;
		$ua['a'] = "#";
		echo '<a href="'. urlize($ua).'">1-10</a> | ';
		while ($i < 123){
		  $ua['a'] = chr($i);
			echo '<a href="'. urlize($ua). '">'. strtoupper(chr($i)). '</a>';
			if ($c % 9 == 0){ echo "<br>"; } else { echo " | "; }
			$i++;
			$c++;
		}
		echo "<br>";
		
		// Now let's setup our form
		echo '<form action="'. urlize($ua) . '" method="post" name="selectAlbum">';
		// Now let's set so we'll know where to go back to
		echo '<input type="hidden" name="return" value="'. $_GET['return']. '">';
		
		// See if they ran a search.
		if ($search != "") {
			// Now let's get all the genres from our cache file
			$root = &new jzMediaNode();
			$matches = $root->search($search, "nodes", distanceTo("album"));

			// arrayify search.
			echo '<select name="' . jz_encode("chosenPath") . '"size="18" class="jz_select" style="width: 200px" onChange="submit()">';
			for ($i=0; $i < count($matches); $i++){
				$parent = $matches[$i]->getNaturalParent();
				echo '<option value="'. jz_encode(htmlentities($matches[$i]->getPath("String"))).'">'. $matches[$i]->getName() . " (" . $parent->getName() . ")";
			}
			echo "</select>";
		}
		// End search stuff.
		
		// Now let's see if they wanted a letter or not
		else if (isset($_GET['a'])){
			// Now let's get all the artists from our cache file
			$root = &new jzMediaNode();
			$matches = $root->getAlphabetical($_GET['a'],"nodes",distanceTo("album"));
			echo '<select name="' . jz_encode("chosenPath") . '"size="15" class="jz_select" style="width: 200px" onChange="submit()">';
			for ($i=0; $i < count($matches); $i++){
				$parent = $matches[$i]->getNaturalParent();
				echo '<option value="'. jz_encode(htmlentities($matches[$i]->getPath("String"))).'">'. $matches[$i]->getName() . " (" . $parent->getName() . ")";
			}
			echo '</select>';
		}
		echo "</form>";
		echo "<br><br>";
		$this->closeButton();
		echo "</center>";

		$this->closeBlock();
		exit();
	}
		
	/**
	* Displays the configuration system
	* 
	* @author Ben Dodson
	* @version 09/15/04
	* @since 09/15/04
	*/
	function displayinstMozPlug(){
		global $web_root, $root_dir, $site_title;
		
		// Let's display the top of our page	
		$this->displayPageTop();
		
		// Now let's execute the plugin creation
		include('extras/mozilla.php');
		makePlugin();
		
		// Now let's set the JavaScript that will actually install the plugin
		$weblink = "http://".$_SERVER['HTTP_HOST']."${root_dir}";
		
		?>
			<script>
				function addEngine()
				{
					if ((typeof window.sidebar == "object") &&
					  (typeof window.sidebar.addSearchEngine == "function"))
					{
						window.sidebar.addSearchEngine(
							"<?php echo $weblink; ?>/data/jinzora.src",
							"<?php echo $weblink; ?>/data/jinzora.gif",
							"<?php echo $site_title; ?>",
							"Multimedia" );  }
					else
					{
						alert('<?php echo word("Mozilla M15 or later is required to add a search engine"); ?>');
					}
				}
			</script>
		<?php
		
		echo '<br><center>';
		echo word("Click below to install the Mozilla Search Plugin<br>(You will be prompted by Mozilla<br>to complete the install, please click 'Ok')");
		echo '<br><br><br><input type="button" onClick="addEngine();window.close();" value="'. word("Install Now"). '" class="jz_submit"><center>';
	}
}
?>
