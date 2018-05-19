@extends('layouts.app')

@section('content')

<div class="container">
	@if (Auth::check())
		<h1>Tags</h1>
		<a href="/tags/add" class="btn btn-primary">Add</a>
		<table class="table">
			<tbody>@foreach($tags as $tag)
				<tr>
					<td style="width:10px;">
						<a href='/tags/edit/{{$tag->id}}'><span class="glyphCustom glyphicon glyphicon-edit"></span></a>
					</td>
					<td style="width:10px; padding-right:20px;">
						<a href='/tags/confirmdelete/{{$tag->id}}'><span class="glyphCustom glyphicon glyphicon-trash"></span></a>
					</td>
					<td>
						<a target="" href="/tags/entries/{{$tag->id}}">{{$tag->name}}</a>
					</td>
				</tr>
			@endforeach</tbody>
		</table>
	@else
		<h3>You need to log in. <a href="/login">Click here to login</a></h3>
	@endif
               
</div>
@endsection
