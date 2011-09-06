<?php 
	//-------------------------------------------------
	// ENTITY:     equipes
	// METHOD:     GET
	// PARAMETERS: id
	// RETURN:     array of Equipe object AS JSON (if id not mentionned)
	//             Equipe object AS JSON          (if id is mentionned)  
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
//	$request = "SELECT idUsfoot, franchise, franchise2, concat(acronyme, '-logo.jpg'), franchiseadd, siteofficiel
	//		FROM franchise";

	// Filter on id
	$filterid = false;	
	if(isset($_GET['id'])&&!empty($_GET['id']))
	{
		$iFranchise = $_GET['id'];
		$filterid = true;		
	}
	
	$result = getFranchiseInfo($iFranchise) ;

	// Create array
	$equipes = array();
	$i = 0;

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
	
	// Return JSON for all equipes or only one
	if ($filterid) {
        $equipe = null;	
		if (count($equipes) > 0)
			$equipe = $equipes[0];
		echo json_encode($equipe);
	}
	else
		echo json_encode($equipes);

	// Free result and close connection
	//mysql_free_result($result);
	
	// Close database
	//close_db();
?>
