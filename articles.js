//----------------- Screen Articles

// Update articles list using ligue, match or team
function updateArticles(target, param) {
	// On articles list, don't reload if already filled
	if (target == '#articles_all' && $(target).children().length > 0) {
		// Hide page loading message
		$.mobile.hidePageLoadingMsg();
		return;
	}

	// Remove all
	$(target).children().remove('li');

	// Build url
	var url = prefixBackoffice+'fa_articles.php';
	if (param != null) {
		if (param.ligue != null)
			url += '?ligue=' + param.ligue;
		else if (param.match != null)
			url += '?match=' + param.match;
		else if (param.team != null)
			url += '?equipe=' + param.team;
	}
	
	// Launch ajax request
	$.getJSONWithMoz(url, function(data) {
		// Store data
		$(target).data('records', data);

		// Append each item
		$.each(data, function(n) {
			var html = '';
			html += '<li><a href="#" data-index="'+n+'">';
			html += '<img src="'+this.image+'" style="position: absolute; left: 0px; top: 25px;"/>';
			html += '<h3 style="position: absolute; left: 0px; top: -10px;">'+this.titre+'</h3>';
			html += '<p style="position: absolute; left: 0px; top: 10px; margin-top: 12px; margin-left: 95px; width: 230px; white-space: normal; ">'+this.resume+'</p>';
			html += '</a></li>';
			$(target).append(html);
		});	

		// Update listview
		$(target).listview("refresh");

		// Hide page loading message
		$.mobile.hidePageLoadingMsg();		
	})
	
	// Loading error
	.error(function(req) {
		// Hide page loading message
		$.mobile.hidePageLoadingMsg();

		// Show error dialog
		$.mobile.changePage("#error", "pop", false, false); 
	});
}

// Init page articles
$(document).on('pageinit', '#pg_articles', function(event, ui) {
	// Show loading message
	Stats.trace("/mobile/articles");
	$.mobile.showPageLoadingMsg();	

	// Clear history
	History.clear();
	
	// Update article using ligue
	updateArticles('#articles_all', {ligue: Preferences.getLigues()});	
});

// Set article click handler from articles list
$(document).on(clickAction, 'ul[id="articles_all"] a', function(event, ui) {
	// Get article selected
	var n = $(this).attr("data-index"); 
	var record = $('#articles_all').data("records")[n];
	
	// Push in history and change page
	History.push('index.html', record);
    History.changePage("article_detail.html");
	return false;
});



//----------------- Screen Detail Article

// Init page detail article
$(document).on('pageshow', '#pg_detail_article', function(event, ui) {  
	// Show page loading message
	$.mobile.showPageLoadingMsg();
	
	// Build url to get content
	var param = History.getParam();
	var url = prefixBackoffice+'fa_articles_contenu.php?id='+param.id;
	Stats.trace("/mobile/article/"+param.id);
	var urlsite = param.urlsite;

	// Launch ajax request
	$.getJSONWithMoz(url, function(data) {
		// Append item
		var html = '';
		html += '<h2><font style: "color: red;">'+param.titre+'</font></h2>';
		html += '<h3>'+param.soustitre+'</h3>';
		html += '<h6>le '+param.date+' par '+param.auteur+'</h6>';
		html += '<img src="'+param.imagemedium+'"/>';
		html += '<div style="text-align: justify;">'+data.corps+'</div>';
		$('#article').html(html);
	  
		// Hide page loading message
		$.mobile.hidePageLoadingMsg();
	
		// Build url to get comments
		url = prefixBackoffice+'fa_articles_comments.php?id='+param.id;
		
		// Launch ajax request
		var articlehtml = html;
		$.getJSONWithMoz(url, function(data) {
			// Append item
			var html = articlehtml;
			var text = '';
			if (data.length == 0)
				text = "Soyez le premier à commenter cet article !";
			else
				text = data.length + " commentaire" + (data.length > 1 ? "s":"") + " sur l'article";
			html += '<br/><a target="_new" href="http://www.footballamericain.com'+urlsite+'#trackback" rel="external">'+text+'</a><br/><br/>';
			$('#article').html(html);
		})
		
		// Loading error
		.error(function() {
		});		
	})
	
	// Loading error
	.error(function() {
	  // Hide page loading message
	  $.mobile.hidePageLoadingMsg();

	  // Show error dialog
	  $.mobile.changePage("#error", "pop", false, false); 
	});		
});

// HACK: Force Articles reference action (need for Firefox OS)
$(document).on(clickAction, '#btnArticles', function(event, ui) {
	History.changePage("index.html");
	return false;
});