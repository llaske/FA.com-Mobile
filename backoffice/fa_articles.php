<?php 
	//-------------------------------------------------------
	// ENTITY:     articles
	// METHOD:     GET
	// PARAMETERS: id, equipe, ligue, match, maxart, maxresume
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
	if(isset($_GET['maxart'])&&!empty($_GET['maxart']))	
		$iPagerSpan = "0," . $_GET['maxart'];
	else
		$iPagerSpan = "0," . constant("max_list_size");
	if(isset($_GET['maxresume'])&&!empty($_GET['maxresume']))	
		$cutpos = $_GET['maxresume'];
	else
		$cutpos = constant("max_resume_size");		
	$result = null ;
	$filtered = false;
	
	// Filter on id
	$filterid = false;
	if(isset($_GET['id'])&&!empty($_GET['id']))
	{
		$result = x_RedactionSectionSelect(null,null,null,null,$_GET['id'],$iPagerSpan,false,true,NEWS_NO) ;
		$filterid = true;
		$filtered = true;
	}
	
	// Filter on team
	if(isset($_GET['equipe'])&&!empty($_GET['equipe']))
	{
		$result = x_RedactionSectionSelect(null,FRANCHISE,$_GET['equipe'],null,null,$iPagerSpan,false,true,NEWS_NO) ;
		$filtered = true;		
	}

	// Filter on ligue
	if(isset($_GET['ligue'])&&!empty($_GET['ligue']))
	{
		$result = x_RedactionSectionSelect(null,LIGUE,$_GET['ligue'],null,null,$iPagerSpan,false,true,NEWS_NO) ; //toute les ligues $_GET['ligue']
		$filtered = true;		
	}

	// Filter on match
	if(isset($_GET['match'])&&!empty($_GET['match']))
	{
		$result = x_RedactionSectionSelect(null,MATCH,$_GET['match'],null,null,$iPagerSpan,false,true,NEWS_NO) ;
		$filtered = true;		
	}

	// No filtered
	if (!$filtered)
	{
		$result = x_RedactionSectionSelect(null,null,null,null,null,$iPagerSpan,false,true,NEWS_NO) ;	
	}
	
	// Create array
	$articles = array();
	$i = 0;

	// Loop on each article
	while ($row = mysql_fetch_array($result)) {
		
		//Get Image path (image linked to article if exist. main image of the section if not)
		if (!is_null($row['idMainImage']))
			list($sPathImage,$sPathThumb,$sPathSlider,$sCommentaire) = getImagePathAndThumbPathFromRedaction($row) ;
		else //we relaunch the search but only on the article id
		{
			$resRed = x_RedactionSectionSelect(null,null,null,null,$row['idRedaction'],null,true);
			$dataRed = mysql_fetch_array($resRed) ;
			list($sPathImage,$sPathThumb,$sPathSlider,$sCommentaire) = getImagePathAndThumbPathFromRedaction($dataRed) ;
		}
		
		// Convert to object
		$article = new Article();  
		$article->id = $row['idRedaction'];
		$article->titre = decode_html(utf8_encode($row['titre1']));
		$article->soustitre = decode_html(utf8_encode($row['titre2']));
		$article->auteur = $row['initiales'];
		$article->image = $sPathThumb ;
		$article->imagemedium = $sPathSlider ;
		$article->resume = decode_html(utf8_encode(clean_resume($row['corps'],$cutpos)));
		$article->date = utf8_encode(getDateTimeFromDateTimeSQL($row['publication'],'d/m/Y à H:i'));
		$article->urlsite = buildUrlToArticle("",$row['idRedaction']) ;		
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