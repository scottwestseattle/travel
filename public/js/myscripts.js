
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

function urlEncodeWithDate(fromId, fromYearId, fromMonthId, fromDayId, toId)
{	
    var fromElem = document.getElementById(fromId);
    var fromDay = document.getElementById(fromDayId);
    var fromMonth = document.getElementById(fromMonthId);
    var fromYear = document.getElementById(fromYearId);
	var toElem = document.getElementById(toId);
	if (fromElem && toElem && fromDay && fromMonth && fromYear)
	{
		toElem.value = encodeURI(fromElem.value.replace(/[\W_]+/g, "-").toLowerCase());
		
		if (fromYear.value > 0 && fromMonth.value > 0 && fromDay.value > 0)
		{			
			toElem.value += '-' + fromYear.value + '-' + pad(fromMonth.value, 2) + '-' + pad(fromDay.value, 2);
		}
	}
	else
	{
		alert('Error creating permalink');
	}
}

function createPhotoName(fromId, fromLocationId, toId)
{	
    var fromElem = document.getElementById(fromId);
    var fromLocation = document.getElementById(fromLocationId);
	var toElem = document.getElementById(toId);
	if (fromElem && toElem && fromLocation)
	{
		var location = fromLocation.value.trim().split(", ");
		if (location.length == 1)
		{
			location = fromLocation.value.trim().split(",");
			
			if (location.length == 1)
			{
				location = fromLocation.value.trim().split(" ");
			}
		}
		
		if (location.length > 2)
		{
			// flip the words, for example "Beijing, China" becomes "china-beijing"
			toElem.value = location[2] + '-';
			toElem.value += location[1] + '-';
			toElem.value += location[0] + '-';
		}
		else if (location.length > 1)
		{
			// flip the words, for example "Beijing, China" becomes "china-beijing"
			toElem.value = location[1] + '-';
			toElem.value += location[0] + '-';
		}
		else
		{
			// not sure of the format, just copy it as is
			toElem.value = fromLocation.value.trim() + '-';
		}
		
		toElem.value += fromElem.value.trim();
		
		// replace whitespace with '-' and make all lower
		toElem.value = encodeURI(toElem.value.replace(/[\W_]+/g, "-").toLowerCase());
	}
	else
	{
		alert('Error creating photo file name');
	}
}

function pad(number, length) 
{
    var str = '' + number;
	
    while (str.length < length) {
        str = '0' + str;
    }

    return str;
}

function changeDate(inc, fromYearId, fromMonthId, fromDayId)
{	
    var fromDay = document.getElementById(fromDayId);
	if (fromDay && parseInt(fromDay.value) == 0)
		fromDay = null;
	
    var fromMonth = document.getElementById(fromMonthId);
    var fromYear = document.getElementById(fromYearId);
	
//	var lastDayOfTheMonth = getLastDayOfMonth(parseInt(fromMonth.value));
	
	if (fromDay && fromMonth && fromYear) // using month/day/year
	{
		if (inc == 0) // this means clear the date
		{
			fromDay.value = 0;
			fromMonth.value = 0;
			fromYear.value = 0;
		}
		else if (inc == 99) // this means set to current day
		{
			var today = new Date();
			
			fromDay.value = today.getDate();
			fromMonth.value = today.getMonth() + 1;
			fromYear.value = today.getFullYear();
		}
		else
		{
			var newDate = parseInt(fromDay.value) + inc;
			
			// get last day of current month
			var lastDayOfMonth = new Date(parseInt(fromYear.value), parseInt(fromMonth.value), 0).getDate();

			if (newDate == 0) // roll to previous month
			{
				newMonth = parseInt(fromMonth.value) - 1;
				if (newMonth >= 0)
				{
					fromMonth.value = newMonth;
				}
				else
				{
					// roll around to previous year
					fromMonth.value = 12;
					fromYear.value = parseInt(fromYear.value) - 1;
				}
				
				// get last day of new month
				lastDayOfMonth = new Date(parseInt(fromYear.value), parseInt(fromMonth.value), 0).getDate();
				fromDay.value = lastDayOfMonth;
			}
			else if (newDate > lastDayOfMonth) // roll to next month
			{
				fromDay.value = 1;
				newMonth = parseInt(fromMonth.value) + 1;
				
				if (newMonth <= 12)
				{
					fromMonth.value = newMonth;
				}
				else
				{
					// roll over to next year
					fromMonth.value = 1;
					fromYear.value = parseInt(fromYear.value) + 1;
				}
			}
			else
			{
				fromDay.value = newDate;
			}
		}
	}
	else if (fromMonth && fromYear) // using month/year only so attempt to switch months
	{
		if (inc == 0) // this means clear the date
		{
			fromMonth.value = 0;
			fromYear.value = 0;
		}
		else if (inc == 99) // this means set to current day
		{
			var today = new Date();
			
			fromMonth.value = today.getMonth() + 1;
			fromYear.value = today.getFullYear();
		}
		else
		{
			var newDate = parseInt(fromMonth.value) + inc;
			
			if (newDate == 0) // roll to previous year
			{
				fromMonth.value = 12;
				fromYear.value = parseInt(fromYear.value) - 1;
			}
			else if (newDate > 12) // roll to next year
			{
				fromMonth.value = 1;
				fromYear.value = parseInt(fromYear.value) + 1;
			}
			else
			{
				// just change month
				fromMonth.value = newDate;
			}
		}
	}
	else
	{
		alert('Error changing dates');
	}
}

function decodeHtml(html) 
{
	var txt = document.createElement("textarea");
	txt.innerHTML = html;
	return txt.value;
}

function popup(id, filename, photo_id)
{
	var origImg = document.getElementById(photo_id);
	title = decodeHtml(origImg.title);
	
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
			popupImg.title = decodeHtml(photos.item(i).title);
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
