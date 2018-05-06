@extends('layouts.app')

@section('content')

<div class="container">
	<h1 style="font-size:1.5em;"><span class="glyphSliders glyphicon glyphicon-plus-sign" style="margin-right: 10px;"></span>Sliders ({{ count($photos) }})</h1>
		<table class="table table-striped">
			<tbody>
			@foreach($photos as $slider)
				<tr>
					<td>
						<table>
							<tr><td>{{ $slider }}</td></tr>
							<tr><td style="padding-top:15px;"><span class="glyphSliders glyphicon glyphicon-edit"></span></td></tr>
							<tr><td style="padding-top:15px;"><span class="glyphSliders  glyphicon glyphicon-trash"></span></td></tr>
						</table>
					</td>
					<td><a href="/view/{{$slider}}"><img src="/img/theme1/{{ $slider }}" style="width: 100%; max-width:500px"/></a></td>
				</tr>
			@endforeach
			</tbody>
		</table>
</div>
@endsection
