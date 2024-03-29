@extends('layouts.app')

@section('content')

<div class="page-size container">

		<input type="hidden" name="parent_id" value="{{$photo->parent_id}}" />
		
		@if (isset($prev) || isset($photo->parent_id) || isset($next))
			<div class="" style="margin-bottom: 10px;">
				@if (isset($prev))
					<a href="/photos/{{$prev->permalink}}/{{$prev->id}}"><button type="button" class="btn btn-blog-nav"><span style="margin-right:5px;" class="glyphicon glyphicon-circle-arrow-left"></span>@LANG('ui.Prev')</button></button></a>
				@endif
			
				@if (false && $photo->parent_id > 0)
					<a href="/galleries/view/{{$photo->parent_id}}"><button type="button" class="btn btn-blog-nav">@LANG('content.Back to Gallery')<span style="margin-left:5px;" class="glyphicon glyphicon-circle-arrow-up"></span></button></a>
				@endif
				
				@if (isset($next))
					<a href="/photos/{{$next->permalink}}/{{$next->id}}"><button type="button" class="btn btn-blog-nav">@LANG('ui.Next')<span style="margin-left:5px;" class="glyphicon glyphicon-circle-arrow-right"></span></button></a>
				@endif	
			</div>
		@endif
		
		<div class="form-group">
			<a href="/photos/{{isset($next) ? $next->permalink . '/' . $next->id : $first->permalink . '/' . $first->id}}">	
				<?php $location = (strlen($photo->location) > 0) ? ', ' . $photo->location : ''; ?>
				<img alt="{{$photo->alt_text}}" title="{{$photo->alt_text . $location}}" style="width:100%;" src="{{$path}}/{{$photo->filename}}" />
			</a>
		</div>
		
		@if (isset($prev) || isset($photo->parent_id) || isset($next))
			<div style="margin-top: 10px;">
				@if (isset($prev))
					<a href="/photos/{{$prev->permalink}}/{{$prev->id}}"><button type="button" class="btn btn-blog-nav"><span style="margin-right:5px;" class="glyphicon glyphicon-circle-arrow-left"></span>@LANG('ui.Prev')</button></button></a>
				@endif
						
				@if (isset($next))
					<a href="/photos/{{$next->permalink}}/{{$next->id}}"><button type="button" class="btn btn-blog-nav">@LANG('ui.Next')<span style="margin-left:5px;" class="glyphicon glyphicon-circle-arrow-right"></span></button></a>
				@endif	
			</div>
		@endif
	
		<div class="form-group" style="margin-top:10px;">
			<span name="alt_text" class="">
				
				@if (false)
				<p><a href="#" onclick="window.history.back();"><span style="font-size: 23px" class="glyphCustom glyphicon glyphicon-circle-arrow-left"></span></a></p>
				@endif
				
				<p>{{trim($photo->alt_text)}}</p>
				
				@if (isset($photo->location) && strlen($photo->location) > 0)
					<p>@LANG('ui.Location'): {{$photo->location}}</p>
				@endif

				@if (isset($photo->display_date))
					<p>@LANG('content.Photo Taken on'): {{date_format(date_create($photo->display_date), "l, F j, Y")}}.</p>					
				@else
					<p>@LANG('content.Photo Taken on'): {{date_format($photo->created_at, "l, F j, Y")}}.</p>
				@endif
				
			</span>
			
			@if ($photo->parent_id > 0)
				<a href="{{$backLink}}"><button type="button" class="btn btn-blog-nav">@LANG('content.' . $backLinkLabel)<span style="margin-left:5px;" class="glyphicon glyphicon-circle-arrow-up"></span></button></a>
			@endif
			
		</div>
		
	@if (isset($bannerIndex))
	<a href="https://www.booking.com/index.html?aid=1535308" target="_blank" >
	<div class="text-center" style="padding: 25px 10px 0 10px;">
		<div style="margin:auto; border: solid 1px #0f367c; line-height:75px; height:75px; width:100%; max-width:500px; 
			background-image:url('/img/banners/banner-booking-fp{{$bannerIndex}}.png');">
			<div style="text-align: right;">
				<a style="margin: 5px 5px 0 0; vertical-align: top; background-color:#0f367c; color:white;" class="btn btn-info" 
					href="https://www.booking.com/index.html?aid=1535308" target="_blank" role="button">
					<div style="font-size:11px">@LANG('ads.Explore the world with')</div>
					<div style="font-size:18px">Booking<span style="color:#449edd">@LANG('ads..com')</span></div>
				</a> 
			</div>
		</div>
	</div>	
	</a>
	@endif	
</div>

@endsection
