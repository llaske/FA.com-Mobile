<?php 
	// Class to handle article
	class Article {
		public $id;
		public $titre;
		public $soustitre;
		public $auteur;
		public $image;
		public $resume;
		public $date;
	}
	
	// Class to handle article/contenu
	Class ArticleContenu {
		public $id;
		public $corps;
	}
	
	// Class to handle matchs
	Class Match {
		public $id;
		public $acrojournee;
		public $journee;
		public $date;
		public $heure;
		public $equipedom;
		public $equipeext;
		public $scoredom;
		public $scoreext;
	}
	
	// Class to handle match/score
	Class MatchScore {
		public $id;
		public $qt1_dom;
		public $qt2_dom;
		public $qt3_dom;
		public $qt4_dom;
		public $qt1_ext;
		public $qt2_ext;
		public $qt3_ext;
		public $qt4_ext;		
	}
	
	// Class to handle equipe
	Class Equipe {
		public $id;
		public $nom;
		public $ville;
		public $image;
		public $creation;
		public $web;
	}	
	
	// Class to handle classement row
	Class Classement {
		public $id;
		public $nom;
		public $conference;
		public $clssmnt_conf;
		public $division;
		public $clssmnt_div;
		public $playoffs;
		public $g;
		public $n;
		public $p;
		public $pf;
		public $pa;
		public $pct;
	}
?>