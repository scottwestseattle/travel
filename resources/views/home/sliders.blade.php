@extends('layouts.app')

@section('content')

<div class="container">
	<h1 style="font-size:1.3em;">Sliders ({{ count($photos) }})</h1>
		<table class="table table-striped">
			<tbody>
			@foreach($photos as $slider)
				<tr>
					<td>
						<table>
							<tr><td>{{ $slider }}</td></tr>
							<tr>
								<td style="xwidth:20px;"><span class="glyphCustom glyphicon glyphicon-edit"></span></td>
								<td style="xwidth:20px;"><span class="glyphCustom glyphicon glyphicon-trash"></span></td>
							</tr>
						</table>
					</td>
					<td><a href="/view/{{$slider}}"><img src="/img/theme1/{{ $slider }}" style="width: 90%; max-width:500px"/></a></td>
				</tr>
			@endforeach
			</tbody>
		</table>
</div>
@endsection
