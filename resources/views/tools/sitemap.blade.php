@extends('layouts.app')

@section('content')

<div class="page-size container">
               
<h1>Site Maps ({{count($siteMaps)}})</h1>

	<div class="form-control-big">	

		@foreach ($siteMaps as $siteMap)
			<h3>{{count($siteMap['sitemap'])}} URLs written to: {{$siteMap['filename']}}</h3>
		@endforeach
				
		<h1>Details</h1>
		
		@foreach ($siteMaps as $siteMap)
	
		<h3>{{count($siteMap['sitemap'])}} URLs written to: {{$siteMap['filename']}}</h3>
	
		<table class="table" style="display:default;">
			
			@if (isset($siteMap['sitemap']))
			@foreach ($siteMap['sitemap'] as $record)
			<tr>
				<td><a target="_blank" href="{{$record}}">{{$record}}</a></td>
			</tr>
			@endforeach
			@endif
		
		</table>
		
		@endforeach
		
	</div>			
	
</div>

@endsection
