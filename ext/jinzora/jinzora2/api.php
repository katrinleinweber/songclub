<?php 
	define('JZ_SECURE_ACCESS','true');
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
	* - This page handles all API requests for add-on services
	*
	* @since 04.14.05
	* @author Ross Carlson <ross@jinzora.org>
	* @author Ben Dodson <ben@jinzora.org>
	*/
	
	// Let's set the error reporting level
	//@error_reporting(E_ERROR);
	
	// Let's include ALL the functions we'll need
	// Now we'll need to figure out the path stuff for our includes
	// This is critical for CMS modes
	$include_path = ""; $link_root = ""; $cms_type = ""; $cms_mode = "false";
  $backend = ""; $jz_lang_file = ""; $skin = ""; $my_frontend = "";
	
	include_once('system.php');		
	include_once('settings.php');	
	include_once('backend/backend.php');	
	include_once('playlists/playlists.php');
	include_once('lib/general.lib.php');
	include_once('lib/jzcomp.lib.php');
	include_once('services/class.php');
	
	$skin = "slick";
	$image_dir = $root_dir. "/style/$skin/";
	include_once('frontend/display.php');
	include_once('frontend/blocks.php');
	include_once('frontend/icons.lib.php');
	include_once('frontend/frontends/slick/blocks.php');
	include_once('frontend/frontends/slick/settings.php');
	
	
	
	$this_page = setThisPage();
	$enable_page_caching = "false";

	
	
	
	// Let's create our user object for later
	$jzUSER = new jzUser();
	
	// Let's make sure this user has the right permissions
	if ($jzUSER->getSetting("view") === false || isset($_GET['user'])) {
		if (isset($_GET['user'])) {
			$store_cookie = true;
			// Are they ok?
			if ($jzUSER->login($_GET['user'],$_GET['pass'],$store_cookie) === false) {
				echoXMLHeader();
				echo "<login>false</login>";
				echoXMLFooter();
				exit();
			}
		} else {
			// Nope, error...
			echoXMLHeader();
			echo "<login>false</login>";
			echoXMLFooter();
			exit();
		}
	}
	
	// Let's create our services object
	// This object lets us do things like get metadata, resize images, get lyrics, etc
	$jzSERVICES = new jzServices();
	$jzSERVICES->loadStandardServices();
	$blocks = new jzBlocks();
	$display = new jzDisplay();
	$jz_path = $_GET['jz_path'];
	
	// Now let's see what they want
	switch($_GET['request']){
		case "genres":
			return listAllGenres();
		break;
		case "artists":
			return listAllSubNode("artist");
		break;
		case "albums":
			return listAllSubNode("album");
		break;
		case "curtrack":
			return getCurrentTrack();
		break;
		case "search":
			return search();
		break;
		case "stylesheet":
			echo '<link rel="stylesheet" title="slick" type="text/css" media="screen" href="'. $root_dir. '/style/'. $skin. '/default.php">';
		break;
		case "javascript":
			$display->handleAJAX();
		break;
		
		case "artistAlbumsBlock":
		case "artistProfileBlock":
		case "displaySlickSampler":
		case "displaySlickAllTracks":
		case "artistAlbumArtBlock":
		case "albumAlbumBlock":
		case "albumTracksBlock":
		case "albumOtherAlbumBlock":
		case "blockUser":
		case "blockNowStreaming":
		case "blockWhoIsWhere":
		case "blockSearch":
		case "blockPlaylists":
		case "blockBrowsing":
		case "blockOptions":
		case "slickHeaderBlock":
		case "blockLogo":
			$node = new jzMediaNode($_GET['jz_path']);
			$blocks->$_GET['request']($node);
		break;


		
		default:
			echoXMLHeader();
			echo "<login>true</login>";
			echoXMLFooter();
			exit();
		break;
	}
	
	
	
	
	// These are the functions for the API
	
	
	/**
	* 
	* Searches the API and returns the results
	*
	* @author Ross Carlson
	* @since 4/21/05
	* 
	**/
	function search(){
		global $jzUSER, $this_site, $root_dir;
		
		// What kind of output?
		if (isset($_GET['type'])){
			$type = $_GET['type'];
		} else {
			$type = "xml";
		}
		
		// Let's setup our objects
		// The display object is just a set of functions related to display
		// Like returning images and links
		$display = new jzDisplay();
		
		// Let's search
		// This will search the API and return an array of objects
		$results = handleSearch($_GET['search'], $_GET['searchtype']);
		
		// Now let's make sure we had results
		if (count($results) == 0){
			// Now let's output
			switch ($type){
				case "xml":
					echoXMLHeader();
					echo "  <search>false</search>\n";
					echoXMLFooter();
				break;
			}
		}
		
		// Now let's break our nodes and tracks out
		foreach ($results as $val) {
			// We look at objects as leafs or nodes
			// Leafs are the last branch on the tree
			// So those would be tracks/videos
			if ($val->isLeaf()) {
				$tracks[] = $val;	
			} else {
				// Nodes are everything above the leafs
				// So albums, artists, and genres
				$nodes[] = $val;
			}
		}
		
		// Now let's output
		switch ($type){
			case "xml":
				echoXMLHeader();
				echo "  <search>\n";
				echo "    <tracks>\n";
				// Now let's display the tracks
				foreach($tracks as $track){
					// Let's get all the data for display
					// The getMeta function lets us get all the metadata (length, bitrate, etc) from a tack
					$meta = $track->getMeta();
					
					// Now we go up from this item to get it's "ancestors" 
					// The reason we do this is to make sure we get the right thing
					// for it, not just the one above.  This is important when using multidisk
					// albums where their parent would be DISC1 not AlbumName
					// You can do this recursively if you want
					$album = $track->getAncestor("album");
					$artist = $album->getAncestor("artist");
					$genre = $artist->getParent();
					
					// Now let's display
					echo "      <track>\n";
					echo "        <name>". $meta['title']. "</name>\n";
					echo "        <metadata>\n";
					echo "          <filename>". $meta['filename']. "</filename>\n";
					echo "          <tracknumber>". $meta['number']. "</tracknumber>\n";
					echo "          <length>". $meta['length']. "</length>\n";
					echo "          <bitrate>". $meta['bitrate']. "</bitrate>\n";
					echo "          <samplerate>". $meta['frequency']. "</samplerate>\n";
					echo "          <filesize>". $meta['size']. "</filesize>\n";
					echo "        </metadata>\n";
					echo "        <album>". $album->getName(). "</album>\n";
					echo "        <artist>". $artist->getName(). "</artist>\n";
					echo "        <genre>". $genre->getName(). "</genre>\n";
					echo "      </track>\n";
				}
				echo "    </tracks>\n";
				echo "    <nodes>\n";
				// Now let's display the nodes
				foreach($nodes as $node){
					// We do the same things here by getting item off the node
					// $art would be the image for the item we're looking at
					// In this case we want the art for the match we found
					// This works on ALL objects if they have art
					$art = $node->getMainArt();
					
					echo "      <node>\n";
					echo "        <name>". $node->getName(). "</name>\n";
					echo "        <type>". $node->getPType(). "</type>\n";
					echo "        <link>". $this_site. $root_dir. "/". xmlUrlClean($display->link($node,false,false,false,true,true)). "</link>\n";
					echo "        <image>";
					if ($art){
						$this_site. $root_dir. "/". xmlUrlClean($display->returnImage($artist->getMainArt(),false,false, false, "limit", false, false, false, false, false, "0", false, true, true));
					}
					echo "        </image>\n"; 
					
					echo "      </node>\n";
				}
				echo "    </nodes>\n";
				echo "  </search>\n";
				echoXMLFooter();
			break;
			case "display":
				// Ok, let's redirect them to the search page
				header("Location: ". $this_site. $root_dir. "/index.php?doSearch=true&search_query=jam&search_type=ALL");
			break;
		}
	}
	
	
	/**
	* 
	* Echos out the XML header information
	*
	* @author Ross Carlson
	* @since 3/31/05
	* 
	**/
	function getCurrentTrack(){
		global $jzUSER, $this_site, $root_dir;
		
		// What kind of output?
		if (isset($_GET['type'])){
			$type = $_GET['type'];
		} else {
			$type = "xml";
		}
		
		// Now let's set the width
		if (isset($_GET['imagesize'])){
			$imagesize = $_GET['imagesize']. "x". $_GET['imagesize'];
		} else {
			$imagesize = "150x150";
		}
		
		// Now let's see when to stop
		if (isset($_GET['count'])){
			$total = $_GET['count'];
		} else {
			$total = 1;
		}

		// Let's start the page
		if ($type == "xml"){
			echoXMLHeader();
		}
		
		// Now let's get the data
		$be = new jzBackend();
		$ar = $be->getPlaying();
		$display = new jzDisplay();
		
		$fullList = "";
		$found=false;
		foreach($ar as $user=>$tracks) {
			$name = $jzUSER->getSetting("full_name");
			if ($name == ""){
				$name = $jzUSER->lookupName($user); // that's the user name
			}			
			$i=0;			
			foreach($tracks as $time=>$song) {
				// Now let's make sure this is the right user
				if ($name == $jzUSER->getName()){
					// Now let's make sure we don't list this twice
					if (stristr($fullList,$song['path']. "-". $name. "\n")){continue;}
					$fullList .= $song['path']. "-". $name. "\n";
					
					// Now let's create the objects we need
					$node = new jzMediaNode($song['path']);
					$track = new jzMediaTrack($song['path']);
					$album = $node->getParent();
					$artist = $album->getParent();
					$meta = $track->getMeta();
					
					// Now, now let's echo out the data
					switch ($type){
						case "xml":
							echo "  <item>\n";
							echo "    <title>". $this_site. $root_dir. "/". xmlUrlClean($meta['title']). "</title>\n";
							echo "    <album>\n";
							echo "      <name>". $this_site. $root_dir. "/". xmlUrlClean($album->getName()). "</name>\n";
							echo "      <image>". $this_site. $root_dir. "/". xmlUrlClean($display->returnImage($album->getMainArt(),$album->getName(),false, false, "limit", false, false, false, false, false, "0", false, true, true)). "</image>\n";
							echo "    </album>\n";					
							echo "    <artist>\n";
							echo "      <name>". $this_site. $root_dir. "/". xmlUrlClean($artist->getName()). "</name>\n";
							echo "      <image>". $this_site. $root_dir. "/". xmlUrlClean($display->returnImage($artist->getMainArt(),$artist->getName(),false, false, "limit", false, false, false, false, false, "0", false, true, true)). "</image>\n";
							echo "    </artist>\n";
							echo "  </item>\n";
						break;
						case "html":
							if (isset($_GET['align'])){
								if ($_GET['align'] == "center"){
									echo "<center>";
								}
							}
							echo $meta['title']. "<br>";
							echo $album->getName(). "<br>";
							echo $this_site. $root_dir. "/". $display->returnImage($album->getMainArt(),$album->getName(),false, false, "limit", false, false, false, false, false, "0", false, true, true). "<br>";
							echo $artist->getName(). "<br>";
							echo $display->returnImage($artist->getMainArt(),$artist->getName(),false, false, "limit", false, false, false, false, false, "0", false, true, true). "<br>";
						break;
						case "mt":
							$art = $album->getMainArt($imagesize);
							if ($art){					
								// Now let's try to get the link from the amazon meta data service
								if ($_GET['amazon_id'] <> ""){
									$jzService = new jzServices();		
									$jzService->loadService("metadata", "amazon");
									$id = $jzService->getAlbumMetadata($album, false, "id");	
									
									echo '<a target="_blank" href="http://www.amazon.com/exec/obidos/tg/detail/-/'. $id. '/'. $_GET['amazon_id']. '/">';
								}
								$display->image($art,$album->getName(),150,false,"limit");	
								if ($_GET['amazon_id'] <> ""){
									echo '</a>';
								}
								echo "<br>";
							}
							echo $meta['title']. "<br>";
							if ($_GET['amazon_id'] <> ""){
								$jzService = new jzServices();		
								$jzService->loadService("metadata", "amazon");
								$id = $jzService->getAlbumMetadata($album, false, "id");	
								
								echo '<a target="_blank" href="http://www.amazon.com/exec/obidos/tg/detail/-/'. $id. '/'. $_GET['amazon_id']. '/">'. $album->getName(). "</a><br>";
							} else {
								echo $album->getName(). "<br>";
							}
							echo $artist->getName(). "<br>";
						break;
					}
					$found=true;
					// Now should we stop?
					$i++;
					if ($i >= $total){ break; }
				}
			}
		}
		
		if (!$found){
			// Ok, we didn't find anything so let's get the last thing they played...
			$be = new jzBackend();
			$history = explode("\n",$be->loadData("playhistory-". $jzUSER->getID()));
			$track = new jzMediatrack($history[count($history)-1]);
			$album = $track->getParent();
			$artist = $album->getParent();
			$meta = $track->getMeta();
			
			// Now, now let's echo out the data
			switch ($type){
				case "xml":
					echo "  <item>\n";
					echo "    <title>". $this_site. $root_dir. "/". xmlUrlClean($meta['title']). "</title>\n";
					echo "    <album>\n";
					echo "      <name>". $this_site. $root_dir. "/". xmlUrlClean($album->getName()). "</name>\n";
					echo "      <image>". $this_site. $root_dir. "/". xmlUrlClean($display->returnImage($album->getMainArt(),$album->getName(),false, false, "limit", false, false, false, false, false, "0", false, true, true)). "</image>\n";
					echo "    </album>\n";					
					echo "    <artist>\n";
					echo "      <name>". $this_site. $root_dir. "/". xmlUrlClean($artist->getName()). "</name>\n";
					echo "      <image>". $this_site. $root_dir. "/". xmlUrlClean($display->returnImage($artist->getMainArt(),$artist->getName(),false, false, "limit", false, false, false, false, false, "0", false, true, true)). "</image>\n";
					echo "    </artist>\n";
					echo "  </item>\n";
				break;
				case "html":
					if (isset($_GET['align'])){
						if ($_GET['align'] == "center"){
							echo "<center>";
						}
					}
					echo $meta['title']. "<br>";
					echo $album->getName(). "<br>";
					echo $this_site. $root_dir. "/". $display->returnImage($album->getMainArt(),$album->getName(),false, false, "limit", false, false, false, false, false, "0", false, true, true). "<br>";
					echo $artist->getName(). "<br>";
					echo $display->returnImage($artist->getMainArt(),$artist->getName(),false, false, "limit", false, false, false, false, false, "0", false, true, true). "<br>";
				break;
				case "mt":
					if (isset($_GET['align'])){
						if ($_GET['align'] == "center"){
							echo "<center>";
						}
					}
					$art = $album->getMainArt($imagesize);
					if ($art){					
						// Now let's try to get the link from the amazon meta data service
						if ($_GET['amazon_id'] <> ""){
							$jzService = new jzServices();		
							$jzService->loadService("metadata", "amazon");
							$id = $jzService->getAlbumMetadata($album, false, "id");	
							
							echo '<a target="_blank" href="http://www.amazon.com/exec/obidos/tg/detail/-/'. $id. '/'. $_GET['amazon_id']. '/">';
						}
						$display->image($art,$album->getName(),150,false,"limit");	
						if ($_GET['amazon_id'] <> ""){
							echo '</a>';
						}
						echo "<br>";
					}
					echo $meta['title']. "<br>";
					if ($_GET['amazon_id'] <> ""){
						$jzService = new jzServices();		
						$jzService->loadService("metadata", "amazon");
						$id = $jzService->getAlbumMetadata($album, false, "id");	
						
						echo '<a target="_blank" href="http://www.amazon.com/exec/obidos/tg/detail/-/'. $id. '/'. $_GET['amazon_id']. '/">'. $album->getName(). "</a><br>";
					} else {
						echo $album->getName(). "<br>";
					}
					echo $artist->getName(). "<br>";
				break;
			}
		}
		
		// Now let's close out
		switch ($type){
			case "xml":
				echoXMLFooter();
			break;
			case "html":
				echo '<a target="_blank" title="Jinzora :: Free Your Media!" href="http://www.jinzora.com"><img src="http://www.jinzora.com/downloads/button-stream.gif" border="0"></a>';
			break;
			case "mt":
				echo '<a target="_blank" title="Jinzora :: Free Your Media!" href="http://www.jinzora.com"><img src="http://www.jinzora.com/downloads/button-stream.gif" border="0"></a>';
			break;
		}
		
		if (isset($_GET['align'])){
			if ($_GET['align'] == "center"){
				echo "</center>";
			}
		}
	}
	
	
	/**
	* 
	* Echos out the XML header information
	*
	* @author Ross Carlson
	* @since 3/31/05
	* 
	**/
	function echoXMLHeader(){
		header("Content-type: text/xml");
		echo '<?xml version="1.0" encoding="ISO-8859-1"?>'. "\n";
		echo '<jinzora>'. "\n";
	}	
	
	/**
	* 
	* Echos out the XML footer information
	*
	* @author Ross Carlson
	* @since 3/31/05
	* 
	**/
	function echoXMLFooter(){
		echo '</jinzora>'. "\n";
	}	
	
	/**
	* 
	* Cleans an XML url for display
	*
	* @author Ross Carlson
	* @since 3/31/05
	* 
	* @param $string The string to clean
	* @return Returns the cleaned string
	**/
	function xmlUrlClean($string){
		//$string = urlencode($string);
		$string = str_replace("&","&amp;",$string);
		$string = str_replace('api.php',"index.php",$string);
				
		return $string;
	}
	
	
	/**
	* 
	* Generates an XML list of all genres
	*
	* @author Ross Carlson
	* @since 3/31/05
	* @return Returns a XML formatted list of all genres
	* 
	**/
	function listAllGenres(){
		global $this_site, $root_dir;
		
		// Let's setup the display object
		$display = new jzDisplay();
		
		// Let's echo out the XML header
		echoXMLHeader();
	
		// Let's get all the nodes
		$node = new jzMediaNode();
		
		// Now let's get each genre
		$nodes = $node->getSubNodes("nodes");
		foreach ($nodes as $item){
			echo '  <genre name="'. xmlUrlClean($item->getName()). '">'. "\n";
			echo '    <link>'. $this_site. $root_dir. "/". xmlUrlClean($display->link($item,false,false,false,true,true)). '</link>'. "\n";
			echo "  </genre>\n";
		}
		
		echoXMLFooter();
	}
	
	
	/**
	* 
	* Generates an XML list of all artists
	*
	* @author Ross Carlson
	* @since 3/31/05
	* @return Returns a XML formatted list of all genres
	* 
	**/
	function listAllSubNode($type){
		global $this_site, $root_dir, $jzSERVICES;
		
		// Let's setup the display object
		$display = new jzDisplay();
		
		// Let's echo out the XML header
		echoXMLHeader();
	
		// Let's get all the nodes
		$node = new jzMediaNode();
		
		// Now let's get each genre
		$nodes = $node->getSubNodes("nodes",distanceTo($type));
		sortElements($nodes,"name");
		foreach ($nodes as $item){
			echo '  <'. $type. ' name="'. xmlUrlClean($item->getName()). '">'. "\n";
			echo '    <link>'. $this_site. $root_dir. "/". xmlUrlClean($display->link($item,false,false,false,true,true)). '</link>'. "\n";
			// Now did they want full details?
			if ($_GET['full'] == "true"){
				if (($art = $item->getMainArt()) !== false){
					$image = xmlUrlClean($display->returnImage($art,false,false,false,"limit",false,false,false,false,false,"0",false,true));
				} else {
					$image = "";
				}
				echo '    <image>'. $image. '</image>'. "\n";
				echo '    <desc><![CDATA['. $item->getDescription(). ']]></desc>'. "\n";
			}
			
			// Now let's close out
			echo "  </". $type. ">\n";
			flushdisplay();
		}
		
		echoXMLFooter();
	}
	

	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
?>