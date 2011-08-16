//----------------- Screen Classements

// Load classements
function loadClassements() {
	// Build url
	var url = prefixBackoffice+'fa_classements.php?ligue=1';
	
	// Launch ajax request to load classements
	$.getJSON(url, function(data) {
		// Store data
		$('#classements').data('records', data);
		
		// Append each item in the right conference
		var countafc = 0;
		var countnfc = 0;
		data.afc = [];
		data.nfc = [];
		$.each(data, function(n) {
			if (this.conference  == 'AFC')
				data.afc[countafc++] = this;
			else
				data.nfc[countnfc++] = this;		
		});	

		// Display the current conference
		displayClassements();
		
		// Hide page loading message
		$.mobile.hidePageLoadingMsg();
	})
	
	// Loading error
	.error(function() {
		// Hide page loading message
		$.mobile.hidePageLoadingMsg();

		// Show error dialog
		$.mobile.changePage("#error", "pop", false, false); 
	});
}

// Display conference items
function displayClassements() {
	// Get records
	var records = $('#classements').data('records');
	var afc = $('#conference_afc').attr('checked');
	var currentconf;
	var bconf;
	var econf;
	if (afc != null) {
		currentconf = records.afc;
		bconf = "<div class='ui-bar-e' style='margin-top: 10px'><strong>";
		econf = "</strong></div>";
	}
	else {
		currentconf = records.nfc;
		bconf = "<div class='ui-bar-a' style='margin-top: 10px'><strong>";
		econf = "</strong></div>";
	}

	// Append each line
	$("#classements").children().remove('div');
	var ba = "<div class='ui-bar-d'>";
	var ea = "</div>";	
	$.each(currentconf, function(n) {
		// Append title if need
		var html = '';
		if (n == 0 || this.division != currentconf[n-1].division) {
			html += '<div class="ui-block-a" style="width:140px">'+bconf+this.division+econf+'</div>';
			html += '<div class="ui-block-b" style="width:40px">'+bconf+'G'+econf+'</div>';
			html += '<div class="ui-block-c" style="width:40px">'+bconf+'N'+econf+'</div>';
			html += '<div class="ui-block-d" style="width:40px">'+bconf+'P'+econf+'</div>';
			html += '<div class="ui-block-e" style="width:60px">'+bconf+'Pct'+econf+'</div>';
		}
		
		// Append line
		var b = "";
		var e = "";
		if (n % 2 == 0) {
			b = ba;
			e = ea;
		}
		html += '<div class="ui-block-a" style="width:140px">'+b+this.nom+e+'</div>';
		html += '<div class="ui-block-b" style="width:40px">'+b+this.g+e+'</div>';
		html += '<div class="ui-block-c" style="width:40px">'+b+this.n+e+'</div>';
		html += '<div class="ui-block-d" style="width:40px">'+b+this.p+e+'</div>';
		html += '<div class="ui-block-e" style="width:60px">'+b+this.pct+e+'</div>';
		$("#classements").append(html);	
	});
}

// Init page classements
$('#pg_classements').live('pageshow', function(event, ui) {
	// Show loading message
	$.mobile.showPageLoadingMsg();	
	
	// Load classements
	loadClassements();	
});

// Set conference click handler to change conference view
$('input[name="conference"]').live('change', function(event, ui) {
	// Display the current conference
	displayClassements();
});