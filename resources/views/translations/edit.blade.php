@extends('layouts.theme1')

@section('content')

<div class="container">

	@component('translations.menu-submenu', ['prefix' => $prefix])@endcomponent

	<h1>Edit Translations</h1>

	<form method="POST" action="/translations/update/{{$filename}}">
					
		<div class="form-group">		

		<?php $cnt = 0; $recs = $records['en']; ?>
		<table>
			<tr><th>Key</th><th>English</th><th>Spanish</th><th>Chinese</th></tr>
		@foreach($recs as $key => $value)
			<tr>
			<td>{{$key}}</td>
			<td style=""><input type="text" name="en{{++$cnt}}" class="form-control" value="{{$records['en'][$key]}}"></input></td>
			<td style=""><input type="text" name="es{{$cnt}}" class="form-control" value="{{$records['es'][$key]}}"></input></td>
			<td style=""><input type="text" name="zh{{$cnt}}" class="form-control" value="{{$records['zh'][$key]}}"></input></td>
			<tr>
		@endforeach
		</table>

		</div>
			
		<div class="submit-button">
			<button type="submit" name="update" class="btn btn-primary">Update</button>
		</div>

		{{ csrf_field() }}
		
	</form>

</div>

@stop
