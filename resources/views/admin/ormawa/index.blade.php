@extends('layouts.app')
@section('title', 'Kelola Ormawa')

@section('content')
<div class="card card-custom p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0"><i class="bi bi-diagram-3-fill text-primary me-2"></i>Kelola Data Ormawa</h4>
        <a href="{{ route('admin.ormawa.create') }}" class="btn btn-gradient-primary rounded-pill"><i class="bi bi-plus-circle me-1"></i> Tambah Ormawa</a>
    </div>

    <div class="table-responsive">
        <table class="table align-middle datatable">
            <thead>
                <tr>
                    <th>Nama Ormawa</th>
                    <th>Jenis</th>
                    <th>Periode</th>
                    <th>Ketua</th>
                    <th>Pembina</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($ormawas as $ormawa)
                    <tr>
                        <td><span class="fw-semibold">{{ $ormawa->nama_ormawa }}</span></td>
                        <td><span class="badge bg-info text-dark">{{ $ormawa->jenis }}</span></td>
                        <td>{{ $ormawa->periode }}</td>
                        <td>{{ $ormawa->ketua }}</td>
                        <td>{{ $ormawa->pembina ?? '-' }}</td>
                        <td>
                            <a href="{{ route('admin.ormawa.edit', $ormawa->id) }}" class="btn btn-sm btn-outline-primary me-1"><i class="bi bi-pencil-square"></i></a>
                            <form action="{{ route('admin.ormawa.destroy', $ormawa->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus ormawa ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
