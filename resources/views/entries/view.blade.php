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
	
{{ csrf_field() }}

</form>

</div>
@endsection
