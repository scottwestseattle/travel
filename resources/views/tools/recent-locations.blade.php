@extends('layouts.theme1')

@section('content')

<div class="page-size container">
		
	<h1>@LANG('content.Recent Locations') ({{count($records)}})</h1>

	<div class="text-center" style="margin-top: 50px;">
		<div style="display: inline-block; width: 95%;">
			<table>
			@foreach($records as $record)
			
			<tr class="drop-box" style="vertical-align:middle; box-shadow: 2px 2px 2px 2px rgba(0, 0, 0, 0.2), 0 1px 1px 0 rgba(0, 0, 0, 0.19);">
				<td style="min-width:100px; font-size: 1.5em; padding:10px; color: white; background-color: purple; margin-bottom:10px;" >
					<div style="margin:0; padding:0; line-height:100%;">
						<div style="font-family:impact; font-size:1.7em; margin:10px 0 10px 0;">@LANG('dt.' . strtoupper(date_format($record->display_date, "M")))</div>
						<!-- div style="font-family:impact; font-size:1.5em; margin-bottom:10px;">{{date_format($record->display_date, "j")}}</div -->
						<div style="font-family:impact; font-size:1.2em;">{{date_format($record->display_date, "Y")}}</div>
					</div>
				</td>
				<td style="color:default; padding: 0 10px; text-align:left; padding:15px;">
					<table>
					<tbody>
						<tr><td style="padding-bottom:10px; font-size:2em; font-weight:bold;">@LANG('geo.' . $record->name)</td></tr>
					</tbody>
					</table>
				</td>
			</tr>
			
			<tr><td>&nbsp;</td><td></td></tr>
			
			@endforeach
			</table>
		</div>
	</div>	
              
</div>
@endsection
