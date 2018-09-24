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
			@if ($stats['tours'] > 0)
				<tr><td style="min-width:150px;">Tours:</td><td>{{$stats['tours']}}</td></tr>
			@endif
			@if ($stats['blogs'] > 0)
				<tr><td>Blogs:</td><td>{{$stats['blogs']}}</td></tr>
			@endif
			@if ($stats['blog_entries'] > 0)
				<tr><td>Blog Posts:</td><td>{{$stats['blog_entries']}}</td></tr>
			@endif
			@if ($stats['articles'] > 0)
				<tr><td>Articles:</td><td>{{$stats['articles']}}</td></tr>
			@endif
			@if ($stats['total_pages'] > 0)
				<tr><td><b>Total Content Pages:</b></td><td><b>{{$stats['total_pages']}}</b></td></tr>
				<tr><td>&nbsp;</td><td>&nbsp;</td></tr>
			@endif

			@if ($stats['photos_content'] > 0)
				<tr><td>Content Photos:</td><td>{{$stats['photos_content']}}</td></tr>
			@endif
			@if ($stats['photos_gallery'] > 0)
				<tr><td>Gallery Photos:</td><td>{{$stats['photos_gallery']}}</td></tr>
			@endif
			@if ($stats['sliders'] > 0)
				<tr><td>Featured Photos:</td><td>{{$stats['sliders']}}</td></tr>
			@endif
			@if ($stats['total_photos'] > 0)
				<tr><td><b>Total Photos:</b></td><td><b>{{$stats['total_photos']}}</b></td></tr>
			@endif
		</tbody>
	</table>

	@if (isset($record->photo))
	<div class="text-center" style="margin-top:50px;">
		<img style="max-width:300px; width:95%" src="{{$record->photo_path}}/{{$record->photo}}" title="{{$record->photo_path}}" />
	</div>
	@endif
			
</div>

@endsection
