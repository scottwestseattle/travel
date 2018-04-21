@extends('layouts.app')

@section('content')

<div class="container">
	<h1 style="font-size:1.3em;">Articles ({{ count($entries) }})</h1>
	<table class="table table-striped">
		<tbody>
		@foreach($entries as $entry)
			<tr>
				<td>
					<a href="/view/{{$entry->id}}">{{$entry->title}}</a>
						
					<?php if (intval($entry->view_count) > 0) : ?>
						<span style="color:#8CB7DD; margin-left: 5px; font-size:.9em;" class="glyphCustom glyphicon glyphicon-copy"><span style="font-family:verdana; margin-left: 2px;" >{{ $entry->view_count }}</span></span>
					<?php endif; ?>						
				</td>
			</tr>
		@endforeach
		</tbody>
	</table>
</div>
@endsection
