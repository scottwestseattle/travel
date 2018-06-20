@extends('layouts.app')

@section('content')

<!--------------------------------------------------------------------------------------->
<!-- About Page -->
<!--------------------------------------------------------------------------------------->

<div class="container page-size main-font" style="">	

	<h1>About</h1>
	
	@if (isset($record))
	<div class="entry-div" style="margin-top:30px;">
		<div class="entry" style="">
			<span name="description" class="">{!! nl2br($record->description) !!}</span>				
		</div>
	</div>
	@endif
	
	@if (isset($record->photo))
	<div class="text-center" style="margin-top:50px;">
		<img style="max-width:300px; width:95%" src="{{$record->photo_path}}/{{$record->photo}}" title="{{$record->photo_path}}" />
	</div>
	@endif
		
</div>

@endsection
