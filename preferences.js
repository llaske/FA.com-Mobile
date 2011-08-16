
//----------------- Global constants

prefixBackoffice = "backoffice/";
prefixImages = "http://www.footballamericain.com/images/";


//----------------- Preferences handling

Preferences = {};

// Filter preference value
Preferences.nfl = true;
Preferences.ncaa = true;
Preferences.elite = true;

// Build the param url to filter using ligue
Preferences.getLigues = function() {
	var count = 0;
	var ligues = "";
	if (this.nfl) {
		ligues = ligues + "1";
		count++;
	}
	if (this.ncaa) {
		if (count > 0) {
			ligues = ligues + ",";
			count = 0;
		}
		ligues = ligues + "2";
		count++;
	}
	if (this.elite) {
		if (count > 0) {
			ligues = ligues + ",";
			count = 0;
		}
		ligues = ligues + "3";
		count++;
	}	
	return ligues;
}

//----------------- Screen Filtrer

// Init page 
$('#pg_filtrer').live('pageshow', function(event, ui) {  
	$('#nfl').val(Preferences.nfl?'on':'off');
	$('#nfl').slider('refresh');
	$('#nfl').slider('disable');	
	$('#ncaa').val(Preferences.ncaa?'on':'off');
	$('#ncaa').slider('refresh');
	$('#elite').val(Preferences.elite?'on':'off');
	$('#elite').slider('refresh');	
});

// Hide page, get new preferences
$('#pg_filtrer').live('pagehide', function(event, ui) {
	Preferences.nfl = ($('#nfl').val() == 'on');
	Preferences.ncaa = ($('#ncaa').val() == 'on');
	Preferences.elite = ($('#elite').val() == 'on');
});


