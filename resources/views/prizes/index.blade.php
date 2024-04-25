@extends('default')

@section('content')


    @include('prob-notice')
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>      
    @endif

    @if(session('errors') && session('errors')->has('message'))
        <div class="alert alert-danger">
            {{ session('errors')->first('message') }}
        </div>
    @endif

    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="d-flex justify-content-end mb-3">
                    <a href="{{ route('prizes.create') }}" class="btn btn-info">Create</a>
                </div>
                <h1>Prizes</h1>
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Title</th>
                            <th>Probability</th>
                            <th>Awarded</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($prizes as $prize)
                            <tr>
                                <td>{{ $loop->index + 1 }}</td>
                                <td>{{ $prize->title }}</td>
                                <td>{{ $prize->probability }}</td>
                                <td>
                                    @foreach ($prize->awardedPrizes as $awardedPrize)
                                        {{ $awardedPrize->awarded_count }}<br>
                                    @endforeach
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('prizes.edit', [$prize->id]) }}" class="btn btn-primary">Edit</a>
                                        {!! Form::open(['method' => 'DELETE', 'route' => ['prizes.destroy', $prize->id], 'id' => 'delete-form']) !!}
                                        {!! Form::submit('Delete', ['class' => 'btn btn-danger', 'onclick' => 'confirmDelete(event, '.$prize->id.')']) !!}
                                        {!! Form::close() !!}
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <div class="card">
                    <div class="card-header">
                        <h3>Simulate</h3>
                    </div>
                    <div class="card-body">
                        {!! Form::open(['method' => 'POST', 'route' => ['simulate']]) !!}
                        <div class="form-group">
                            {!! Form::label('number_of_prizes', 'Number of Prizes') !!}
                            {!! Form::number('number_of_prizes', null, ['class' => 'form-control']) !!}
                            @error('number_of_prizes')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        {!! Form::submit('Simulate', ['class' => 'btn btn-primary']) !!}
                        {!! Form::close() !!}
                    </div>

                    <br>

                    <div class="card-body">
                        {!! Form::open(['method' => 'POST', 'route' => ['reset']]) !!}
                        {!! Form::submit('Reset', ['class' => 'btn btn-primary']) !!}
                        {!! Form::close() !!}
                    </div>

                </div>
            </div>
        </div>
    </div>



    <div class="container  mb-4">
        <div class="row">
            <div class="col-md-6">
                <h2>Probability Settings</h2>
                <canvas id="probabilityChart"></canvas>
            </div>
            <div class="col-md-6">
                <h2>Actual Rewards</h2>
                <canvas id="awardedChart"></canvas>
            </div>
        </div>
    </div>


@stop


@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>

    <!-- Confirmation script for delete the prizes -->
    <script>
        function confirmDelete(event, id) {
            event.preventDefault(); // Prevent the form from submitting
    
            Swal.fire({
                title: 'Are you sure?',
                text: 'You will not be able to recover this prize!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Use the ID passed as an argument
                    document.getElementById('delete-form').action = '/prizes/' + id;
                    document.getElementById('delete-form').submit(); // Submit the form
                }
            });
        }
        
    </script>
    
    <!-- chart script for actual rewards and prize probabilities -->
    <script>
        var darkColors = [
            'rgba(33, 150, 243, 0.8)',
            'rgba(76, 175, 80, 0.8)',
            'rgba(255, 152, 0, 0.8)',
            'rgba(156, 39, 176, 0.8)',
            'rgba(255, 87, 34, 0.8)',
            'rgba(121, 85, 72, 0.8)'
        ];

        var ctx = document.getElementById('probabilityChart').getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: [
                    @foreach ($prizes as $prize)
                    '{{ $prize->title }} ({{ $prize->probability }}%)',
                    @endforeach
                ],
                datasets: [{
                    label: 'Probability',
                    data: [
                        @foreach ($prizes as $prize)
                            {{ $prize->probability }},
                        @endforeach
                    ],
                    backgroundColor: darkColors.slice(0, {{ count($prizes) }}),
                    borderColor: darkColors.slice(0, {{ count($prizes) }}),
                    borderWidth: 1
                }]
            },
            options: {
                plugins: {
                    datalabels: {
                        formatter: function(value, context) {
                            return value + '%';
                        }
                    }
                }
            }
        });

        var ctx2 = document.getElementById('awardedChart').getContext('2d');
        var myChart2 = new Chart(ctx2, {
            type: 'doughnut',
            data: {
                labels: [
                    @foreach ($prizes as $prize)
                    '{{ $prize->title }} ({{ $prize->awardedPrizes->sum('percentage') }}%)' ,
                    @endforeach
                ],
                datasets: [{
                    label: 'Awarded',
                    data: [
                        @foreach ($prizes as $prize)
                            @foreach ($prize->awardedPrizes as $awardedPrize)
                                {{ $awardedPrize->percentage }},
                            @endforeach
                        @endforeach
                    ],
                    backgroundColor: darkColors.slice(0, {{ count($prizes) }}),
                    borderColor: darkColors.slice(0, {{ count($prizes) }}),
                    borderWidth: 1
                }]
            },
            options: {
                plugins: {
                    datalabels: {
                        formatter: function(value, context) {
                            return value;
                        }
                    }
                }
            }
        });
    </script>

    
@endpush
