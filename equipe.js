
//----------------- Cache Equipe handling
TeamCache = {};

TeamCache.content = [];

TeamCache.bulkCount = 0;

// Load a bulk of team
TeamCache.bulkloadTeams = function(ids, ok_callback, ko_callback) {
	TeamCache.bulkCount = ids.length;
	if (TeamCache.bulkCount == 0) {
		ok_callback();
		return;
	}
	
	// Build url
	var url = prefixBackoffice+"fa_equipes.php?id=" + ids;
	
	// Load data for this team
	$.getJSONWithMoz(url, function(data) {
		// Set team in cache
		$.each(data, function(n) {
			var team = data[n];
			TeamCache.content[team.id] = team;
		});
		ok_callback();
	})
	
	// Loading error
	.error(function() {
		ko_callback();
	});	
	
}

// Load a team
TeamCache.loadTeam = function(id, ok_callback, ko_callback) {
	// Already there
	if (this.content[id]!=null) {
		// Update count
		TeamCache.bulkCount = TeamCache.bulkCount - 1;
		if (TeamCache.bulkCount == 0)
			ok_callback();
		return;
	}
	
	// Build url
	var url = prefixBackoffice+"fa_equipes.php?id=" + id;
	
	// Load data for this team
	$.getJSONWithMoz(url, function(data) {
		// Store team data
		TeamCache.content[id] = data;

		// Update count
		TeamCache.bulkCount = TeamCache.bulkCount - 1;
		if (TeamCache.bulkCount == 0)
			ok_callback();
	})
	
	// Loading error
	.error(function() {
		ko_callback();
	});	
}

// Get team information
TeamCache.getTeam = function(id) {
	return this.content[id];
}

//----------------- Screen Equipe

// Init page equipe
$(document).on('pageshow', '#pg_equipe', function(event, ui) {  
	// Show page loading message
	$.mobile.showPageLoadingMsg();
	
	// Display information about team
	var param = History.getParam();
	var team = param;
	Stats.trace("/mobile/team/"+team.id);
	$('#team').data('team', team);	
	$('#team > #nom').html(team.ville + ' ' +team.nom);
	var html = '';
	if (team.creation != null) {
		html += 'Création '+team.creation;
		if (team.creation[team.creation.length-1] != '.')
			html += '.';
	}
	html += ' <a target="_new" href="'+team.web+'" rel="external">Voir le site web</a>';
	$('#team > #creation').html(html);	
	$('#team > #image').html("<img src='"+prefixImages+"images/team/100/"+team.image+"' width=100px>");	
	
	// Build url to get matchs	
	var url = prefixBackoffice+'fa_matchs.php?ligue=1&equipe='+team.id;

	// Launch ajax request to get matchs
	var ids = [];
	var count = 0;	
	$('#matchs_collapse').attr('data-collapsed', true);
	$.getJSONWithMoz(url, function(data) {
		// Store data
		$('#matchs_equipe').data('records', data);
		$.each(data, function() {
			if (this.equipedom != team.id) 
				ids[count++] = this.equipedom;
			if (this.equipeext != team.id) 
				ids[count++] = this.equipeext;
		});		
		
		// Load teams info then display matchs
		TeamCache.bulkloadTeams(ids,
			function() {
				// Remove all
				$('#matchs_equipe').children().remove('li');			
				
				// Append each match
				var team = $('#team').data('team');
				$.each(data, function(n) {
					// Compute score string
					var result;
					var theme;
					var place;
					var opponent;
					var score;
					var scorestring;
					var opponentscore;
					if ( this.equipedom == team.id ) {
						place = 'vs ';
						score = this.scoredom;
						opponent = this.equipeext;
						opponentscore = this.scoreext;
					} else {
						place = '@';
						score = this.scoreext;
						opponent = this.equipedom;
						opponentscore = this.scoredom;
					}
					if (parseInt(score) > parseInt(opponentscore)) {
						result = ' V ';
						theme = 'b';
					} else if (parseInt(score) < parseInt(opponentscore)) {
						result = ' D ';
						theme = 'c';
					} else {
						result = ' N ';
						theme = 'd';
					}
					if (this.scoredom != null && this.scoreext != null) {
						scorestring = this.scoredom + '-' + this.scoreext;
					} else {
						result = ' ';
						var dateParts = this.date.split("-");
						var date = new Date(dateParts[0], parseFloat(dateParts[1])-1, parseFloat(dateParts[2]));
						scorestring = date.getDate()+'/'+(date.getMonth()+1)+'/'+date.getFullYear();					
					}
					
					// Append line
					var html = '';
					var teamopp = TeamCache.getTeam(opponent);
					html += '<li data-theme="'+theme+'"><a href="#" data-index="'+n+'">';				
					html += '<p>'+this.acrojournee + ': ' + result + place + teamopp.nom + ', <strong>'+ scorestring +'</strong></p>';
					html += '</a></li>';
					$('#matchs_equipe').append(html);
				});	

				// Update listview
				$('#matchs_equipe').listview("refresh");			
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
	
	// Read matching articles
	updateArticles('#articles_equipe', {team: team.id});
});

// Set article click handler from team
$(document).on(clickAction, 'ul[id="articles_equipe"] a', function(event, ui) {
	// Get article selected
	var n = $(this).attr("data-index"); 
	var record = $('#articles_equipe').data("records")[n];
	
	// Push in history and change page
	var team = $('#team').data('team');
	History.push('equipe.html', record);	
    History.changePage("article_detail.html");
});

// Set match click handler
$(document).on(clickAction, 'ul[id="matchs_equipe"] a', function(event, ui) {
	// Get match and team selected
	var n = $(this).attr("data-index"); 
	var record = $('#matchs_equipe').data("records")[n];
	
	// Push in history and change page
	var team = $('#team').data('team');
	History.push('equipe.html', {match: record, teamdom: TeamCache.getTeam(record.equipedom), teamext: TeamCache.getTeam(record.equipeext)});
    History.changePage("match_detail.html");
});