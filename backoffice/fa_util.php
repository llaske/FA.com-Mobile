<?php
	// Connect to MySQL
	function connect_db() {
		mysql_connect(constant("mysql_server"), constant("mysql_user"), constant("mysql_password"));
		mysql_select_db(constant("mysql_database"));	
	}
	
	// Close connexion to MySQL
	function close_db() {
		mysql_close();	
	}
	
	// get image url from the id
	function getimage_url($imgid) {
		// Request image
		$request = "SELECT concat(dossier,'/',nomImage) FROM image WHERE idImage=" . $imgid;
		
		// Run query
		$result = mysql_query($request);		
		
		// Fetch row
		while ($row = mysql_fetch_array($result)) {
			// Convert to object
			$url = $row[0];
		}

		// Free result
		mysql_free_result($result);		
		
		return constant("prefix_img_medium") . $url;
	}
	
	// Replace img tags and relative by a full URL
	function subst_images($content) {
		// Look for the "[img=" tag
		$pos = stripos($content, "[img=");
		while ($pos !== false) {
			// Get img id
			$endid = $pos + 5;
			while($content[$endid] != ' ') $endid = $endid + 1;
			$imgid = substr($content, $pos + 5, $endid - $pos - 5);
			
			// Get end of img tag
			while($content[$endid] != ']') $endid = $endid + 1;
			
			// Strip image
			$content = substr($content, 0, $pos) . "<br/><img src='" . getimage_url($imgid) . "'/><br/>" . substr($content, $endid+1);
			
			// Next image
			$pos = stripos($content, "[img=");
		}
		
		// Look for the "<a href=" tag
		$pos = stripos($content, "<a href=");
		while ($pos !== false) {
			// Split after expression
			$init = $pos;
			$pos = $pos + 8;
			$quote = $content[$pos];
			$right = substr($content, $pos+1);
			
			// If starting by "http://", just add target
			if (strncmp($right, "http://", 7) == 0) {
				$content = substr($content, 0, $init) . "<a target='_new' href=" . $quote . $right;			
			}
			
			// If not starting by "http://", add prefixe
			else {
				// Look for ending quote
				$pos = $pos + 1;
				$i = $pos;
				while ($content[$i] != $quote) $i = $i + 1;
				
				// Strip starting /
				if ($content[$pos] == '/') $pos = $pos + 1;
				
				// Add prefix
				$content = substr($content, 0, $init) . "<a target='_new' href=" . $quote . constant("prefix_global") . substr($content, $pos);
			}
						
			// Next tag
			$pos = stripos($content, "<a href=", $pos);
		}
		return $content;
	}
	
	// Build Resume of tags
	function clean_resume($content) {
		// Replace [img ... ] tag
		$content = subst_images($content);
		
		// Remove HTML tags
		$content = strip_tags($content);
		
		// Get 256 characters only but try to cut on a whitespace
		$cutpos = constant("max_resume_size");
		if ($cutpos < strlen($content))
		{
			while ($content[$cutpos-1] != ' ')
				$cutpos = $cutpos - 1;
		}
		$content = substr($content, 0, $cutpos);
		
		// Add ...
		$content = $content . "...";
		
		return $content;
	}

	// Decode html chars
	function decode_html($content) {
		$content = html_entity_decode($content, ENT_QUOTES, "UTF-8");
		$content = htmlspecialchars_decode($content, ENT_QUOTES);
		
		return $content;
	}
	
	//SQL Exec
	function sql_exec($sReqSql)
	{
		return mysql_query($sReqSql) ;
	}
	
	function sql_numrows($oRes)
	{
		return mysql_num_rows($oRes) ;
	}
	
	//Format SQL DateTime
	function getDateTimeFromDateTimeSQL($datetimesql,$format)
	{
		if ( preg_match( "/([0-9]{4})-([0-9]{1,2})-([0-9]{1,2}) ([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})/", $datetimesql, $regs ) )
		{
			//$regs : 0-ALL, 1-Y, 2-m, 3-d, 4-h, 5-m, 6-s
			//mktime (HEURE , MIN, SEC, MOIS, JOUR, ANNEE)
			$timestamp = mktime($regs[4],$regs[5],$regs[6],$regs[2],$regs[3],$regs[1]) ;
			$sDayExtract = $regs[3] ;
			$sMonthExtract = $regs[2] ;		
			$sYearExtract = $regs[1] ;
	
			$sHourExtract= $regs[4] ;
			$sMinExtract = $regs[5] ;
			//useless $sSecExtract = $regs[6] ;		
			
			$sDateTime = date($format, $timestamp) ;
			
			return $sDateTime ;
		}
		else
			echo ("Format de date invalide : $datetimesql") ;
	}
	
/**
 * Construit une requête SQL $sSqlRequest en renseignant dynamiquement la clause Where
 * Intérêt : la req sera construite en fonction de la validité des paramètres passés.
 * 		on appelle cette fonction et elle se charge du reste
 * Note : n'exécute pas la requête !
 *
 * @param string $sSqlRequest (la req avec %s à l'endroit où la clause Where sera placée)
 * @param array 2D $tWhereParameters (tableau 2D contenant la liste des paramètres à tester. Pour chaque param on a :
 * 		value=valeur à tester
 * 		test=type de test (NOT_NULL, NOT_EMPTY...)
 * 		request=la chaine à évaluer (toto=$s) ATTENTION : si une chaine en LIKE est à tester, il faut échapper %s : LIKE '%%%s%%'
 * 		parenthesis=bool expliquant s'il faut mettre une parenthèse
 * 		logical=string : "AND" ou "OR"
 * @return string la requête
 */
define("NOT_NULL",2) ;
define("NOT_EMPTY",6) ; // chaine != ''
define("NOT_ZERO",7) ; // !=0
define("JOINTURE",8) ; // pas de test, on a une jointure. Utilse dans la fonction sqlSearchEngine()

function sqlSearchEngine($sSqlRequest,$tWhereParameters)
{
	$tWhereClause = array();
	$sWhereClause = "" ;
	
	if (count($tWhereParameters)>0) //on a un paramètre à analyser
	{
		foreach($tWhereParameters as $param)
		{
			//foreach($tParameters as $param)
			//{
				if ($param["test"]==NOT_NULL && !is_null($param['value'])) $bIsWhereClauseValid=true ;	//le paramètre ne doit pas valoir NULL
				elseif ($param['test']==NOT_EMPTY && $param['value']!="") $bIsWhereClauseValid=true ;	//le paramètre ne doit pas valoir ""
				elseif ($param['test']==NOT_ZERO && $param['value']!=0) $bIsWhereClauseValid=true ; 	//le paramètre ne doit pas valoir 0 
				elseif ($param['test']==JOINTURE) $bIsWhereClauseValid=true ; 							//on a une jointure == rien à tester
				else $bIsWhereClauseValid=false ;

				if ($bIsWhereClauseValid)
				{
					if ($param['test']==JOINTURE)
						$sRequest = $param['request'];
					else
						$sRequest = sprintf($param['request'],$param['value']) ;
					
					array_push($tWhereClause,array("request"=>$sRequest,"isParenthesis"=>$param['parenthesis'],"logicalBeforeIfExist"=>$param['logical'])) ;
				}
			//}
		}
	}
	//Moteur de création de la clause Where
	if (count($tWhereClause)>0)
	{
		$sWhereClause .= " WHERE " ;
		$loop_one=true ;
		$bParenthesisOpened = false ;
		
		foreach($tWhereClause as $clause)
		{
			if ($clause['isParenthesis']==true)
			{	
				if ($bParenthesisOpened == false)
				{
					$sWhereClause .= "(" ;
					$bParenthesisOpened = true;	
				}
				else
				{
					//DO NOTHING
				}
				
			}
			elseif ($clause['isParenthesis']==false && $bParenthesisOpened == true)
			{
				$sWhereClause .= ")" ;
				$bParenthesisOpened = false;
			}
			else
			{
				//DO NOTHING
			}
			
			$sWhereClause .= !$loop_one ? " ".$clause['logicalBeforeIfExist']." " : "" ;
			
			$sWhereClause .= $clause['request'] ;
			
			if ($loop_one) $loop_one=false ;
		}	
		
		//on ferme une éventuelle parenthèse
		if ($bParenthesisOpened) $sWhereClause .= ")" ;			
	}
	
	$req = sprintf($sSqlRequest,$sWhereClause);

	return $req ;
	
}
	
	
?>