<?php
	// Connect to MySQL
	function connect_db() {
		mysql_connect(constant("mysql_server"), constant("mysql_user"), constant("mysql_password"));
		mysql_select_db(constant("mysql_database"));	
	}
	
	// Close connexion to MySQL
	function close_db() {
		mysql_close();	
	}
	
	// get image url from the id
	function getimage_url($imgid) {
		// Request image
		$request = "SELECT concat(dossier,'/',nomImage) FROM image WHERE idImage=" . $imgid;
		
		// Run query
		$result = mysql_query($request);		
		
		// Fetch row
		while ($row = mysql_fetch_array($result)) {
			// Convert to object
			$url = $row[0];
		}

		// Free result
		mysql_free_result($result);		
		
		return constant("prefix_img_medium") . $url;
	}
	
	// Replace img tags by the equivalent URL
	function subst_images($content) {
		// Look for the "[img=" tag
		$pos = stripos($content, "[img=");
		while ($pos !== false) {
			// Get img id
			$endid = $pos + 5;
			while($content[$endid] != ' ') $endid = $endid + 1;
			$imgid = substr($content, $pos + 5, $endid - $pos - 5);
			
			// Get end of img tag
			while($content[$endid] != ']') $endid = $endid + 1;
			
			// Strip image
			$content = substr($content, 0, $pos) . "<br/><img src='" . getimage_url($imgid) . "'/><br/>" . substr($content, $endid+1);
			
			// Next image
			$pos = stripos($content, "[img=");
		}
		
		return $content;
	}
	
	// Build Resume of tags
	function clean_resume($content) {
		// Replace [img ... ] tag
		$content = subst_images($content);
		
		// Remove HTML tags
		$content = strip_tags($content);
		
		// Get 256 characters only but try to cut on a whitespace
		$cutpos = constant("max_resume_size");
		if ($cutpos < strlen($content))
		{
			while ($content[$cutpos-1] != ' ')
				$cutpos = $cutpos - 1;
		}
		$content = substr($content, 0, $cutpos);
		
		// Add ...
		$content = $content . "...";
		
		return $content;
	}

	// Decode html chars
	function decode_html($content) {
		$content = html_entity_decode($content, ENT_QUOTES, "UTF-8");
		$content = htmlspecialchars_decode($content, ENT_QUOTES);
		
		return $content;
	}
?>