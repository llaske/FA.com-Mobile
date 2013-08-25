<?php 
	//-------------------------------------------------
	// ENTITY:     articles/commentaires
	// METHOD:     GET
	// PARAMETERS: id
	// RETURN:     array of ArticleCommentaire object AS JSON 
	//-------------------------------------------------
	
	// Set return type as JSON
	header('Content-type: application/json');

	// Includes
	include 'fa_constants.php';
	include 'fa_entity.php';
	include 'fa_util.php';
	
	// Init array
	$comments = array();

	// Must filter on id	
	if(!isset($_GET['id'])||empty($_GET['id'])) {	
		// id not found
		echo encode_json($comments);
		return;
	}
	$id = $_GET['id'];
	
	// Connect to database
	connect_db();

	// Run query for current id
	$request = "SELECT idTrackback, nomTb, commentaireTb, FROM_UNIXTIME(timestampTb)
		FROM trackback
		WHERE idRedaction = %s
		ORDER BY timestampTb DESC";
	$result = mysql_query(sprintf($request,$id));

	// Loop on each comments
	$i = 0;
	while ($row = mysql_fetch_array($result)) {
		// Convert to object
		$comment = new ArticleCommentaire();  
		$comment->id = $row['idTrackback'];
		$comment->nom = utf8_encode($row['nomTb']);
		$comment->contenu = utf8_encode($row['commentaireTb']);
		$comment->date = utf8_encode(getDateTimeFromDateTimeSQL($row[3],'d/m/Y à H:i'));
		
		// Store in array
		$comments[$i] = $comment;
		$i = $i + 1;		
	}

	// Return JSON for all comments
	echo encode_json($comments);
?>
