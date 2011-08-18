<?php 
	//-------------------------------------------------
	// ENTITY:     matchs
	// METHOD:     GET
	// PARAMETERS: ligue, equipe
	// RETURN:     array of Match object AS JSON (if id not mentionned)
	//             Match object AS JSON          (if id mentionned)
	//-------------------------------------------------
	
	// Set return type as JSON
	header('Content-type: application/json');

	// Includes
	include 'fa_constants.php';
	include 'fa_entity.php';
	include 'fa_util.php';
	
	// Init array
	$matchs = array();

	// Must filter on ligue	
	if(!isset($_GET['ligue'])||empty($_GET['ligue'])) {	
		// Ligue not found
		echo json_encode($matchs);
		return;
	}
		
	// Connect to database
	connect_db();
	
	// Create request to get competition
	$request = "SELECT idCompetition FROM competition WHERE "; 
	$season = constant("force_season");
	if (!empty($season))
		$request = $request . "saison='" . $season ."' AND ";
	$request = $request . "ligue=" . $_GET['ligue'] . "	ORDER BY saison DESC LIMIT 0, 1";

	// Run query
	$result = mysql_query($request);

	// Get last competition
	if (!($row = mysql_fetch_array($result))) {
		// Competition not found, stop
		echo json_encode($matchs);
		
		// Free result and close connection
		mysql_free_result($result);
		
		// Close database
		close_db();		
		return;
	}
	
	// Create base request
	$request = "SELECT idMatch, dateMatch, acroJournee, libJournee, idUsFootDom, idUsFootExt, score_d, score_e
		FROM matchs LEFT JOIN journee AS j
		ON matchs.journee = j.idJournee AND matchs.idCompetition = j.idCompetition
		WHERE dateMatch < CURRENT_DATE AND j.typeMatch<2 AND matchs.idCompetition=" . $row['idCompetition'];

	// Filter on id
	$filterid = false;
	if(isset($_GET['id'])&&!empty($_GET['id']))
	{
		$request = $request . " AND idMatch = " . $_GET['id'];
		$filterid = true;
	}	
	
	// Filter on team
	$teamfilter = false;
	if(isset($_GET['equipe'])&&!empty($_GET['equipe']))
	{
		$teamfilter = true;
		$request = $request . "	AND (idUsFootDom=" . $_GET['equipe'] . "
			OR idUSFootExt=" . $_GET['equipe'] . ")";
	}	
	
	// Build end of request
	$request = $request . " ORDER BY dateMatch DESC";
	
	// Run query
	$result = mysql_query($request);

	// Loop on each match
	$i = 0;
	$journee = "";
	$numdays = 0;
	while ($row = mysql_fetch_array($result)) {
		// Convert to object
		$match = new Match();  
		$match->id = $row['idMatch'];
		$match->acrojournee = $row['acroJournee'];
		$match->journee = $row['libJournee'];
		$match->date = $row['dateMatch'];
		$match->equipedom = $row['idUsFootDom'];
		$match->equipeext = $row['idUsFootExt'];
		$match->scoredom = $row['score_d'];
		$match->scoreext = $row['score_e'];
		
		// If not filter on a team, stop to N last days of the competition
		if (!$teamfilter) {
			if ($numdays == 0) {
				$journee = $match->journee;
				$numdays = 1;
			} else if ($journee != $match->journee) {
				$journee = $match->journee;
				$numdays = $numdays + 1;
				if ($numdays > constant("max_competition_days"))
					break;
			}
		}
		
		// Store in array
		$matchs[$i] = $match;
		$i = $i + 1;		
	}

	// Return JSON for all matchs or only one
	if ($filterid) {
        $match = null;	
		if (count($matchs) > 0)
			$match = $matchs[0];
		echo json_encode($match);
	}
	else
		echo json_encode($matchs);

	// Free result and close connection
	mysql_free_result($result);
	
	// Close database
	close_db();
?>
