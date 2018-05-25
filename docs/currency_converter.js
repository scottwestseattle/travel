var originalAmt = 0;
var quote = 'quote failed';

function convert() 
{
	var amt = document.getElementById('TransactionAmount');
	var frontPage = (document.getElementById('showRateFrontPage') != null);

	if (amt != 'undefined' /*&& amt.value != 0*/)
	{
		if (originalAmt == 0)
			originalAmt = amt.value;		
		
		var s = document.getElementById('TransactionConvert');
		if (s != 'undefined')
		{
			if (!frontPage && s.selectedIndex == 0)
			{
				// put back original value
				$('#showRate').text('');
				$('#TransactionAmount').val(originalAmt);
			}
			else
			{
				////////////////////////////////////////////////////////////////
				// get FXE quote
				////////////////////////////////////////////////////////////////
		
				/* turned off
				$.ajax({
						type: 'GET',
						processData: false,
						crossDomain: true,
						dataType: "text",				
						url: "/scraper-quote.php?symbol=fxe",
						success: function (rate, textStatus, jqXHR) {
							quote = rate;
							
							var buy = 104.296;
							curr = Number(quote).toFixed(3);
							var output = (curr - buy).toFixed(3);
							if (curr > buy)
							{
								output = '+' + output;
							}
							output = 'fxe: ' + curr + " (" + output + ")";
							
							$('#rateQuote').text(output);
						},
						error: function (responseData, textStatus, errorThrown) {
							alert('quote failed:' + textStatus);
						}
				});
				*/
								
				////////////////////////////////////////////////////////////////
				// get USD to EUR quote
				////////////////////////////////////////////////////////////////
			
				var currency = (s[s.selectedIndex].text);
				var currFrom = currency.match(/^[A-Za-z]*/g);
				var currTo = currency.match(/[A-Za-z]*$/g);

				if (typeof currTo == 'object' && currTo.length > 0)
					currTo = currTo[0];
					
				//alert(currTo);
				
				$.ajax({
						type: 'GET',
						processData: false,
						crossDomain: true,
						dataType: "text",				
						url: "/scraper.php?from=" + currFrom + "&to=" + currTo,
						success: function (rate, textStatus, jqXHR) {
															
							if (rate.length > 10)
							{								
								if (frontPage)
								{
									$('#showRateFrontPage').text('Conversion Not Available');
								}
								else
								{
									$('#showRate').text('Conversion Not Available');
								}
								
								return;
							}
							
							var converted = amt.value * rate;
							
							if (frontPage)
							{
								var amount = amt.value * 1.0;
								var c = converted.toString();
								if (c.length > 5)
								{
									c = Number(converted.toFixed(5));
								}
									
								$('#showRateFrontPage').text(amount.toFixed(2) + ' ' + currFrom + ' = ' + c.toString() + ' ' + currTo);
							}
							else
							{
								$('#showRate').text(amt.value + ' x ' + rate + ' = ' + converted.toFixed(5));
								$('#TransactionAmount').val(converted.toFixed(2));
							}
						},
						error: function (responseData, textStatus, errorThrown) {
							alert('failed:' + textStatus);
						}
				});
				
			}
		}
	}
}
