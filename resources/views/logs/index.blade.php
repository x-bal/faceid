@extends('layouts.master', ['title' => $title, 'breadcrumbs' => $breadcrumbs])

@push('style')
<link href="{{ asset('/') }}plugins/datatables.net-bs4/css/dataTables.bootstrap4.min.css" rel="stylesheet" />
<link href="{{ asset('/') }}plugins/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css" rel="stylesheet" />
@endpush

@section('content')
<div class="panel panel-inverse">
    <!-- BEGIN panel-heading -->
    <div class="panel-heading">
        <h4 class="panel-title">{{ $title }}</h4>
        <div class="panel-heading-btn">
            <a href="javascript:;" class="btn btn-xs btn-icon btn-default" data-toggle="panel-expand"><i class="fa fa-expand"></i></a>
            <a href="javascript:;" class="btn btn-xs btn-icon btn-success" data-toggle="panel-reload"><i class="fa fa-redo"></i></a>
            <a href="javascript:;" class="btn btn-xs btn-icon btn-warning" data-toggle="panel-collapse"><i class="fa fa-minus"></i></a>
            <a href="javascript:;" class="btn btn-xs btn-icon btn-danger" data-toggle="panel-remove"><i class="fa fa-times"></i></a>
        </div>
    </div>
    <!-- END panel-heading -->
    <!-- BEGIN panel-body -->
    <div class="panel-body">
        <form method="GET" class="row mb-3">
            <div class="col-md-3 form-group">
                <label for="from">From</label>
                <input type="date" name="from" id="from" class="form-control" value="{{ request('from') ?? Carbon\Carbon::now('Asia/Jakarta')->format('Y-m-d') }}">
            </div>
            <div class="col-md-3 form-group">
                <label for="to">To</label>
                <input type="date" name="to" id="to" class="form-control" value="{{ request('to') ?? Carbon\Carbon::now('Asia/Jakarta')->format('Y-m-d') }}">
            </div>
            <div class="col-md-3 form-group">
                <label for="department">Separtment</label>
                <select name="department" id="department" class="form-control">
                    <option value="all" selected>-- All Department --</option>
                    @foreach($departments as $dept)
                    <option value="{{ $dept->intiddepartemen }}" {{ request('department') == $dept->intiddepartemen ? 'selected' : '' }}>{{ $dept->txtnamadepartemen }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 form-group mt-3">
                <button type="submit" class="btn btn-primary mt-1"><i class="fas fa-filter"></i> Filter</button>
            </div>
        </form>

        <form method="POST" class="row mb-3" action="{{ route('setting.update', $setting->id) }}">
            @method("PUT")
            @csrf
            <div class="col-md-3 form-group">
                <label for="limit">Limit Suhu</label>
                <input type="text" name="limit" id="limit" class="form-control" value="{{ $setting->val }}">
            </div>
            <div class="col-md-4 form-group mt-3">
                <button type="submit" class="btn btn-primary mt-1"><i class="fas fa-save"></i> Update</button>
                <a href="{{ route('logs.export') }}?from={{ request('from') }}&to={{ request('to') }}&department={{ request('department') }}" class="btn btn-success mt-1"><i class="fas fa-file-excel"></i> Export</a>
            </div>
        </form>

        <table id="datatable" class="table table-striped table-bordered align-middle">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Foto</th>
                    <th>Date Created</th>
                    <th>Nama Karyawan</th>
                    <th>Nik</th>
                    <th>Nama Dept</th>
                    <th>Beard</th>
                    <th>Moustache</th>
                    <th>Suhu</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="modal-dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Form Foto Karyawan</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
            </div>


            <form action="" method="post" id="form-edit">
                @method('PUT')
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <img src="" alt="" id="img-target" width="100">
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="beard">Beard</label>
                                <select name="beard" id="beard" class="form-control">
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                            <div class="form-group mb-3">
                                <label for="moustache">Moustache</label>
                                <select name="moustache" id="moustache" class="form-control">
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="javascript:;" class="btn btn-white" data-bs-dismiss="modal"><i class="fas fa-xmark"></i> Close</a>
                    <a href="" class="btn btn-success btn-download"><i class="fas fa-download"></i> Download</a>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

@push('script')
<script src="{{ asset('/') }}plugins/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="{{ asset('/') }}plugins/datatables.net-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="{{ asset('/') }}plugins/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
<script src="{{ asset('/') }}plugins/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js"></script>

<script>
    let from = $("#from").val();
    let to = $("#to").val();
    let department = $("#department").val();

    var table = $('#datatable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: {
            url: "{{ route('logs.get') }}",
            type: "GET",
            data: {
                "from": from,
                "to": to,
                "department": department,
            }
        },
        deferRender: true,
        pagination: true,
        columns: [{
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
                sortable: false,
                searchable: false
            },
            {
                data: 'foto',
                name: 'foto'
            },
            {
                data: 'dtmCreated',
                name: 'dtmCreated'
            },
            {
                data: 'txtName',
                name: 'txtName'
            },
            {
                data: 'txtNik',
                name: 'txtNik'
            },
            {
                data: 'department',
                name: 'department'
            },
            {
                data: 'beard',
                name: 'beard'
            },
            {
                data: 'moustache',
                name: 'moustache'
            },
            {
                data: 'suhu',
                name: 'suhu'
            },
            {
                data: 'status',
                name: 'status'
            },
        ]
    });

    $("#datatable").on('click', '.btn-action', function() {
        let route = $(this).attr('data-route')
        let id = $(this).attr('id');
        let url = "/logs/" + id
        $("#form-edit").attr("action", url);

        $.ajax({
            url: route,
            type: 'GET',
            method: 'GET',
            success: function(response) {
                let log = response.log;

                $("#img-target").attr("src", response.image)
                $("#beard").val(log.beard)
                $("#moustache").val(log.moustache)
            }
        })
    })
</script>
@endpush