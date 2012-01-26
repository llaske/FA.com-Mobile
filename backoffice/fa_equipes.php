<?php 
	//-------------------------------------------------
	// ENTITY:     equipes
	// METHOD:     GET
	// PARAMETERS: id
	// RETURN:     array of Equipe object AS JSON (if id not mentionned)
	//             Equipe object AS JSON          (if multiple id is mentionned)  
	//-------------------------------------------------
	
	// Set return type as JSON
	header('Content-type: application/json');

	// Includes
	include 'fa_constants.php';
	include 'fa_entity.php';
	include 'fa_util.php';
	
	// Connect to database
	connect_db();

	// Look for one id or for a set of ids
	$ids = array();
	if(isset($_GET['id'])&&!empty($_GET['id']))
	{
		$ids = explode(",",$_GET['id']);
		$ids = array_unique($ids) ;
	}

	// Create array for results
	$equipes = array();

	// Loop on each id
	$i = 0;
	foreach ($ids as &$curid) {
		// Get team id info
		$result = getFranchiseInfo($curid) ;

		// Loop on each article
		while ($row = mysql_fetch_array($result)) {
			// Convert to object
			$equipe = new Equipe();  
			$equipe->id = $row['idUsfoot'];
			$equipe->nom = $row['franchise'];	
			$equipe->ville = $row['franchise2'];
			$equipe->image = $row['acronyme'] . "-logo.jpg";		
			$equipe->creation = $row['franchiseadd'];
			$equipe->web = $row['siteofficiel'];
			$equipes[$i] = $equipe;
			$i = $i + 1;
		}
	}
	
	// Return JSON for a set of equipe or only one
	if (count($equipes) == 1)
		echo encode_json($equipes[0]);
	else
		echo encode_json($equipes);
?>
