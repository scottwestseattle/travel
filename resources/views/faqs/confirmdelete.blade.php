@extends('layouts.app')

@section('content')

@component('menu-submenu')
	@component('menu-icons-start')@endcomponent
	@component('menu-icons-links', ['data' => (isset($data) ? $data : null)])@endcomponent	
	@component('control-search', ['command' => '/faqs/search/', 'placeholder' => 'Search Kbase'])@endcomponent
@endcomponent

<div class="container">
	<h1>Delete</h1>

	<div class="form-control-big">
		<form method="POST" action="/faqs/delete/{{ $faq->id }}">

			<div class="form-group">
				<span name="title" class="">{{$faq->title }}</span>	
			</div>

			<div class="form-group">
				<span name="link" class="">{{$faq->link }}</span>	
			</div>

			<div class="form-group">
				<span name="description" class="">{{$faq->description }}</span>	
			</div>
			
			<div class="form-group">
				<button type="submit" class="btn btn-primary">Confirm Delete</button>
			</div>
		{{ csrf_field() }}
		</form>
	</div>
</div>
@endsection
