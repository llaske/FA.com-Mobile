<?php 
	//-------------------------------------------------
	// ENTITY:     articles/contenu
	// METHOD:     GET
	// PARAMETERS: id
	// RETURN:     ArticleContenu object AS JSON 
	//-------------------------------------------------
	
	// Set return type as JSON
	header('Content-type: application/json');

	// Includes
	include 'fa_constants.php';
	include 'fa_entity.php';
	include 'fa_util.php';
	
	// Connect to database
	connect_db();

	// Create request
	$request = "SELECT idRedaction, corps 
	            FROM redaction";

	// Filter on id
	if(isset($_GET['id'])&&!empty($_GET['id']))
	{
		$request = $request . " WHERE  idRedaction = " . $_GET['id'];
	}

	// Order and limit
	$request = $request . " ORDER BY publication DESC ";
	$request = $request . " LIMIT 0, 1";

	// Run query
	$result = mysql_query($request);

	// Get article
	if ($row = mysql_fetch_array($result)) {
		// Convert to object
		$article = new ArticleContenu();  
		$article->id = $row['idRedaction'];
		$article->corps = utf8_encode(subst_images($row['corps']));
	}
	else {
		// No article
		$article = null;
	}
	echo json_encode($article);

	// Free result and close connection
	mysql_free_result($result);
	
	// Close database
	close_db();
?>
