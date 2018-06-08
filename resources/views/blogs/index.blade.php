@extends('layouts.app')

@section('content')

<div class="page-size container">

	<h1 style="font-size:1.3em;">Blogs ({{ count($records) }})</h1>
		<table class="table table-striped">
			<tbody>
			@foreach($records as $record)
				<tr>
					<td>
						<a style="font-size:1.3em;" href="/blogs/show/{{$record->id}}">{{$record->title}}</a>
						
						@if (intval($record->view_count) > 0)
							<span style="color:#8CB7DD; margin-left: 5px; font-size:.9em;" class="glyphCustom glyphicon glyphicon-copy"><span style="font-family:verdana; margin-left: 2px;" >{{ $record->view_count }}</span></span>
						@endif
					</td>
				</tr>
			@endforeach
			</tbody>
		</table>
</div>

@endsection
