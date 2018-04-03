@extends('layouts.app')

@section('content')

@component('menu-submenu')
	@component('menu-icons-start')@endcomponent
	@component('menu-icons-links', ['data' => (isset($data) ? $data : null)])@endcomponent	
	@component('control-search', ['command' => '/faqs/search/', 'placeholder' => 'Search Kbase'])@endcomponent
@endcomponent

<div class="container">
               
	<div class="form-group">
		<h3 style="font-size:1.4em;" class="">{{$faq->title }}</h3>
	</div>
	
	<div style="padding:0; margin:0px; font-size:.5em;" class="submenu">
		<ul class="submenu">
			<li><a href='/faqs/edit/{{ $faq->id }}'><span class="glyphCustom glyphicon glyphicon-edit"></span></a></li>
			<li><a href='/faqs/confirmdelete/{{ $faq->id }}'><span class="glyphCustom glyphicon glyphicon-trash"></span></a></li>
		</ul>		
	</div>
		
	<div class="form-group" style="display:block; clear:both;">
		<span name="link" class="">{{$faq->link }}</span>
	</div>
	<div class="form-group">
		<span name="description" class="" style="font-size:1.2em;">{!! $faq->description !!}</span>
	</div>

</div>
@endsection
