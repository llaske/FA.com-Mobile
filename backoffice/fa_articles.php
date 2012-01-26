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
	$iPagerSpan = "0," . constant("max_list_size");
	$result = null ;
	
	// Filter on id
	$filterid = false;
	if(isset($_GET['id'])&&!empty($_GET['id']))
	{
		$result = x_RedactionSectionSelect(null,null,null,null,$_GET['id'],$iPagerSpan,false,false,NEWS_NO) ;
		$filterid = true;
	}
	
	// Filter on team
	if(isset($_GET['equipe'])&&!empty($_GET['equipe']))
	{
		$result = x_RedactionSectionSelect(null,FRANCHISE,$_GET['equipe'],null,null,$iPagerSpan,false,false,NEWS_NO) ;
	}

	// Filter on ligue
	if(isset($_GET['ligue'])&&!empty($_GET['ligue']))
	{
		$result = x_RedactionSectionSelect(null,LIGUE,$_GET['ligue'],null,null,$iPagerSpan,false,false,NEWS_NO) ; //toute les ligues $_GET['ligue']
	}

	// Filter on match
	if(isset($_GET['match'])&&!empty($_GET['match']))
	{
		$result = x_RedactionSectionSelect(null,MATCH,$_GET['match'],null,null,$iPagerSpan,false,false,NEWS_NO) ;
	}

	// Create array
	$articles = array();
	$i = 0;

	// Loop on each article
	while ($row = mysql_fetch_array($result)) {
		
		//Get Image path (image linked to article if exist. main image of the section if not)
		list($sPathImage,$sPathThumb,$sPathSlider,$sCommentaire) = getImagePathAndThumbPathFromRedaction($row) ;
		
		// Convert to object
		$article = new Article();  
		$article->id = $row['idRedaction'];
		$article->titre = decode_html(utf8_encode($row['titre1']));
		$article->soustitre = decode_html(utf8_encode($row['titre2']));
		$article->auteur = $row['initiales'];
		$article->image = $sPathThumb ; //$row[4];
		$article->imagemedium = $sPathSlider ;
		$article->resume = decode_html(utf8_encode(clean_resume($row['corps'])));
		$article->date = utf8_encode(getDateTimeFromDateTimeSQL($row['publication'],'d/m/Y à H:i'));
		$articles[$i] = $article;
		$i = $i + 1;
	}
	
	// Return JSON for all articles or only one
	if ($filterid) {
        $article = null;	
		if (count($articles) > 0)
			$article = $articles[0];
		echo encode_json($article);
	}
	else
		echo encode_json($articles);
?>
