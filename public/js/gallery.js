	
var cnt = 1;
var interval_header = null;	

// widths:
//   laptop: 1583
//   ipad: 983
//   android: 800
//alert($(document).width());

function onResize()
{	
	var dc = { width: 0, height: 0, ppl: 0, margin: 0, readonly: false };
	resize(dc);
	
	var content = document.getElementById("content");
	content.style.display = 'block';
	
	var loader = document.getElementById("load-loop");
	loader.style.display = 'none';
}

function resize(dc)
{	
	var log = true;
	if (log)
		console.log('resizing...');

	var browserWidth = window.innerWidth;
	var w = 0;
		
	// get margin value from photo box
	var box = $('.frontpage-box');
	
	var sMargin = $('.frontpage-box').css('margin-left'); 
	console.log('sMargin: ' + sMargin);
	var margin = (typeof sMargin !== 'undefined') ? Number(sMargin.substring(0, 1)) : 5;
	
	var pheight = 220;						// default photo height
	var pwidth = 320;						// calc's all based on the default width
	var ratio = pheight / pwidth;			// ratio needed for height calc
	var photoBaseWidth = margin + pwidth;	// default photo full width
	var photosPerLine = Math.floor(browserWidth / photoBaseWidth) + 1; // +1 so they'll always be smaller than base width
		
	// minimum is one
	if (photosPerLine <= 0)
		photosPerLine = 1;
		
	// get device info
	var deviceWidth = (browserWidth > screen.width) ? browserWidth : screen.width;
	var deviceHeight = screen.height;
	var isPortrait = (deviceWidth < deviceHeight);
	var isMicro = (window.innerWidth <= 380);
	
	// adjust font size, if needed
	var fontSet = false;
	if (isMicro) // micro screen
	{
		if (isPortrait) // portrait
		{
			photosPerLine = 2;
			
			// crank up the font size
			if (!dc.readonly)
				$('.frontpage-box-text a').css({fontSize:'300%'});				
		}
		else // landscape
		{
			photosPerLine = 2;	
			if (!dc.readonly)
				$('.frontpage-box-text a').css({fontSize:'150%'});
		}
		
		fontSet = true;
	}
	else
	{
		if (isPortrait) // portrait
			photosPerLine = Math.floor(browserWidth / photoBaseWidth);
		else // landscape
			photosPerLine = Math.floor(browserWidth / photoBaseWidth) + 1; // +1 so they'll always be smaller than base width
	}
	
	//	
	// compute new photo width and height
	//
	
	var widthTotal = 0;
	var rightMargin = 17; // estimated, don't know where the space comes from
	var windowWidth = window.innerWidth - rightMargin;

	// calculate width of each photo box
	w = (windowWidth / photosPerLine) - (margin);
	w = Math.floor(w);

	widthTotal = (((w + margin) * photosPerLine));
	console.log('widthTotal: ' + widthTotal + ', innerWidth: ' + windowWidth);

	// make a slight adjustment for more right margin when they get a little too big
	var loopLimit = 5;
	while (widthTotal >= windowWidth && loopLimit > 0)
	{
		if (log)
			console.log('too big - widthTotal: ' + widthTotal + ', innerWidth: ' + windowWidth);
			
		w--;
		widthTotal = (((w + margin) * photosPerLine));

		if (log)
			console.log('adjusted widthTotal: ' + widthTotal + ', innerWidth: ' + windowWidth);
		
		loopLimit--;
	}

	// set height according to width
	var h = Math.floor(w * ratio);	
	
	//
	// adjust font size
	//
	if (!dc.readonly && !fontSet)
	{
		// check if text needs to shrink
		var textResizeFactor = (pwidth * 0.8);
		if (w < textResizeFactor) // if new size of photo is less than 80%, then start shrinking the text so it doesn't wrap
		{
			var fs = Math.floor((w / textResizeFactor) * 100); // new font size percentage
			$('.frontpage-box-text a').css({fontSize:fs+'%'}); // apply it to the text links
		}
		else
		{
			// reset to full size
			$('.frontpage-box-text a').css({fontSize:'100%'});
		}
	}
	
	// set the new photo box size	
	if (!dc.readonly)
	{	
		$('.frontpage-box-link').css({width:w+'px', height:h+'px'});
	}
	
	if (log)
		console.log(
		  "window.innerWidth: " + window.innerWidth 
		+ ", windowWidth: " + windowWidth 
		+ ", screen.width: " + screen.width 
		+ ", screen.height: " + screen.height 
		+ ", deviceWidth: " + deviceWidth 
		+ ", ppl: " + photosPerLine 
		+ ", w=" + w 
		+ ", h=" + h 
		+ ", wt=" + widthTotal
		+ ", isMicro=" + isMicro
		+ ", isPortrait=" + isPortrait
		+ ", margin=" + margin
		);
	
	dc.width = w;
	dc.height = h;
	dc.ppl = photosPerLine;
	dc.margin = margin;
}

function flash(text)
{
	$('#debug').text(text);	
}

// sbw: need this??  onResize() is getting called when .js is loaded
//onResize();

