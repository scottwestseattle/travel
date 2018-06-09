@extends('layouts.app')

@section('content')

<div class="page-size container">

	<h1 style="font-size:1.3em;">Blogs ({{ count($records) }})</h1>
		
	<div class="{{SHOW_NON_XS}}">
	<table class="table">
		<tbody>
			@foreach($records as $record)
				<tr>
					<td style='width:50%;'>
						@if (isset($record->photo))
							<a href="/blogs/show/{{$record->id}}">
								<img title="{{ $record->photo_title }}" src="{{$record->photo_path}}{{$record->photo}}" style="width: 100%; max-width:500px"/>
							</a>
						@endif
					</td>
					<td style='width:50%'>
						<h3><a href="/blogs/show/{{$record->id}}">{{ $record->title }}</h3></a>
						{{ $record->description }}
					</td>
				</tr>
			@endforeach
		</tbody>
	</table>
	</div>
	
	<div class="{{SHOW_XS_ONLY}}">
	<table class="table">
		<tbody>
			@foreach($records as $record)
				<tr>
					@if (isset($record->photo))
						<a href="/blogs/show/{{$record->id}}">
							<img title="{{ $record->photo_title }}" src="{{$record->photo_path}}{{$record->photo}}" style="width: 100%;"/>
						</a>
					@endif
						
					<h3><a href="/blogs/show/{{$record->id}}">{{ $record->title }}</h3></a>
						
					{{ $record->description }}
				</tr>
			@endforeach
		</tbody>
	</table>	
	</div>
		
</div>

@endsection
