@extends('layouts.app')

@section('content')

<div class="page-size container">

		<input type="hidden" name="parent_id" value="{{$photo->parent_id}}" />
	
		<div class="form-group">
			<span name="alt_text" class="">
				
				<p><a href="#" onclick="window.history.back();"><span style="font-size: 23px" class="glyphCustom glyphicon glyphicon-circle-arrow-left"></span></a></p>
				
				<p>{{trim($photo->alt_text)}}</p>
				
				@if (isset($photo->location) && strlen($photo->location) > 0)
					<p>Location: {{$photo->location}}</p>
				@endif

				<p>Photo Taken on {{date_format($photo->created_at, "l, F j, Y")}}.</p>
				
			</span>
		</div>
				
		<div class="form-group">
			<?php $location = (strlen($photo->location) > 0) ? ', ' . $photo->location : ''; ?>
			
			<img alt="{{$photo->alt_text}}" title="{{$photo->alt_text . $location}}" style="width:100%;" src="{{$path}}/{{$photo->filename}}" />
		</div>
		
</div>

@endsection
