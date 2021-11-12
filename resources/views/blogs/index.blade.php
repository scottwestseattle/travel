@extends('layouts.app')

@section('content')

	<div class="container main-font page-size ">	
	
		<div class="text-center"><h1>@LANG('ui.Blogs') ({{ \App\Entry::getPublicBlogCount() }})</h1></div>

		<div class="row" style="margin-bottom:10px;">
				
			@foreach($records as $record)
			@if ($record->published_flag >= 1)
			<div style="max-width: 400px; padding:10px;" class="col-sm-4"><!-- outer div needed for the columns and the padding, otherwise they won't center -->
				<div class="drop-box blog-box"><!-- inner col div -->
					<!-- blog photo -->
					<a href="/blogs/show/{{$record->id}}">
						@if (!isset($record->photo))
							<div style="min-width:200px; min-height:220px; background-color: white; background-size: cover; background-position: center; background-image: url('{{$placeholder}}'); "></div>
						@else
							<div style="min-width:200px; min-height:220px; background-color: white; background-size: cover; background-position: center; background-image: url('{{$record->photo_path}}/{{$record->photo}}'); "></div>
						@endif
					</a>							
							
					<!-- blog text -->
					<div class="" style="padding:10px;">	

						<p><a href="/blogs/show/{{$record->id}}/all" style="color:green; text-decoration:none;">{{$record->post_count}} @LANG('ui.posts')</a></p>
					
						<a style="font-family: 'Volkhov', serif; color: black; font-size:1.4em; font-weight:bold; text-decoration: none; " href="/blogs/show/{{$record->id}}">{{ $record->title }}</a>						
						
						<p>{{$record->description_short}}</p>
						
					</div>
				</div><!-- inner col div -->
			</div><!-- outer col div -->
			@endif
			@endforeach					

		</div><!-- row -->									
				
		@if (\App\Tools::isAdmin())
		<h1 style="font-size:1.3em;">{{ 'Admin' }} ({{ count($records) }})</h1>
		<table class="table table-striped">
			<tbody>
			@foreach($records as $record)
				<tr>
					<td style="width:20px;"><a href='/entries/edit/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-edit"></span></a></td>
					<td style="width:20px;"><a href='/photos/entries/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-picture"></span></a></td>
					<td style="width:20px;"><a href='/entries/publish/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-flash"></span></a></td>
					<td>
						<a href="/blogs/view/{{$record->id}}">{{$record->title}}</a>
							
						<div>
						@if (isset($record->published_flag) && $record->published_flag == 0)
							<a href="/entries/publish/{{$record->id}}"><button type="button" class="btn btn-danger btn-alert">@LANG('ui.Private')</button></a>
						@endif
						@if (isset($record->approved_flag) && $record->approved_flag == 0)
							<a href="/entries/publish/{{$record->id}}"><button type="button" class="btn btn-danger btn-alert">@LANG('ui.Pending Approval')</button></a>
						@endif
						@if (strlen($record->permalink) === 0)
							<a href="/entries/edit/{{$record->id}}"><button type="button" class="btn btn-danger btn-alert">@LANG('ui.No Permalink')</button></a>
						@endif
						</div>
											
					</td>
					<td>
						<a href='/entries/confirmdelete/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-trash"></span></a>
					</td>
				</tr>
			@endforeach
			</tbody>
		</table>
		@endif 				
			
	</div><!-- container -->

@endsection
