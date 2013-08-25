<?php 
	//-------------------------------------------------
	// ENTITY:     matchs
	// METHOD:     GET
	// PARAMETERS: ligue, equipe, season, maxcompdays
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
		echo encode_json($matchs);
		return;
	}
		
	// Connect to database
	connect_db();
	
	// Create request to get competition
	if(isset($_GET['season'])&&!empty($_GET['season']))
		$season = $_GET['season'];
	else
		$season = constant("force_season");
	$row = getCompetitionInfo($_GET['ligue'],$season) ;

	// Get last competition
	if (is_null($row)) {
		// Competition not found, stop
		echo encode_json($matchs);

		return;
	}
	
	// Compute current week
	if(isset($_GET['maxcompdays'])&&!empty($_GET['maxcompdays']))
		$maxcompdays = $_GET['maxcompdays'];
	else
		$maxcompdays = constant("max_competition_days");
	$current_week = $row['current_week'];
	if ($current_week <= 0) {
		$start_week = 22 - $maxcompdays;
		$end_week = 22;
	} else {
		$start_week = max($current_week - $maxcompdays - 2, 1);
		$end_week = min($current_week + 1, 22);
	}

	
	$result = null ;
	
	// Filter on id
	$filterid = false;
	$teamfilter = false;
	
	if(isset($_GET['id'])&&!empty($_GET['id']))
	{
		$result = getMatchsInfo(null,null,null,$_GET['id'],"<2","DESC") ;
		$filterid = true;
	}	
	elseif(isset($_GET['equipe'])&&!empty($_GET['equipe'])) // Filter on team
	{
		$teamfilter = true;
		$result = getMatchsInfo($row['idCompetition'],null,$_GET['equipe'],null,"<2","DESC") ;
	}	
	else //No Filter, automatic filter on competition
	{
		$result = getMatchsInfo($row['idCompetition'],null,null,null,"<2","DESC") ;
	}
	
	// Loop on each match
	$i = 0;
	$numdays = 0;
	while ($row = mysql_fetch_array($result)) {
		// Convert to object
		$match = new Match();  
		$match->id = $row['idMatch'];
		$match->acrojournee = $row['acroJournee'];
		$match->journee = $row['libJournee'];
		$match->date = $row['dateMatch'];
		$match->heure = $row['heureMatch'];
		$match->equipedom = $row['idUsFootDom'];
		$match->equipeext = $row['idUsFootExt'];
		$match->scoredom = $row['score_d'];
		$match->scoreext = $row['score_e'];
		
		// If not filter on a team, stop to N last days of the competition
		if (!$teamfilter) {
			$journee = $row['journee'];
			if ($journee < $start_week || $journee > $end_week)
				continue;
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
		echo encode_json($match);
	}
	else
		echo encode_json($matchs);
?>
