@extends('layouts.master')

@section('content')
<div class="row">
    <div class="col-12 ui-sortable">
        <form action="" method="get" id="form-filter" class="row">
            <div class="form-group col-md-3 mb-3">
                <label for="month">Month</label>
                <input type="month" name="month" id="month" class="form-control" value="{{ request('month') ?? Carbon\Carbon::now('Asia/Jakarta')->format('Y-m') }}">
            </div>

            <div class="form-group col-md-3 mb-3">
                <label for="from">From</label>
                <select name="from" id="from" class="form-control">
                    <option disabled selected>-- Select Date --</option>
                    @foreach($dates as $date)
                    <option value="{{ $date }}" {{ request('from') == $date ? 'selected' : '' }}>{{ $date }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group col-md-3 mb-3">
                <label for="to">To</label>
                <select name="to" id="to" class="form-control">
                    <option disabled selected>-- Select Date --</option>
                    @foreach($dates as $dt)
                    <option value="{{ $dt }}" {{ request('to') == $dt ? 'selected' : '' }}>{{ $dt }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group col-md-3 mb-3">
                <label for="department">Department</label>
                <select name="department" id="department" class="form-control">
                    <option selected value="all">-- All Department --</option>
                    @foreach($departments as $dept)
                    <option value="{{ $dept->intiddepartemen }}" {{ request('department') == $dept->intiddepartemen ? 'selected' : '' }}>{{ $dept->txtnamadepartemen }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group col-md-3 mb-3">
                <button type="submit" class="btn btn-primary mt-3">Submit</button>
            </div>
        </form>
        <div class="panel panel-inverse">
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="text-center">Rekap Status GMP per-Tanggal</h5>
                                <canvas id="count-gmp"></canvas>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="text-center">Rekap Status Hygiene Declare per-Tanggal</h5>
                                <canvas id="count-health"></canvas>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12 mt-3">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="text-center">Hygiene Declare</h5>
                                <canvas id="health-chart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script src="{{ asset('/') }}plugins/chart.js/dist/chart.min.js"></script>

<script>
    $("#month").on('change', function() {
        $("#form-filter").submit()
    })
</script>

<script>
    let gmpok = @json($gmpok);
    let gmpnok = @json($gmpnok);
    let newdateList = @json($newdateList);

    let counthealth = "{{ $counthealth }}"
    let countnothealth = "{{ $countnothealth }}";

    let dailyhealth = @json($dailyhealth);
    let dailynothealth = @json($dailynothealth);

    let totalDaily = @json($totalDaily);
    let dates = @json($dates);

    let temps = @json($temps);
    let users = @json($users);

    var ctx = document.getElementById('count-gmp').getContext('2d');
    var barChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: newdateList,
            datasets: [{
                label: 'GMP OK',
                borderWidth: 2,
                borderColor: "#068FFF",
                backgroundColor: "#068FFF",
                data: gmpok
            }, {
                label: 'GMP NOK',
                borderWidth: 2,
                borderColor: "#EB8242",
                backgroundColor: "#EB8242",
                data: gmpnok
            }]
        }
    });

    var ctx2 = document.getElementById('count-health').getContext('2d');
    var barChart = new Chart(ctx2, {
        type: 'line',
        data: {
            labels: newdateList,
            datasets: [{
                label: 'Health OK',
                borderWidth: 2,
                borderColor: "#068FFF",
                backgroundColor: "#068FFF",
                data: dailyhealth
            }, {
                label: 'Health NOK',
                borderWidth: 2,
                borderColor: "#EB8242",
                backgroundColor: "#EB8242",
                data: dailynothealth
            }]
        }
    });

    var ctx3 = document.getElementById('health-chart').getContext('2d');
    var barChart = new Chart(ctx3, {
        type: 'line',
        data: {
            labels: dates,
            datasets: [{
                label: 'Total',
                borderWidth: 2,
                borderColor: "#068FFF",
                backgroundColor: "#068FFF",
                data: totalDaily
            }, ]
        }
    });
</script>
@endpush