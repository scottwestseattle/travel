	
var cnt = 1;
var interval_header = null;	

// widths:
//   laptop: 1583
//   ipad: 983
//   android: 800
//alert($(document).width());

function onResize()
{
	console.log('onResize...');
	
	var dc = { width: 0, height: 0, ppl: 0, margin: 0, readonly: false };

	resize(dc);
	
	var content = document.getElementById("content");
	content.style.display = 'block';
	
	var loader = document.getElementById("load-loop");
	loader.style.display = 'none';
}

// The wake lock sentinel.
let wakeLock = null;

// Function that attempts to request a screen wake lock.
const requestWakeLock = async () => {
  try {
    wakeLock = await navigator.wakeLock.request();
    wakeLock.addEventListener('release', () => {
      console.log('Screen Wake Lock released:', wakeLock.released);
    });
    console.log('Screen Wake Lock released:', wakeLock.released);
  } catch (err) {
    console.error(`${err.name}, ${err.message}`);
  }
};


function resize(dc)
{
	if ('wakeLock' in navigator) {
		console.log('wakeLock supported');
	  
		// Request a screen wake lock…
		//orig: await requestWakeLock();
		requestWakeLock();

		// …and release it again after 5s.
		window.setTimeout(() => {
		  wakeLock.release();
		  wakeLock = null;
		}, 5000);


	}
	else {
	  console.log('wakeLock NOT supported');
	}
	
	//return;
	
	var browserWidth = window.innerWidth;
	var w = 0;
		
	// get margin value from photo box
	var box = $('.frontpage-box');
	console.log("box = " + box);
	if (typeof box === 'object')
	{
		console.log('box is object');
	}
	else
	{
		console.log('box is NOT object');
	}
	
		
	var sMargin = $('.frontpage-box').css('margin-left'); 
	var margin = (typeof sMargin !== 'undefined') ? Number(sMargin.substring(0, 1)) : 5;
	//alert(sMargin);
	
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
	//orig: var isMicro = (deviceWidth <= 380);
	var isMicro = (window.innerWidth <= 380);
	
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
			photosPerLine = Math.floor(browserWidth / photoBaseWidth); // +1 so they'll always be smaller than base width
		else // landscape
			photosPerLine = Math.floor(browserWidth / photoBaseWidth) + 1; // +1 so they'll always be smaller than base width
	}
		
	//alert(browserWidth);

	// compute new photo width and height
	browserWidth -= (margin * photosPerLine);
	w = (browserWidth / photosPerLine) - (margin / photosPerLine);
	w = Math.floor(w);

	var widthTotal = ((w + margin) * photosPerLine);	// only for info
	var h = Math.floor(w * ratio);

	if ((widthTotal + 10) > window.innerWidth)
	{
		w -= 4;
		widthTotal = ((w + margin) * photosPerLine);	// only for info
	}
	
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
		$('.frontpage-box-link').css({width:w+'px', height:h+'px'});
		// sbw new: $('.frontpage-box').css({width:w+'px', height:h+'px'});
	
	if (false)
		flash("doc-width: " + window.innerWidth 
		+ ", screen.width: " + screen.width 
		+ ", screen.height: " + screen.height 
		+ ", deviceWidth: " + deviceWidth 
		+ ", ppl: " + photosPerLine 
		+ ", w=" + w 
		+ ", h=" + h 
		+ ", wt=" + widthTotal
		+ ", micro=" + isMicro
		+ ", vert=" + isPortrait
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

