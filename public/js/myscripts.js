function loadPage(url) 
{
	//alert(url);
	
	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			//alert('call response: ' + this.responseText);
		}
	};
	
	xhttp.open("GET", url, true);
	xhttp.send();
}

function loadTag() 
{
	var ix = $('#tags').find(":selected").index();
	var href = '/entries/';
	if (ix == 0)
	{
	}
	else
	{
		var sel = $('#tags').find(":selected").text();	
		href = "/entries/tagged/" + sel.toLowerCase();
	}

	window.location.href = href;
}

function clearText(id)
{
	$(id).val('');
	$(id).focus();
}

function flip(from, to, useText = true)
{
	if (useText)
	{
		var one = $(from).text();
		var two = $(to).text();

		$(from).text(two);
		$(to).text(one);
	}
	else
	{
		var one = $(from).val();
		var two = $(to).val();

		$(from).val(two);
		$(to).val(one);
	}	
}

function copyFromTo(from, to)
{
	var f = $(from).html();
	var t = $(to).val();

	//f = f.replace(/<\/?[^>]+(>|$)/g, "");	
	
	//alert(f.replace("br", "sbw"));
	f = f.replace(/<br>/g, "\r\n");
	f = f.replace(/<br\/>/g, "\r\n");
	f = f.replace(/<font>/g, "");
	f = f.replace(/<\/font>/g, "");
	f = f.replace(/<p>/g, "");
	f = f.replace(/<p\/>/g, "\r\n");
	
	//alert(f);
	//var lines = $(from).text().split('\n');
	//for(var i = 0; i < lines.length; i++){
	//	alert(i + ": " + lines[i]);
	//}
	//alert("from: " + f + ", to: " + t);
	
	//$(to).val(t.trim() + f.trim());
	$(to).val(f.trim());
}

function copyFromToInput(from, trx, to)
{
	var f = $(from).val();
	$(trx).text(f.trim());
	//copyFromTo(trx, to);
}

function copyToClipboardAndCount(idFlash, id, countUrl)
{
	//alert(countUrl);
	
	clipboardCopy(idFlash, id);
	
	loadPage(countUrl);
}

function clipboardCopy(idFlash, id)
{
	var text = document.getElementById(id).innerHTML;
	var target = document.createElement("textarea");
	target.style.position = "absolute";
	target.style.left = "-9999px";
    target.style.top = "0";
    target.id = "_hiddenCopyText_";
    document.body.appendChild(target);

	//var elem = document.getElementById(id);
	$("#" + idFlash + ' p').fadeTo('slow', 0.1).fadeTo('slow', 1.0);
	$("#" + idFlash).fadeTo('slow', 0.1).fadeTo('slow', 1.0);

	// remove the <br>'s and <p>'s and <span>'s
	text = text.replace(/(\r\n|\n|\r)/gm, "");
    text = text.trim().replace(/<br\/>/gi, "\n");
    text = text.trim().replace(/<br \/>/gi, "\n");
    text = text.trim().replace(/<br>/gi, "\n");
    text = text.trim().replace(/<p>/gi, "\n");
    text = text.trim().replace(/<\/p>/gi, "\n");

    text = text.trim().replace(/<span style="color:green;">/gi, "");
    text = text.trim().replace(/<\/span>/gi, "");

    target.textContent = text;
    //target.textContent = text.replace(/<br\s*[\/]?>/gi, "\n");
 		
    // select the content
    var currentFocus = document.activeElement;
    target.focus();
    target.setSelectionRange(0, target.value.length);
    
    // copy the selection
    var succeed;
    try {
    	  succeed = document.execCommand("copy");
    } catch(e) {
        succeed = false;
	}
}

function copyToClipboard(text) {
    window.prompt("Copy to clipboard: Ctrl+C, Enter", text);
  }
  
function selectText(id){
    var sel, range;
    var el = document.getElementById(id); //get element id
    if (window.getSelection && document.createRange) { //Browser compatibility
      sel = window.getSelection();
      if(sel.toString() == ''){ //no text selection
         window.setTimeout(function(){
            range = document.createRange(); //range object
            range.selectNodeContents(el); //sets Range
            sel.removeAllRanges(); //remove all ranges from selection
            sel.addRange(range);//add Range to a Selection.
        },1);
      }
    }else if (document.selection) { //older ie
        sel = document.selection.createRange();
        if(sel.text == ''){ //no text selection
            range = document.body.createTextRange();//Creates TextRange object
            range.moveToElementText(el);//sets Range
            range.select(); //make selection.
        }
    }
}  
  
function save()
{

	$( "#save" ).click();
}

function stay()
{
	$('#stay').prop('checked', true);	
	$( "#save" ).click();
}

function urlEncode(fromId, toId)
{
    var fromElem = document.getElementById(fromId);
	var toElem = document.getElementById(toId);
	if (fromElem && toElem)
	{
		toElem.value = encodeURI(fromElem.value.replace(/[\W_]+/g, "-").toLowerCase());
	}
	else
	{
		alert('Error creating permalink');
	}
}

function urlEncodeWithDate(fromId, fromDateId, toId)
{	
    var fromElem = document.getElementById(fromId);
    var fromDateElem = document.getElementById(fromDateId);
	var toElem = document.getElementById(toId);
	if (fromElem && fromDateElem && toElem)
	{
		toElem.value = encodeURI(fromElem.value.replace(/[\W_]+/g, "-").toLowerCase());
		toElem.value += "-" + fromDateElem.value;
	}
	else
	{
		alert('Error creating permalink');
	}
}

function popup(id, filename, title)
{	
	var popupDiv = document.getElementById("myModal");
	popupDiv.style.display = "block";
	
	var popupImg = document.getElementById("popupImg");
	popupImg.src = "/img/entries/" + id + "/" + filename;
	popupImg.title = title;
	
	var popupImgTitle = document.getElementById("popupImgTitle");
	popupImgTitle.innerHTML = title;
}

function nextPhoto(found)
{	
	var popupImg = null;
	var photos = document.getElementsByClassName("popupPhotos");
	var popupImg = document.getElementById("popupImg");
	var popupImgTitle = document.getElementById("popupImgTitle");

	for(var i = 0; i < photos.length; i++)
	{
		if (found)
		{
			popupImg.src = photos.item(i).src;
			popupImg.title = photos.item(i).title;
			popupImgTitle.innerHTML = popupImg.title;
			return;
		}

		// if it's the current photo and then set the found flag to stop at the 
		// next photo at the top of the next iterartion
		var count = i + 1; // if it's the last item don't consider it found so we can wrap to the first item
		if (count < photos.length && popupImg.src == photos.item(i).src)
		{
			found = true;
		}
	}	
	
	if (!found)
	{
		// show the first photo
		nextPhoto(true);
	}
}

function popdown()
{	
	var popupDiv = document.getElementById("myModal");
	popupDiv.style.display = "none";
}

