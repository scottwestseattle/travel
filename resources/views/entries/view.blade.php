@extends('layouts.app')

@section('content')

<?php $gallery = isset($gallery) ? $gallery : null; ?>

<div class="page-size container">
               
	@guest
	@else
	
		@component('entries.menu-submenu', ['record' => $record])@endcomponent
		
		@if (Auth::user()->user_type >= 1000 || Auth::user()->id === $record->user_id)
		<div class="publish-pills">
			<ul class="nav nav-pills">
				@if ($record->published_flag === 0)
					<li class="active"><a href="/entries/publish/{{$record->id}}">@LANG('ui.Private')</a></li>
				@elseif ($record->approved_flag === 0)
					<li class="active"><a href="/entries/publish/{{$record->id}}">@LANG('ui.Pending Approval')</a></li>
				@endif
			</ul>
		</div>
		@endif
				
	@endguest
		
	<div class="form-group">
	
		<!------------------------------------>
		<!-- Top Navigation Buttons -->
		<!------------------------------------>
		
		@if (isset($prev) || isset($record->parent_id) || isset($next))
		<div style="margin-top: 10px;">
			@if (isset($prev))
				<a href="/entries/{{$prev->permalink}}"><button type="button" class="btn btn-blog-nav"><span style="margin-right:5px;" class="glyphicon glyphicon-circle-arrow-left"></span>@LANG('ui.Prev')</button></button></a>
			@endif
			@if ($record->type_flag == ENTRY_TYPE_BLOG_ENTRY)
				<a href="/blogs/show/{{$record->parent_id}}"><button type="button" class="btn btn-blog-nav">@LANG('content.Back to Blog')<span style="margin-left:5px;" class="glyphicon glyphicon-circle-arrow-up"></span></button></a>
				
			@if (Auth::user() && (Auth::user()->user_type >= 1000 || Auth::user()->id === $record->user_id))
				<a href="/blogs/addpost/{{$record->parent_id}}"><button type="button" class="btn btn-blog-nav">@LANG('content.Add New Post')<span style="margin-left:5px;" class="glyphicon glyphicon-plus-sign"></span></button></a>	
			@endif				
				
			@elseif($record->type_flag == ENTRY_TYPE_ARTICLE)
				<a href="/articles"><button type="button" class="btn btn-blog-nav">@LANG('content.Back to Articles')<span style="margin-left:5px;" class="glyphicon glyphicon-circle-arrow-up"></span></button></a>
			@endif
			@if (isset($next))
				<a href="/entries/{{$next->permalink}}"><button type="button" class="btn btn-blog-nav">@LANG('ui.Next')<span style="margin-left:5px;" class="glyphicon glyphicon-circle-arrow-right"></span></button></a>
			@endif	

		</div>
		@elseif (isset($backLink) && isset($backLinkText) && !((Auth::user() && (Auth::user()->user_type >= 1000))))
		<div style="margin-top: 10px;">
			<a href="{{$backLink}}">
				<button type="button" class="btn btn-blog-nav">{{$backLinkText}}
					<span style="margin-left:5px;" class="glyphicon glyphicon-circle-arrow-up"></span>
				</button>
			</a>			
		</div>
		@endif
		
		<h1 name="title" class="">{{$record->title}}</h1>
		<strong>

		@if (isset($record->location) && is_string($record->location))
			@if ($record->location_type != LOCATION_TYPE_COUNTRY)
				{{$record->location}}, {{$record->location_parent}}
			@else
				{{$record->location}}
			@endif
		@endif
		
		@if (isset($display_date))
			<br/>{{$display_date}}
		@endif
		
		@if (null !== Auth::user() && (Auth::user()->user_type >= 1000 || Auth::user()->id === $record->user_id))
			<br/>View Count: {{$record->view_count}}<br/>
			Word Count: {{str_word_count($record->description)}}<br/>
		@endif
		</strong>
	</div>
	
	<?php 
		//
		// show main photo
		//
		$photo_found = false;
		$width = 800;
		$base_folder = 'img/entries/';
		$photo_folder = $base_folder . $record->id . '/';
		$photo = $photo_folder . 'main.jpg';
								
		if (file_exists($photo) === FALSE)
		{
			$photo = '/img/theme1/placeholder.jpg';
			$width = 300;
		}
		else
		{
			$photo = '/' . $photo;
			$photo_found = true;
		}
		
		// map size
		$mapWidth = 500;
	?>

	@if ($record->photo_gallery !== null)
	<div style="display:default; margin-top:20px;">
		<img src="{{$record->photo_gallery_path}}/{{$record->photo_gallery}}" title="{{$record->photo_gallery_title}}" class="popupPhotos" style="max-width:100%; width:{{ $width }}" />
	</div>		
	@elseif ($record->photo !== null)
	<div style="display:default; margin-top:20px;">
		<img src="{{$record->photo_path}}/{{$record->photo}}" title="{{$record->photo_title}}" class="popupPhotos" style="max-width:100%; width:{{ $width }}" />
	</div>	
	@endif
	
	@if (strlen(trim($record->description_short)) > 0)
	<div class="entry" style="margin-bottom:20px;">
		<h3>@LANG('ui.Highlights')</h3>
		<div>{{$record->description_short}}</div>
	</div>
	@endif

	<div class="entry-div" style="margin-top:20px;width:100%;">
		<div class="entry" style="width:100%;">
			<span name="description" class="">{!! $record->description !!}</span>		
		</div>
	</div>
	
	<!-- if ($record->photo_count > 0 || count($record->photo_gallery_count) > 0) -->
	@if ((isset($photos) && count($photos) > 0) || (isset($gallery) && count($gallery) > 0))
	
		<div class="entry-div">
			<div class="entry amenity-item">
				<h3>@LANG('ui.PHOTOS')<!-- a style="font-size:.65em;" href="/photos/slideshow/{{$record->id}}">(Slideshow)</a --></h3>
			</div>
		</div>
			
		<div class="text-center" style="display:default; margin-top:5px;">
			@if (isset($photos))
			@foreach($photos as $photo)		
				@if ($photo->main_flag !== 1)
					<?php 
						$title = $photo->filename;  // just in case the others are empty
						
						if (isset($photo->alt_text) && strlen($photo->alt_text) > 0)
							$title = $photo->alt_text;
						
						if (isset($photo->location) && strlen($photo->location) > 0)
							$title .= ', ' . $photo->location;
					?>
					
					<span style="cursor:pointer;" onclick="popup({{$record->id}}, '{{$photo->filename}}', {{$photo->id}})">
						<img class="{{SHOW_XS_ONLY}}" id="{{$photo->id}}" style="width:100%; margin-bottom:5px;" title="{{$title}}" src="/img/entries/{{$record->id}}/{{$photo->filename}}" />
						<img class="{{SHOW_NON_XS}} popupPhotos" style="height:250px; max-width:100%; margin-bottom:5px;" title="{{$title}}" src="/img/entries/{{$record->id}}/{{$photo->filename}}" />
					</span>
				@endif
			@endforeach	
			@endif
			
			@if (isset($gallery))
			@foreach($gallery as $photo)
				<?php 
					$title = $photo->filename;  // just in case the others are empty
					
					if (isset($photo->alt_text) && strlen($photo->alt_text) > 0)
						$title = $photo->alt_text;
					
					if (isset($photo->location) && strlen($photo->location) > 0)
						$title .= ', ' . $photo->location;
				?>
			
				@if ($record->photo_id != $photo->id)
				<span style="cursor:pointer;" onclick="popup({{$photo->parent_id}}, '{{$photo->filename}}', {{$photo->id}})">
					<img class="{{SHOW_XS_ONLY}}" id="{{$photo->id}}" style="width:100%; margin-bottom:5px;" title="{{$title}}" src="/img/entries/{{$photo->parent_id}}/{{$photo->filename}}" />
					<img class="{{SHOW_NON_XS}} popupPhotos" style="height:250px; max-width:100%; margin-bottom:5px;" title="{{$title}}" src="/img/entries/{{$photo->parent_id}}/{{$photo->filename}}" />
				</span>
				@endif
			@endforeach
			@endif
		</div>

	@endif

	<!------------------------------------>
	<!-- Bottom Navigation Buttons -->
	<!------------------------------------>

	<div class="trim-text " style="max-width:100%; margin-top: 30px;">
		@if (isset($prev))
			<div class="" style="float:left; margin: 0 5px 5px 0;" >
				<a href="/entries/{{$prev->permalink}}"><button type="button" class="btn btn-nav-bottom"><span class="glyph-nav-bottom glyphicon glyphicon-circle-arrow-left"></span>{{$prev->title}}</button></a>
			</div>
		@endif
		@if (isset($next))
			<div style="float:left;">
			<span class="{{SHOW_NON_XS}}">
				<a href="/entries/{{$next->permalink}}"><button type="button" class="btn btn-nav-bottom">{{$next->title}}<span class="glyph-nav-bottom glyphicon glyphicon-circle-arrow-right"></span></button></a>
			</span>
			<span class="{{SHOW_XS_ONLY}}">
				<a href="/entries/{{$next->permalink}}"><button type="button" class="btn btn-nav-bottom"><span class="glyph-nav-bottom glyphicon glyphicon-circle-arrow-right"></span>{{$next->title}}</button></a>
			</span>
			</div>
		@endif			
	</div>
	
	<!------------------------------------>
	<!-- Comment Form                   -->
	<!------------------------------------>
	
	<div class="text-center" style="margin-top: 50px; background-color: #f1f1f1; border-radius:15px;">
		<div style="display: inline-block; width: 95%; max-width:500px;">	
			<div style="" class="sectionHeader main-font">	
				<h3>@LANG('content.Leave a Comment')</h3>
			</div>

			<div class="text-left" style="font-size: 1em;">
				<form method="POST" action="/comments/create">
			
					<input type="hidden" name="parent_id" value="{{$record->id}}" />	
				
					<label for="name" class="control-label">@LANG('ui.Name'):</label>
					<input type="text" name="name" class="form-control" />

					<label for="comment" class="control-label" style="margin-top:20px;">@LANG('content.Comment'):</label>
					<textarea name="comment" class="form-control"></textarea>
		
					<div class="submit-button text-center" style="margin: 20px 0;">
						<button type="submit" name="update" class="btn btn-primary">@LANG('ui.Submit')</button>
					</div>

					{{ csrf_field() }}

				</form>
			</div>
		</div>
	</div>	

	<!------------------------------------>
	<!-- Comments                       -->
	<!------------------------------------>

	@if (isset($comments) && count($comments) > 0)
	<div class="text-center" style="margin-top: 50px;">
		<div style="display: inline-block; width: 95%; max-width:500px;">	
			<div style="" class="sectionHeader main-font">	
				<h3>@LANG('content.Comments')</h3>
			</div>

			<table>
			@foreach($comments as $comment)
			<tr style="max-width:100%; vertical-align:top;box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);">
				<td style="min-width:100px; font-size: 1.5em; padding:10px; vertical-align: center; color: white; background-color: #74b567; margin-bottom:10px;" >
					<div>{{strtoupper(date_format($comment->created_at, "M"))}}</div>
					<div>{{date_format($comment->created_at, "j")}}</div>
					<div>{{date_format($comment->created_at, "Y")}}</div>
				</td>
				<td style="max-width: 75%; width: 500px; color:default; padding: 0 10px; text-align:left;">
					<table>
					<tbody>
						<tr><td style="padding:10 0; font-size:2.0em; font-weight:bold;">{{$comment->name}}</td></tr>
						<tr><td>{{$comment->comment}}</td></tr>
					</tbody>
					</table>
				</td>
			</tr>
			<tr><td>&nbsp;</td><td></td></tr>
			@endforeach
			</table>
		</div>
	</div>	
	@endif
	
	
		<!-- GET YOUR GUIDE AD -->
		<!--
		<script async defer src="https://widget.getyourguide.com/v2/widget.js"></script>

		<div style="float:left; margin:20px;">
			<!-- a target="_blank" href="https://www.getyourguide.com/?partner_id=RTJHCDQ&utm_medium=online_publisher&placement=button-cta">Get Your Guide</a -->
			
<div data-gyg-partner-id="RTJHCDQ" data-gyg-number-of-items="3" data-gyg-currency="USD" data-gyg-locale-code="en-US" data-gyg-id="code-example" data-gyg-widget="activites" data-gyg-href="https://widget.getyourguide.com/RTJHCDQ/activities.frame"></div>

		</div>
		-->
	
	
@if ((isset($photos) && count($photos) > 0) || (isset($gallery) && count($gallery) > 0))
<!-- photo view popup -->
<div id="myModal" onclick="nextPhoto(false)" class="modal-popup text-center">
	<div  style="cursor:pointer;" class="modal-content">
		<span onclick="popdown()" id="modalSpan" class="close-popup">&times;</span>
		<img id="popupImg" style="max-width:900px;" width="100%" src="" />
		<div id="popupImgTitle"></div>
	</div>
</div>

<script>

// Get the modal
var modal = document.getElementById('myModal');

// Get the <span> element that closes the modal
var span = document.getElementById("modalSpan");

// When the user clicks on <span> (x), close the modal
span.onclick = function() {
	alert(2);
    modal.style.display = "none";
}

</script>		
@endif

@endsection
