<?php 
	//-------------------------------------------------
	// ENTITY:     classements
	// METHOD:     GET
	// PARAMETERS: ligue
	// RETURN:     array of Classement object AS JSON
	//-------------------------------------------------
	
	// Set return type as JSON
	header('Content-type: application/json');

	// Includes
	include 'fa_constants.php';
	include 'fa_entity.php';
	include 'fa_util.php';
	
	// Init array
	$classements = array();

	// Must filter on ligue	
	if(!isset($_GET['ligue'])||empty($_GET['ligue'])) {	
		// Ligue not found
		echo json_encode($classements);
		return;
	}
		
	// Connect to database
	connect_db();
	
	// Create request to get competition
	$season = constant("force_season");
	$row = getCompetitionInfo($_GET['ligue'],$season) ;

	// Get last competition
	if (is_null($row)) {
		// Competition not found, stop
		echo json_encode($classements);

		return;
	}

	// Create base request
	$current_week = $row['current_week'];
	if ($current_week <= 0) 
		$current_week = 17;	
		
	$request = "SELECT ID_EQUIPE, franchise, conf, CLMNT_CONF, division, CLMNT, playoffs, G, N, P, PP, PC
		FROM standings, franchise, division, division_franchise, conference, conference_division
		WHERE competition=" . $row['idCompetition'] . "
			AND franchise.idUsfoot=ID_EQUIPE
			AND division_franchise.idFranchise=ID_EQUIPE
			AND division_franchise.idDiv=division.idDiv
			AND conference_division.idDiv=division.idDiv
			AND conference_division.idConference=conference.idConf
			AND clmnt_apres_journee = " . $current_week;
	
	// Build end of request
	$request = $request . " ORDER BY conf ASC, division ASC, CLMNT ASC";
	
	// Run query
	$result = mysql_query($request);

	// Loop on each classement
	$i = 0;
	while ($row = mysql_fetch_array($result)) {
		// Convert to object
		$classement = new Classement();  
		$classement->id = $row['ID_EQUIPE'];
		$classement->nom = $row['franchise'];
		$classement->conference = $row['conf'];
		$classement->clssmnt_conf = $row['CLMNT_CONF'];
		$classement->division = $row['division'];
		$classement->clssmnt_div = $row['CLMNT'];
		$classement->playoffs = $row['playoffs'];
		$classement->g = $row['G'];
		$classement->n = $row['N'];
		$classement->p = $row['P'];
		$classement->pf = $row['PP'];
		$classement->pa = $row['PC'];		
		$classement->pct = round($classement->g/($classement->g+$classement->p)*100,2) ;
		
		// Store in array
		$classements[$i] = $classement;
		$i = $i + 1;		
	}

	// Return JSON for all classements
	echo json_encode($classements);
?>
