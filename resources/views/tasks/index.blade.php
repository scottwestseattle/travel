@extends('layouts.app')

@section('content')

@component('menu-submenu', ['data' => $data])
	@component('menu-icons-start')@endcomponent
@endcomponent

<div class="container">
	@if (Auth::check())
		<h1>Follow Up</h1>
		<a href="/tasks/add" class="btn btn-primary">Add</a>
		<table class="table">
			<thead>
				<tr>
					<th colspan="2">Properties</th>
				</tr>
			</thead>
			<tbody>@foreach($tasks as $task)
				<tr>
					<td style="width:10px;">
						<a href='/tasks/edit/{{$task->id}}'><span class="glyphCustom glyphicon glyphicon-edit"></span></a>
					</td>
					<td style="width:10px;">
						<a href='/tasks/confirmdelete/{{$task->id}}'><span class="glyphCustom glyphicon glyphicon-trash"></span></a>
					</td>
					<td style="width:100px;">
						<?php echo date('m-d-y', strtotime($task->created_at)); ?>
					</td>
					<td>
						<a target="_blank" href="{{ $task->link }}">{{$task->description}}</a>
					</td>
				</tr>
			@endforeach</tbody>
		</table>
	@else
		<h3>You need to log in. <a href="/login">Click here to login</a></h3>
	@endif
               
</div>
@endsection
