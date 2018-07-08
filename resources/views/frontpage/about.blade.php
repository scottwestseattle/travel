@extends('layouts.app')

@section('content')

<!--------------------------------------------------------------------------------------->
<!-- About Page -->
<!--------------------------------------------------------------------------------------->

<div class="container page-size main-font" style="">	

	<h1>About</h1>
	
	@if (isset($record))
	<div class="entry-div" style="margin-top:30px;">
		<div class="entry" style="">
			<span name="description" class="">{!! nl2br($record->description) !!}</span>				
		</div>
	</div>
	@endif
	
	<h3>Stats</h3>
	<table>
		<tbody>
			<tr><td style="min-width:150px;">Tours:</td><td>{{$stats['tours']}}</td></tr>
			<tr><td>Blogs:</td><td>{{$stats['blogs']}}</td></tr>
			<tr><td>Blog Posts:</td><td>{{$stats['blog-entries']}}</td></tr>
			<tr><td>Articles:</td><td>{{$stats['articles']}}</td></tr>
			<tr><td>Content Photos:</td><td>{{$stats['photos']}}</td></tr>
			<tr><td>Header Photos:</td><td>{{$stats['sliders']}}</td></tr>
		</tbody>
	</table>

	@if (isset($record->photo))
	<div class="text-center" style="margin-top:50px;">
		<img style="max-width:300px; width:95%" src="{{$record->photo_path}}/{{$record->photo}}" title="{{$record->photo_path}}" />
	</div>
	@endif
			
</div>

@endsection
