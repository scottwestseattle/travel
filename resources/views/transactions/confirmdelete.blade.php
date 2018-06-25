@extends('layouts.theme1')

@section('content')

<div class="container page-size">

	@component($prefix . '.menu-submenu', ['record' => $record, 'prefix' => $prefix])@endcomponent
	
	<h1>Delete {{$title}}</h1>

	<form method="POST" action="/{{$prefix}}/delete/{{ $record->id }}">
			   
		<h3 name="description" class="">{{$record->description }}</h3>

		<div class="form-group">
			<button type="submit" class="btn btn-primary">Confirm Delete</button>
		</div>		
	
		<p>{{$record->transaction_date }}</p>
		<p>{{$record->amount }}</p>
		
		<div class="submit-button">
			<button type="submit" class="btn btn-primary">Confirm Delete</button>
		</div>
		
	{{ csrf_field() }}
	</form>
</div>
@endsection
