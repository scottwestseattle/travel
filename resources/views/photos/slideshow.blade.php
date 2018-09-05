@extends('layouts.app')

@section('content')

<div class="page-size container">

		<input type="hidden" name="parent_id" value="{{$photo->parent_id}}" />
	
		<div class="form-group">
			<span name="alt_text" class="">
				
		<div style="margin-top: 10px;">
			@if (isset($prev))
				<a href="/entries/{{$prev->permalink}}"><button type="button" class="btn btn-blog-nav"><span style="margin-right:5px;" class="glyphicon glyphicon-circle-arrow-left"></span>Prev</button></button></a>
			@endif
			@if ($record->type_flag == ENTRY_TYPE_BLOG_ENTRY)
				<a href="/blogs/show/{{$record->id}}"><button type="button" class="btn btn-blog-nav">Back to Blog Post<span style="margin-left:5px;" class="glyphicon glyphicon-circle-arrow-up"></span></button></a>
			@elseif($record->type_flag == ENTRY_TYPE_ARTICLE)
				<a href="/entries/{{$record->permalink}}"><button type="button" class="btn btn-blog-nav">Back to Article<span style="margin-left:5px;" class="glyphicon glyphicon-circle-arrow-up"></span></button></a>
			@endif
			@if (isset($next))
				<a href="/entries/{{$next->permalink}}"><button type="button" class="btn btn-blog-nav">Next<span style="margin-left:5px;" class="glyphicon glyphicon-circle-arrow-right"></span></button></a>
			@endif	

		</div>
		
				<p>{{trim($photo->alt_text)}}</p>
				
				@if (isset($photo->location) && strlen($photo->location) > 0)
					<p>Location: {{$photo->location}}</p>
				@endif

				<p>Photo Taken on {{date_format($photo->created_at, "l, F j, Y")}}.</p>
				
			</span>
		</div>
				
		<div class="form-group">
			<?php $location = (strlen($photo->location) > 0) ? ', ' . $photo->location : ''; ?>
			
			<img alt="{{$photo->alt_text}}" title="{{$photo->alt_text . $location}}" style="width:100%;" src="{{PHOTO_ENTRY_PATH}}/{{$photo->parent_id}}/{{$photo->filename}}" />
		</div>
		
</div>

@endsection
