@extends('layouts.app')

@section('content')

@component('menu-submenu')
	@component('menu-icons-start')@endcomponent
	@component('menu-icons-links', ['data' => (isset($data) ? $data : null)])@endcomponent	
	@component('control-search', ['command' => '/faqs/search/', 'placeholder' => 'Search Kbase', 'popup' => false ])@endcomponent
@endcomponent

<div id="" class="container">
	@if (Auth::check())
		<h1>Faqs</h1>
		<a href="/faqs/add" class="btn btn-primary">Add</a>
		<span id="searchList">
		<table class="table">
			<tbody>@foreach($faqs as $faq)
				<tr>
					<td style="width:10px;">
						<a href='/faqs/edit/{{$faq->id}}'><span class="glyphCustom glyphicon glyphicon-edit"></span></a>
					</td>
					<td style="width:10px; padding-right:20px;">
						<a href='/faqs/confirmdelete/{{$faq->id}}'><span class="glyphCustom glyphicon glyphicon-trash"></span></a>
					</td>
										
					<td>
					@if (isset($faq->link) && mb_strlen($faq->link) > 0 && mb_substr($faq->link, 0, 4) === "http")
						<a style="font-size:1.1em;" href="{{$faq->link}}" target="_blank">{{$faq->title}}<span style="margin-left: 5px;" class="glyphCustom glyphicon glyphicon-share-alt"></span></a>
					@else
						<span style="font-size:1.1em;"><a style="font-weight:bold; color:gray;" href="/faqs/view/{{$faq->id}}">{{$faq->title}}</a></span>
						<div>{!! $faq->description !!}</div>
					@endif
					</td>
				</tr>
			@endforeach</tbody>
		</table>
		</span>
	@else
		<h3>You need to log in. <a href="/login">Click here to login</a></h3>
	@endif
               
</div>
@endsection
