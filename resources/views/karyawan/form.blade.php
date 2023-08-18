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
        <form action="{{ route('karyawan.update', $karyawan->id) }}" class="row mb-3" method="post" enctype="multipart/form-data">
            @method('PATCH')
            @csrf
            <div class="form-group mb-3">
                <label for="foto">Foto</label>
                <input type="file" name="foto" id="foto" class="form-control">

                @error('foto')
                <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('script')
<script src="{{ asset('/') }}plugins/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="{{ asset('/') }}plugins/datatables.net-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="{{ asset('/') }}plugins/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
<script src="{{ asset('/') }}plugins/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js"></script>
<script src="{{ asset('/') }}plugins/select2/dist/js/select2.min.js"></script>

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
</script>
@endpush