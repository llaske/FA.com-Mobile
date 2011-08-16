<?php 
	//-------------------------------------------------------
	// ENTITY:     articles
	// METHOD:     GET
	// PARAMETERS: id, equipe, ligue, match
	// RETURN:     array of Articles object AS JSON (if id not mentionned)
	//             Articles object AS JSON          (if id is mentionned)  
	//-------------------------------------------------------
	
	// Set return type as JSON
	header('Content-type: application/json');

	// Includes
	include 'fa_constants.php';
	include 'fa_entity.php';
	include 'fa_util.php';
	
	// Connect to database
	connect_db();
	
	// Create request
	$request = "SELECT idRedaction, titre1, titre2, initiales, concat(dossier,'/',nomImage), corps,
				DATE_FORMAT(publication, '%d/%m/%Y à %k:%i:%s')
	            FROM auteur AS a, redaction AS r LEFT JOIN image AS i ON r.idMainImage = i.idImage  WHERE r.auteur = a.idAuteur";

	// Filter on id
	$filterid = false;
	if(isset($_GET['id'])&&!empty($_GET['id']))
	{
		$request = $request . " AND idRedaction = " . $_GET['id'];
		$filterid = true;
	}
	
	// Filter on team
	if(isset($_GET['equipe'])&&!empty($_GET['equipe']))
	{
		$request = $request . " AND idRedaction IN (
			SELECT idRedaction
			FROM x_redaction_section, section
			WHERE 
				x_redaction_section.idSection = section.idS
				AND section.idSG = 1
				AND section.idLink = " . $_GET['equipe'] . ") ";
	}

	// Filter on ligue
	if(isset($_GET['ligue'])&&!empty($_GET['ligue']))
	{
		$request = $request . " AND idRedaction IN (
			SELECT idRedaction
			FROM x_redaction_section, section
			WHERE 
				x_redaction_section.idSection = section.idS
				AND section.idSG = 2
				AND section.idLink IN (" . $_GET['ligue'] . ")) ";
	}

	// Filter on match
	if(isset($_GET['match'])&&!empty($_GET['match']))
	{
		$request = $request . " AND idRedaction IN (
			SELECT idRedaction
			FROM x_redaction_section, section
			WHERE 
				x_redaction_section.idSection = section.idS
				AND section.idSG = 8
				AND section.idLink = " . $_GET['match'] . ") ";
	}

	// Order and limit
	$request = $request . " ORDER BY publication DESC ";
	$request = $request . " LIMIT 0, " .  constant("max_list_size");

	// Run query
	$result = mysql_query($request);

	// Create array
	$articles = array();
	$i = 0;

	// Loop on each article
	while ($row = mysql_fetch_array($result)) {
		// Get section image if image is null
		if ($row[4] == null) {
			// Build request
			$imagereq = "SELECT concat(dossier,'/',nomImage) FROM x_redaction_section, section, image WHERE idS=mainSection AND mainImageIdS=idImage AND idRedaction=" . $row['idRedaction'];
			
			// Run query
			$imageres = mysql_query($imagereq);
			if ($imgrow = mysql_fetch_array($imageres)) {
				$row[4] = $imgrow[0];	
			}
			mysql_free_result($imageres);
		}
		
		// Convert to object
		$article = new Article();  
		$article->id = $row['idRedaction'];
		$article->titre = decode_html(utf8_encode($row['titre1']));
		$article->soustitre = decode_html(utf8_encode($row['titre2']));
		$article->auteur = $row['initiales'];
		$article->image = $row[4]; 
		$article->resume = decode_html(utf8_encode(clean_resume($row[5])));
		$article->date = utf8_encode($row[6]);
		$articles[$i] = $article;
		$i = $i + 1;
	}
	
	// Return JSON for all articles or only one
	if ($filterid) {
        $article = null;	
		if (count($articles) > 0)
			$article = $articles[0];
		echo json_encode($article);
	}
	else
		echo json_encode($articles);

	// Free result and close connection
	mysql_free_result($result);
	
	// Close database
	close_db();
?>
