@extends('layouts.app')

@section('content')

<div class="page-size container">

		<input type="hidden" name="parent_id" value="{{$photo->parent_id}}" />
		<input type="hidden" name="return_url" value="{{$return_url}}" />
		
		<div class="form-group">
				<?php $location = (strlen($photo->location) > 0) ? ', ' . $photo->location : ''; ?>
				<img alt="{{$photo->alt_text}}" title="{{$photo->alt_text . $location}}" style="width:100%;" src="{{$path}}/{{$photo->filename}}" />
		</div>
		
		@if (isset($prev) || isset($photo->parent_id) || isset($next))
		
			<div style="margin-top: 10px;">
		
				@if (isset($prev))
					<a href="/photos/view/{{$prev->id}}"><button type="button" class="btn btn-blog-nav"><span style="margin-right:5px;" class="glyphicon glyphicon-circle-arrow-left"></span>@LANG('ui.Prev')</button></button></a>
				@endif
			
				@if ($photo->parent_id != 0)
					<a href="/galleries/view/{{$photo->parent_id}}"><button type="button" class="btn btn-blog-nav">@LANG('content.Back to Gallery')<span style="margin-left:5px;" class="glyphicon glyphicon-circle-arrow-up"></span></button></a>
				@endif
				
				@if (isset($next))
					<a href="/photos/view/{{$next->id}}"><button type="button" class="btn btn-blog-nav">@LANG('ui.Next')<span style="margin-left:5px;" class="glyphicon glyphicon-circle-arrow-right"></span></button></a>
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
		</div>
		
</div>

@endsection
