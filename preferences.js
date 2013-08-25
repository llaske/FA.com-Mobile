
//----------------- Global constants

prefixSite = "/mobile/";
prefixBackoffice = "http://m.footballamericain.com/backoffice/v2/";
prefixImages = "http://www.footballamericain.com/images/";


// Override data-url with site prefix
$("[data-role='page']").live('pagebeforecreate',function(event){
	$("[data-role='page']").each(function(){
		var dataurl = $(this).attr("data-url");
		if(typeof dataurl != "undefined" && dataurl.indexOf(prefixSite) != 0) {
			dataurl = prefixSite + $(this).attr("data-url");
			$(this).attr("data-url", dataurl);
		}
	})
});

//----------------- Preferences handling

Preferences = {};

// Filter preference value
Preferences.nfl = (LocalStorage.getValue('fa_nfl')==null?true:LocalStorage.getValue('fa_nfl'));
Preferences.ncaa = (LocalStorage.getValue('fa_ncaa')==null?true:LocalStorage.getValue('fa_ncaa'));
Preferences.elite = (LocalStorage.getValue('fa_elite')==null?true:LocalStorage.getValue('fa_elite'));

// Build the param url to filter using ligue
Preferences.getLigues = function() {
	var count = 0;
	var ligues = "";
	if (this.nfl || this.nfl == null) {
		ligues = ligues + "1";
		count++;
	}
	if (this.ncaa || this.ncaa == null) {
		if (count > 0) {
			ligues = ligues + ",";
			count = 0;
		}
		ligues = ligues + "2";
		count++;
	}
	if (this.elite || this.elite == null) {
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
	Stats.trace("/mobile/filtrer");
	$('#nfl').val(Preferences.nfl?'on':'off');
	$('#nfl').slider('refresh');
	$('#nfl').slider('disable');	
	$('#ncaa').val(Preferences.ncaa?'on':'off');
	$('#ncaa').slider('refresh');
	$('#elite').val(Preferences.elite?'on':'off');
	$('#elite').slider('refresh');	
});

// Hide page, get new preferences
$('ul[id="filtrer_nav"] a').live(clickAction, function(event, ui) {
	Preferences.nfl = ($('#nfl').val() == 'on');
	LocalStorage.setValue('fa_nfl', Preferences.nfl);
	Preferences.ncaa = ($('#ncaa').val() == 'on');
	LocalStorage.setValue('fa_ncaa', Preferences.ncaa);	
	Preferences.elite = ($('#elite').val() == 'on');
	LocalStorage.setValue('fa_elite', Preferences.elite);	
});


