@extends('layouts.app')

@section('content')

@component('menu-submenu')
	@component('menu-icons-start')@endcomponent
	@component('menu-icons-links', ['data' => (isset($data) ? $data : null)])@endcomponent	
	@component('control-search', ['command' => '/faqs/search/', 'placeholder' => 'Search Kbase'])@endcomponent
@endcomponent

	<div class="container">
	<h1>Edit</h1>

	<div class="form-control-big">
		<form method="POST" action="/faqs/update/{{ $faq->id }}">

			<div class="form-group">
				<input type="text" name="title" class="form-control" value="{{$faq->title }}"></input>
			</div>

			<div class="form-group">
				<input type="text" name="link" class="form-control" placeholder="Link" value="{{$faq->link }}"></input>
			</div>

			<div class="form-group">
				<textarea name="description" style="height: 500px;" class="form-control">{{$faq->description }}</textarea>
			</div>
			
			<div class="form-group">
				<button type="submit" name="update" class="btn btn-primary">Update</button>
			</div>
			
			{{ csrf_field() }}
			
		</form>
	</div>

</div>

@stop
