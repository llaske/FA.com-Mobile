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

	// Filter on id
	$idRedaction = (isset($_GET['id'])&&!empty($_GET['id'])) ? $_GET['id'] : null ;
	
	$result = x_RedactionSectionSelect(null,null,null,null,$idRedaction);

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
?>
