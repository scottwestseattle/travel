@extends('layouts.theme1')

@section('content')

<!--------------------------------------------------------------------------------------->
<!-- About Page -->
<!--------------------------------------------------------------------------------------->

<div class="container page-size main-font">	
	
	<h1>@LANG('ui.About')</h1>

	<div class="text-center">
		<a target="_blank" href="https://www.booking.com/index.html?aid=1535308">
			<img style="border: 1px solid black; width:100%; max-width:500px;" src="/img/banners/banner-booking-about.png" />
		</a>
	</div>

	<h3>@LANG('content.Most Recent Visitors')</h3>
	<div>
			
		<div class="drop-box text-center flag-box gray" style="">
			<div class="flag-box-title">@LANG('content.Last Visitor Country')</div>
			<p><img height="70" src="/img/flags/{{$visitorCountryInfo['lastCountryCode']}}.png" /></p>
			<div class="flag-box-footer">@LANG('geo.' . $visitorCountryInfo['lastCountry'])</div>
		</div>	

		<div class="drop-box text-center flag-box gray" style="">
			<div class="flag-box-title">@LANG('content.Newest Visitor Country')</div>
			<p><a href="/visitors/countries"><img height="70" src="/img/flags/{{$visitorCountryInfo['newestCountryCode']}}.png" /></a></p>
			<div class="flag-box-footer">@LANG('geo.' . $visitorCountryInfo['newestCountry'])</div>
		</div>

		<div style="clear: both;"></div>

		<p>{{$visitorCountryInfo['totalCountriesToday']}} @LANG('content.countries have visited this site today')</p>
		<p>
		@foreach($visitorCountryInfo['countriesToday'] as $country)
			<div style="display:inline-block; min-width:45px;">
				<img style="margin: 0 5px 5px 0;" height="30" src="/img/flags/{{strtolower($country->countryCode)}}.png" 
					alt="@LANG('geo.' . $country->country)" 
					title="@LANG('geo.' . $country->country)" />
			</div>
		@endforeach
		</p>
		
		<p>{{$visitorCountryInfo['totalCountries']}} @LANG('content.total countries have visited this site')</p>
		<p>
		<?php $cnt = 0; ?>
		@foreach($visitorCountryInfo['countries'] as $country)
			<div style="display:inline-block; min-width:45px;">
				<img style="margin: 0 5px 5px 0;" height="30" src="/img/flags/{{strtolower($country->countryCode)}}.png" 
					alt="@LANG('geo.' . $country->country)" 
					title="@LANG('geo.' . $country->country)" />
			</div>
			<?php $cnt++; if ($cnt >= 24) break; ?>
		@endforeach
		</p>
		<a href="/visitors/countries">@LANG('content.show all')</a>
	</div>
	
	<h3>@LANG('content.Content Stats')</h3>
	
	<div class="about-stats">
	<table>
		<tbody>
			@if ($stats['articles'] > 0)
				<tr><td>@LANG('ui.Articles'):</td><td>{{$stats['articles']}}</td></tr>
			@endif
			@if ($stats['blogs'] > 0)
				<tr><td>@LANG('ui.Blogs'):</td><td>{{$stats['blogs']}}</td></tr>
			@endif
			@if ($stats['blog_entries'] > 0)
				<tr><td>@LANG('ui.Blog Posts'):</td><td>{{$stats['blog_entries']}}</td></tr>
			@endif
			@if ($stats['galleries'] > 0)
				<tr><td>@LANG('ui.Galleries'):</td><td>{{$stats['galleries']}}</td></tr>
			@endif
			@if ($stats['hotels'] > 0)
				<tr><td>@LANG('content.Hotels'):</td><td>{{$stats['hotels']}}</td></tr>
			@endif
			@if ($stats['tours'] > 0)
				<tr><td style="min-width:150px;">@LANG('ui.Tours'):</td><td>{{$stats['tours']}}</td></tr>
			@endif
			@if ($stats['total_pages'] > 0)
				<tr><td style="padding-right:10px;"><b>@LANG('content.Total Content Pages'):</b></td><td><b>{{$stats['total_pages']}}</b></td></tr>
			@endif			
		</tbody>
	</table>
	</div>
	
	<div class="about-stats">
	<table>
		<tbody>
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
		</tbody>
	</table>
	</div>
	
	<div class="about-stats">
	<table>
		<tbody>
			<tr><td>@LANG('content.Static Pages'):</td><td>{{$stats['static_pages']}}</td></tr>
			<tr><td>@LANG('content.Content Pages'):</td><td>{{$stats['total_pages']}}</td></tr>
			<tr><td>@LANG('content.Photo Pages'):</td><td>{{$stats['total_sitemap_photos']}}</td></tr>
			<tr><td><b>@LANG('content.Site Map Pages'):</b></td><td><b>{{$stats['total_sitemap']}}</b></td></tr>			
		</tbody>
	</table>
	</div>
	
	<div style="clear:both;"></div>
	
	@if (isset($record))
		<div class="entry-div">
			<div class="entry">
				<span name="description" style="font-size:1.5em;">{!! nl2br($record->description) !!}</span>				
			</div>
		</div>
	@endif
	
	@if (false && isset($record->photo))
		<div class="text-center" style="margin-top:50px;">
			<img style="max-width:500px; width:95%" src="{{$record->photo_path}}/{{$record->photo}}" title="{{$record->photo_path}}" />
		</div>
	@elseif (isset($image))
		<div class="text-center" style="margin-top:50px;">
			<img style="max-width:500px; width:95%" src="{{$image}}" title="@LANG('content.About Page Image')" />
		</div>	
	@endif
		
	<h3>@LANG('content.Countries') ({{count($countries)}})</h3>
	<div>
		<p>
		<?php $last = end($countries); ?>
		@foreach($countries as $record)
			<button style="margin-bottom:10px;" type="button" class="btn btn-info">@LANG('geo.' . $record)</button>
		@endforeach
		</p>
	</div>
		
</div>

@endsection
