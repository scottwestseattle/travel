<td><a href="#" onclick="window.history.back()"><span style="font-size: 23px" class="glyphCustom glyphicon glyphicon-circle-arrow-left"></span></a></td>
<td><a href='/transactions/summary/'><span class="glyphCustom glyphicon glyphicon-home"></span></a></td>
@if (null !== session('transactionFilter'))
<td><a href='/transactions/filter/'><span class="glyphCustom glyphicon glyphicon-filter"></span></a></td>
@endif
<td><a href='/transactions'><span class="glyphCustom glyphicon glyphicon-list"></span></a></td>
<td><a href='/accounts/index/'><span class="glyphCustom glyphicon glyphicon-piggy-bank"></span></a></td>
<td><a href='/transactions/expenses/'><span class="glyphCustom glyphicon glyphicon-usd"></span></a></td>
<td><a href='/email/check/'><span class="glyphCustom glyphicon glyphicon-envelope"></span></a></td>
