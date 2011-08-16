//----------------- Screen Matchs

// Load matchs
function loadMatchs() {
	// Build url
	var url = prefixBackoffice+'fa_matchs.php?ligue=1';
	
	// Launch ajax request to load matchs
	$.getJSON(url, function(data) {
		// Store data
		var ids = [];
		var count = 0;
		$('#matchs').data('records', data);
		$.each(data, function() {
			ids[count++] = this.equipedom;
			ids[count++] = this.equipeext;
		});	
		
		// Load teams
		TeamCache.bulkloadTeams(ids,
			function() {
				// Update listview when done
				updateMatchs();
			},
			
			function() {
				// Hide page loading message
				$.mobile.hidePageLoadingMsg();

				// Show error dialog
				$.mobile.changePage("#error", "pop", false, false); 			
			}
		);
	})
	
	// Loading error
	.error(function() {
		// Hide page loading message
		$.mobile.hidePageLoadingMsg();

		// Show error dialog
		$.mobile.changePage("#error", "pop", false, false); 
	});
}

// Update matchs
function updateMatchs() {
	// Remove all
	$("#matchs").children().remove('li');
	
	// Append match
	var records = $('#matchs').data('records');
	$.each(records, function(n) {
		var html = '';
		if (n == 0 || records[n-1].journee != this.journee)
			html += '<li data-role="list-divider">'+this.journee+'</li>';
		html += '<li ><a href="#" data-index="'+n+'">';
		html += '<img  src="'+prefixImages+'images/team/100/'+TeamCache.getTeam(this.equipedom).image+'" class="ui-li-icon" style="top: 0px; width: 30px;max-height: 40px"/>';
		html += '<p style="position: absolute; left: 45px; top: 10;max-height: 40px">'+TeamCache.getTeam(this.equipedom).nom+'</p>';
		html += '<p style="position: absolute; left: 120px; top: 10;max-height: 40px"><strong>'+this.scoredom+' - '+this.scoreext+'</strong></p>';		
		html += '<p style="position: absolute; left: 200px; top: 10;max-height: 40px">'+TeamCache.getTeam(this.equipeext).nom+'</p>';		
		html += '<img  src="'+prefixImages+'images/team/100/'+TeamCache.getTeam(this.equipeext).image+'" style="position: absolute; left: 260px; top: 0; width: 30px;max-height: 40px"/>';		
		html += "</a></li>";		
		$("#matchs").append(html);		
	});
	
	// Update listview
	$("#matchs").listview("refresh");

	// Hide page loading message
	$.mobile.hidePageLoadingMsg();
}

// Init page matchs
$('#pg_matchs').live('pageshow', function(event, ui) {
	// Show loading message
	$.mobile.showPageLoadingMsg();	
	
	// Load matchs
	loadMatchs();	
});

// Set article click handler
$('ul[id="matchs"] a').live('vclick', function(event, ui) {
	// Get match and team selected
	var n = $(this).attr("data-index"); 
	var record = $('#matchs').data("records")[n];
	
	// Push in history and change page
	History.push('Matchs', {match: record, teamdom: TeamCache.getTeam(record.equipedom), teamext: TeamCache.getTeam(record.equipeext)});
    $.mobile.changePage("match_detail.html");
});



//----------------- Screen Detail Match

// Init page detail article
$('#pg_match_detail').live('pageshow', function(event, ui) {  
	// Show page loading message
	$.mobile.showPageLoadingMsg();
	
	// Build url to get score
	var pop = History.pop();
	var match = pop.param.match;
	var teamdom = pop.param.teamdom;
	var teamext = pop.param.teamext;
	$('#match').data('param', pop.param);	
	var url = prefixBackoffice+'fa_matchs_scores.php?id='+match.id;
	
	// Launch ajax request
	$.getJSON(url, function(data) {
		// Display score
		var html = '';
		html += '<img  src="'+prefixImages+'images/team/100/'+teamdom.image+'" style="position: absolute; left: 70px; top: 55px; width: 30px"/>';
		html += '<a href="#" id="teamdom" style="position: absolute; left: 50px; top: 80px">'+teamdom.nom+'</a>';
		html += '<p style="position: absolute; left: 130px; top: 60px"><strong>'+match.scoredom+' - '+match.scoreext+'</strong></p>';		
		html += '<a href="#" id="teamext" style="position: absolute; left: 210px; top: 80px">'+teamext.nom+'</a>';		
		html += '<img  src="'+prefixImages+'images/team/100/'+teamext.image+'" style="position: absolute; left: 230px; top: 55px; width: 30px"/>';	
		html += '<p style="position: absolute; left: 5px; top: 140px">'+teamdom.nom+'</p>';	
		html += '<p style="position: absolute; left: 5px; top: 180px">'+teamext.nom+'</p>';			
		$('#score').html(html);
		
		// Display detail score
		$('#qt1_dom').html(data.qt1_dom);
		$('#qt2_dom').html(data.qt2_dom);
		$('#qt3_dom').html(data.qt3_dom);		
		$('#qt4_dom').html(data.qt4_dom);
		$('#qt1_ext').html(data.qt1_ext);
		$('#qt2_ext').html(data.qt2_ext);
		$('#qt3_ext').html(data.qt3_ext);		
		$('#qt4_ext').html(data.qt4_ext);
	})
	
	// Loading error
	.error(function() {
	  // Hide page loading message
	  $.mobile.hidePageLoadingMsg();

	  // Show error dialog
	  $.mobile.changePage("#error", "pop", false, false); 
	});	
	
	// Read matching articles
	updateArticles('#articles_match', {match: match.id});
});

// Set article click handler from detail match
$('ul[id="articles_match"] a').live('vclick', function(event, ui) {
	// Get article selected
	var n = $(this).attr("data-index"); 
	var record = $('#articles_match').data("records")[n];
	
	// Push in history and change page
	History.push('Match', $('#match').data('param'));	// HACK: Push match context to go back here
	History.push('Articles', record);	
    $.mobile.changePage("article_detail.html");
});

// Set team click handler
$('#pg_match_detail > #match > #score > #teamdom').live('vclick', function(event, ui) {
	// Get team clicked
	var param = $('#match').data('param');
	var teamdom = param.teamdom;
	
	// Push in history and change page
	History.push('Match', $('#match').data('param'));	// HACK: Push match context to go back here
	History.push('Team', teamdom);
    $.mobile.changePage("equipe.html");
});
$('#pg_match_detail > #match > #score > #teamext').live('vclick', function(event, ui) {
	// Get team clicked
	var param = $('#match').data('param');
	var teamext = param.teamext;
	
	// Push in history and change page
	History.push('Match', $('#match').data('param'));	// HACK: Push match context to go back here
	History.push('Team', teamext);
    $.mobile.changePage("equipe.html");
});