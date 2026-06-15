@extends('layouts.app')
@section('title', 'Kelola Pengguna')

@section('content')
<div class="card card-custom p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0"><i class="bi bi-people-fill text-primary me-2"></i>Kelola Akun Pengguna</h4>
        <a href="{{ route('admin.pengguna.create') }}" class="btn btn-gradient-primary rounded-pill"><i class="bi bi-plus-circle me-1"></i> Tambah Pengguna</a>
    </div>

    <div class="table-responsive">
        <table class="table align-middle datatable">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Ormawa</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                    <tr>
                        <td><span class="fw-semibold">{{ $user->nama }}</span></td>
                        <td>{{ $user->email }}</td>
                        <td>
                            <span class="badge bg-secondary text-uppercase">{{ str_replace('_', ' ', $user->role) }}</span>
                        </td>
                        <td>{{ $user->ormawa ? $user->ormawa->nama_ormawa : '-' }}</td>
                        <td>
                            <a href="{{ route('admin.pengguna.edit', $user->id) }}" class="btn btn-sm btn-outline-primary me-1"><i class="bi bi-pencil-square"></i></a>
                            @if(Auth::id() !== $user->id)
                                <form action="{{ route('admin.pengguna.destroy', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
