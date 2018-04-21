@extends('layouts.app')

@section('content')

<div class="container">
               
<form method="POST" action="/entries/gen/{{ $entry->id }}">

	<div class="form-group">
		<h1 name="title" class="">{{$entry->title }}</h1>
	</div>
	
	<div class="entry-div">
	
		<div class="entry">
			<span name="description" class="">{!! $entry->description !!}</span>	
		</div>
		

	</div>

	<div style="display:default; margin-top:20px;">
	
		<?php 
			$width = "1000px";
			$photo = 'img/theme1/' . str_replace(":", "", $entry->title) . '.jpg';
			//dd(getcwd());
					
			if (file_exists($photo) === FALSE)
			{
				$photo = '/img/theme1/placeholder.jpg';
				$width = "300px";
			}
			else
			{
				$photo = '/img/theme1/' . str_replace(":", "", $entry->title) . '.jpg';
			}
		?>
	
		<img src="{{ $photo }}" style="max-width:100%; width:{{ $width }}" />
	</div>
	
	<?php if ($entry->title === 'West Seattle: Lincoln Park Hike') : ?>
	<div id="xttd-map" style="display:default; margin-top:20px;">
		<iframe id="xttd-map" src="https://www.google.com/maps/d/embed?mid=1DCC6Grd1QN9n_vAdYMMJCgy5h8Hegfa5" style="max-width:100%;" width="{{ $width }}" height="{{ floor($width * .75) }}"></iframe>					
	</div>
	<?php endif; ?>
	
	
{{ csrf_field() }}

</form>

</div>
@endsection
