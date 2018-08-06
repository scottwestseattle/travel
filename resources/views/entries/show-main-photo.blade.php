@if (isset($record->photo_gallery))
	<div class="{{$class}}" style="background-image: url('{{$record->photo_path_gallery}}/{{$record->photo_gallery}}'); "></div>
@elseif (isset($record->photo))
	<div class="{{$class}}" style="background-image: url('{{$record->photo_path}}/{{$record->photo}}'); "></div>
@else
	<div class="{{$class}}" style="background-image: url('./{{TOUR_PHOTO_PLACEHOLDER}}'); "></div>
@endif
