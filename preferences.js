
//----------------- Global constants

prefixSite = "/mobile/";
prefixBackoffice = "backoffice/";
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
Preferences.nfl = ($.cookie('fa_nfl')==null?true:$.cookie('fa_nfl')=="true");
Preferences.ncaa = ($.cookie('fa_ncaa')==null?true:$.cookie('fa_ncaa')=="true");
Preferences.elite = ($.cookie('fa_elite')==null?true:$.cookie('fa_elite')=="true");

// Build the param url to filter using ligue
Preferences.getLigues = function() {
	var count = 0;
	var ligues = "";
	if (this.nfl || this.nfl == null) {
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
$('ul[id="filtrer_nav"] a').live('vclick', function(event, ui) {
	Preferences.nfl = ($('#nfl').val() == 'on');
	$.cookie('fa_nfl', Preferences.nfl, { path: '/' });
	Preferences.ncaa = ($('#ncaa').val() == 'on');
	$.cookie('fa_ncaa', Preferences.ncaa, { path: '/' });	
	Preferences.elite = ($('#elite').val() == 'on');
	$.cookie('fa_elite', Preferences.elite, { path: '/' });	
});


