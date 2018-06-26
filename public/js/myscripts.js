
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

function save()
{
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

function showAllRows(tableId, showAllButtonId)
{	
	var showAllButton = document.getElementById(showAllButtonId);
	showAllButton.style.display = "none";
	
	var rows = document.getElementById(tableId).rows;

	for(var i = 0; i < rows.length; i++)
	{
		rows[i].style.display = "block";
		//alert(rows[i].style.display);
	}		
}

function onCategoryChange(id)
{	
	var xhttp = new XMLHttpRequest();
	var url = '/categories/subcategories/' + id;
	
	xhttp.onreadystatechange = function() 
	{
		//alert(this.status);
		
		if (this.status == 200)
		{
			//alert(this.responseText);
		}
		else if (this.status == 404)
		{
			alert(this.responseText);
		}
					
		if (this.readyState == 4 && this.status == 200) 
		{	
			/*
			alert(
				'call response: ' + this.responseText +
				', length: ' + this.responseText.length 
				+ ', char: ' + this.responseText.charCodeAt(0) 
				+ ' ' + this.responseText.charCodeAt(1)
			);
			*/

			//
			// results
			//
			//alert(this.requestText);
				
			// get the select element
			var s = document.getElementById("subcategory_id");
			
			// replace the option list
			s.innerHTML = this.responseText;
		}
	};
	
	xhttp.open("GET", url, true);
	xhttp.send();
}
