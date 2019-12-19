@extends('layouts.app')

@section('content')

<div class="page-size container">

@if (false)

<div class="text-center" style="margin:auto;">
	<div style="margin:auto; border: solid 1px #0f367c; position: relative; max-width:500px; background-color: LightGray; margin:20px 0;">
		<a href="/"><img style="width:100%;" src="/img/banners/banner-test.png" /></a>
		
		<!-- the button -->
		<div style="text-align:right; position: absolute; top: 0; right: 0;">
			<a style="margin: 5px 5px 0 0; vertical-align: top; background-color:#0f367c; color:white;" class="btn btn-info" 
				href="https://www.booking.com/index.html?aid=1535308" target="_blank" role="button">
				<div style="font-size:11px">@LANG('ads.Explore the world with')</div>
				<div style="font-size:18px">Button<span style="color:#449edd">@LANG('ads..com')</span></div>
			</a> 
		</div>
	</div>
</div>

@else

<div class="text-center" style="margin: 20px 0;">
	<div style="border: solid 1px #0f367c; line-height:100px; height:100px; width:100%; max-width:500px; 
		background-image:url('/img/banners/banner-test.png');">
		
		<a href="/"><img id="slider-spacer" src="/img/theme1/spacer-banner.png" width="100%" /></a>

		<div style="text-align: right;">
		<a style="margin: 5px 20px 0 0; vertical-align: top; background-color:#0f367c; color:white;" class="btn btn-info" 
			href="https://www.booking.com/index.html?aid=1535308" target="_blank" role="button">
			<div style="font-size:13px">Explore the world with</div>
			<div style="font-size:22px">Booking<span style="color:#449edd">.com</span></div>			
		</a> 
		</div>
	</div>
</div>

@endif

@endsection

