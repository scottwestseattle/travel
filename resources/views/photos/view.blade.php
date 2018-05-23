@extends('layouts.app')

@section('content')

<div class="page-size container">

		<input type="hidden" name="parent_id" value="{{$photo->parent_id}}" />
	
		<div class="form-group">
			<span name="alt_text" class="">
				<p><a href="#" onclick="window.history.back();">&lt;&nbsp;Back to Photos</a></p>
				{{trim($photo->alt_text)}}
				@if (isset($photo->location) && strlen($photo->location) > 0)
					&nbsp;&mdash;&nbsp;{{$photo->location}}
				@endif
			</span>
		</div>
				
		<div class="form-group">
			<?php $location = (strlen($photo->location) > 0) ? ', ' . $photo->location : ''; ?>
			
			<img alt="{{$photo->alt_text}}" title="{{$photo->alt_text . $location}}" style="width:100%;" src="{{$path}}{{$photo->filename}}" />
		</div>
		
</div>

@endsection
