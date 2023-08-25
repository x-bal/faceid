@extends('layouts.master', ['title' => $title, 'breadcrumbs' => $breadcrumbs])

@push('style')
<link href="{{ asset('/') }}plugins/datatables.net-bs4/css/dataTables.bootstrap4.min.css" rel="stylesheet" />
<link href="{{ asset('/') }}plugins/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css" rel="stylesheet" />
<link href="{{ asset('/') }}plugins/select2/dist/css/select2.min.css" rel="stylesheet" />
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

    <div class="panel-body">
        <form action="{{ route('karyawan.addperson') }}" class="row mb-3" method="post" id="form-export">
            @csrf
            <div class="col-md-3">
                <div class="form-group mb-3">
                    <label for="device">Device</label>
                    <select name="device" id="device" class="form-control">
                        <option disabled selected>-- Pilih device --</option>
                        @foreach($devices as $device)
                        <option value="{{ $device->id }}">{{ $device->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-6 mt-3 ms-auto">
                <button type="submit" value="add" id="btn-export" class="btn btn-success mt-1"></i> Export Foto</button>
            </div>

            <div class="col-md-3 mt-3">
                <a href="#modal-add" id="btn-add" class="btn btn-primary mt-1" style="float: right;" data-route="" data-bs-toggle="modal"><i class="ion-ios-add"></i> Add Foto</a>
            </div>
        </form>

        <table id="datatable" class="table table-striped table-bordered align-middle">
            <thead>
                <tr>
                    <th class="text-nowrap">#</th>
                    <th class="text-nowrap">Foto</th>
                    <th class="text-nowrap">Name</th>
                    <th class="text-nowrap">Created At</th>
                    <th class="text-nowrap">Action</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="modal-add">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Form Foto Karyawan</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
            </div>
            <form action="" method="post" id="form-foto" data-parsley-validate="true" enctype="multipart/form-data">
                @csrf

                <div class="modal-body">
                    <div class="form-group karyawan mb-3">
                        <label for="karyawan">Karyawan</label>
                        <select name="karyawan" id="karyawan" class="form-control default-select2">
                            <option disabled selected>-- Pilih Karyawan --</option>
                            @foreach($karyawan as $kry)
                            <option value="{{ $kry->intiduser }}">{{ $kry->txtnamauser }}</option>
                            @endforeach
                        </select>

                        @error('karyawan')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="foto">Foto</label>
                        <input type="file" name="foto" id="foto" class="form-control">

                        @error('foto')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="javascript:;" class="btn btn-white" data-bs-dismiss="modal"><i class="fa-solid fa-xmark"></i> Close</a>
                    <button type="submit" class="btn btn-success"><i class="fa-solid fa-floppy-disk"></i> Save</button>
            </form>
        </div>
    </div>
</div>

<form action="" class=" d-none" id="form-delete" method="post">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('script')
<script src="{{ asset('/') }}plugins/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="{{ asset('/') }}plugins/datatables.net-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="{{ asset('/') }}plugins/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
<script src="{{ asset('/') }}plugins/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js"></script>
<script src="{{ asset('/') }}plugins/select2/dist/js/select2.min.js"></script>
<script src="{{ asset('/plugins/sweetalert/dist/sweetalert.min.js') }}"></script>

<script>
    $("#karyawan").select2({
        dropdownParent: $('#modal-add')
    });

    var table = $('#datatable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: "{{ route('karyawan.get') }}",
        deferRender: true,
        pagination: true,
        columns: [{
                data: 'checkbox',
                name: 'checkbox'
            },
            {
                data: 'foto',
                name: 'foto'
            },
            {
                data: 'txtName',
                name: 'txtName'
            },
            {
                data: 'created',
                name: 'created'
            },
            {
                data: 'action',
                name: 'action',
            },
        ]
    });

    $("#datatable").on('click', '.check-karyawan', function() {
        let id = $(this).attr('id')

        if ($(this).is(':checked')) {
            $("#form-export").append(`<input type="hidden" name="idkary[]" id="kar-` + id + `" value="` + id + `">`);
        } else {
            $("#kar-" + id).remove()
        }

    })

    $("#datatable").on('click', '.btn-delete', function(e) {
        e.preventDefault();
        let route = $(this).attr('data-route')
        $("#form-delete").attr('action', route)

        swal({
            title: 'Hapus foto karyawan?',
            text: 'Menghapus foto karyawan bersifat permanen.',
            icon: 'error',
            buttons: {
                cancel: {
                    text: 'Cancel',
                    value: null,
                    visible: true,
                    className: 'btn btn-default',
                    closeModal: true,
                },
                confirm: {
                    text: 'Yes',
                    value: true,
                    visible: true,
                    className: 'btn btn-danger',
                    closeModal: true
                }
            }
        }).then((result) => {
            if (result) {
                $("#form-delete").submit()
            } else {
                $("#form-delete").attr('action', '')
            }
        });
    })
</script>
@endpush