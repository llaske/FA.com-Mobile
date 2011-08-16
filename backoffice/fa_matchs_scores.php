<?php 
	//-------------------------------------------------
	// ENTITY:     matchs/scores
	// METHOD:     GET
	// PARAMETERS: id
	// RETURN:     MatchScore object AS JSON 
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
	$request = "SELECT idMatch, qt1_d, qt2_d, qt3_d, qt4_d, qt1_e, qt2_e, qt3_e, qt4_e
			FROM matchs";

	// Filter on id
	if(isset($_GET['id'])&&!empty($_GET['id']))
	{
		$request = $request . " WHERE  idMatch = " . $_GET['id'];
	}

	// Order and limit
	$request = $request . " ORDER BY dateMatch DESC ";
	$request = $request . " LIMIT 0, 1";

	// Run query
	$result = mysql_query($request);

	// Get match
	if ($row = mysql_fetch_array($result)) {
		// Convert to object
		$match = new MatchScore();  
		$match->id = $row['idMatch'];
		$match->qt1_dom = $row['qt1_d'];
		$match->qt2_dom = $row['qt2_d'];
		$match->qt3_dom = $row['qt3_d'];
		$match->qt4_dom = $row['qt4_d'];
		$match->qt1_ext = $row['qt1_e'];
		$match->qt2_ext = $row['qt2_e'];
		$match->qt3_ext = $row['qt3_e'];
		$match->qt4_ext = $row['qt4_e'];		
	}
	else {
		// No match
		$match = null;
	}
	echo json_encode($match);

	// Free result and close connection
	mysql_free_result($result);
	
	// Close database
	close_db();
?>
