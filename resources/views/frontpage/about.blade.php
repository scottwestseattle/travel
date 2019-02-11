@extends('layouts.app')

@section('content')

<!--------------------------------------------------------------------------------------->
<!-- About Page -->
<!--------------------------------------------------------------------------------------->

<div class="container page-size main-font" style="">	
	
	<h1>@LANG('ui.About')</h1>
	
	<h3>@LANG('content.Content Stats')</h3>
	<table>
		<tbody>
			@if ($stats['blogs'] > 0)
				<tr><td>@LANG('ui.Blogs'):</td><td>{{$stats['blogs']}}</td></tr>
			@endif
			@if ($stats['tours'] > 0)
				<tr><td style="min-width:150px;">@LANG('ui.Tours'):</td><td>{{$stats['tours']}}</td></tr>
			@endif
			@if ($stats['galleries'] > 0)
				<tr><td>@LANG('ui.Galleries'):</td><td>{{$stats['galleries']}}</td></tr>
			@endif
			@if ($stats['articles'] > 0)
				<tr><td>@LANG('ui.Articles'):</td><td>{{$stats['articles']}}</td></tr>
			@endif
			@if ($stats['blog_entries'] > 0)
				<tr><td>@LANG('ui.Blog Posts'):</td><td>{{$stats['blog_entries']}}</td></tr>
			@endif
			@if ($stats['total_pages'] > 0)
				<tr><td style="padding-right:10px;"><b>@LANG('content.Total Content Pages'):</b></td><td><b>{{$stats['total_pages']}}</b></td></tr>
				<tr><td>&nbsp;</td><td>&nbsp;</td></tr>
			@endif

			@if ($stats['sliders'] > 0)
				<tr><td>@LANG('content.Featured Photos'):</td><td>{{$stats['sliders']}}</td></tr>
			@endif
			@if ($stats['photos_content'] > 0)
				<tr><td>@LANG('content.Content Photos'):</td><td>{{$stats['photos_content']}}</td></tr>
			@endif
			@if ($stats['photos_gallery'] > 0)
				<tr><td>@LANG('content.Gallery Photos'):</td><td>{{$stats['photos_gallery']}}</td></tr>
			@endif
			@if ($stats['total_photos'] > 0)
				<tr><td><b>@LANG('content.Total Photos'):</b></td><td><b>{{$stats['total_photos']}}</b></td></tr>
			@endif
			
			<tr><td>&nbsp;</td><td>&nbsp;</td></tr>
			<tr><td>@LANG('content.Static Pages'):</td><td>{{$stats['static_pages']}}</td></tr>
			<tr><td>@LANG('content.Content Pages'):</td><td>{{$stats['total_pages']}}</td></tr>
			<tr><td>@LANG('content.Photo Pages'):</td><td>{{$stats['total_sitemap_photos']}}</td></tr>
			<tr><td><b>@LANG('content.Site Map Pages'):</b></td><td><b>{{$stats['total_sitemap']}}</b></td></tr>
			
		</tbody>
	</table>
	
	@if (isset($record))
	<div class="entry-div" style="margin-top:30px;">
		<div class="entry" style="">
			<span name="description" class="">{!! nl2br($record->description) !!}</span>				
		</div>
	</div>
	@endif
	
	@if (isset($record->photo))
	<div class="text-center" style="margin-top:50px;">
		<img style="max-width:500px; width:95%" src="{{$record->photo_path}}/{{$record->photo}}" title="{{$record->photo_path}}" />
	</div>
	@endif
			
</div>

@endsection
