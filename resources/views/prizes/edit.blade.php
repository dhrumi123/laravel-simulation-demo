@extends('default')

@section('content')

@include('prob-notice')

	{{-- @if($errors->any())
		<div class="alert alert-danger">
			@foreach ($errors->all() as $error)
				{{ $error }} <br>
			@endforeach
		</div>
	@endif --}}

	{{ Form::model($prize, array('route' => array('prizes.update', $prize->id), 'method' => 'PUT')) }}

		<div class="mb-3">
			{{ Form::label('title', 'Title', ['class'=>'form-label']) }}
			{{ Form::text('title', null, array('class' => 'form-control','placeholder' => 'Enter Title')) }}
			@error('title')
				<div class="text-danger">{{ $message }}</div>
			@enderror
		</div>
		<div class="mb-3">
			{{ Form::label('probability', 'Probability', ['class'=>'form-label']) }}
			{{ Form::number('probability', null, array('class' => 'form-control', 'placeholder' => '0 - 100','step' => '0.01')) }}
			@error('probability')
				<div class="text-danger">{{ $message }}</div>
			@enderror
		</div>

		{{ Form::submit('Edit', array('class' => 'btn btn-primary')) }}

	{{ Form::close() }}
@stop
