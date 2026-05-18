@extends('layouts.app')
@section('title', 'Data Ibu')

@section('content')
<div class="topbar">
    <div class="page-header">
        <h1>👩 Data Ibu</h1>
        <p>Kelola data profil ibu yang terdaftar</p>
    </div>
    <a href="{{ route('ibu.create') }}" class="btn btn-primary">+ Tambah Ibu</a>
</div>

<div class="card fade-up">
    <div class="card-header">
        <div class="card-title">Daftar Ibu ({{ $ibu->total() }})</div>
        <form method="GET" style="display:flex;gap:10px;">
            <div class="search-bar">
                <span class="icon">🔍</span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama / NIK...">
            </div>
            <button class="btn btn-outline btn-sm" type="submit">Cari</button>
        </form>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Ibu</th>
                    <th>NIK</th>
                    <th>No. WhatsApp</th>
                    <th>Pekerjaan</th>
                    <th>Pendidikan</th>
                    <th>Jumlah Anak</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ibu as $i => $row)
                <tr>
                    <td class="text-muted">{{ $ibu->firstItem() + $i }}</td>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div class="avatar" style="width:36px;height:36px;font-size:14px;">
                                {{ strtoupper(substr($row->nama_ibu,0,1)) }}
                            </div>
                            <div>
                                <div style="font-weight:600;">{{ $row->nama_ibu }}</div>
                                <div style="font-size:11.5px;color:var(--text-muted);">{{ $row->umur }} tahun</div>
                            </div>
                        </div>
                    </td>
                    <td class="text-muted">{{ $row->nik }}</td>
                    <td>{{ $row->no_telepon }}</td>
                    <td><span class="badge badge-neutral">{{ $row->pekerjaan_label }}</span></td>
                    <td>{{ $row->pendidikan_label }}</td>
                    <td>
                        <span class="badge badge-info">{{ $row->anak_count }} anak</span>
                    </td>
                    <td>
                        <div style="display:flex;gap:6px;">
                            <a href="{{ route('ibu.show', $row) }}" class="btn btn-outline btn-sm">👁</a>
                            <a href="{{ route('ibu.edit', $row) }}" class="btn btn-outline btn-sm">✏️</a>
                            <form method="POST" action="{{ route('ibu.destroy', $row) }}"
                                  onsubmit="return confirm('Hapus data ibu ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger btn-sm">🗑</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8">
                    <div class="empty-state">
                        <div class="empty-icon">👩</div>
                        <div class="empty-title">Belum ada data ibu</div>
                        <a href="{{ route('ibu.create') }}" class="btn btn-primary btn-sm">Tambah Ibu Pertama</a>
                    </div>
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding:16px 22px;">
        {{ $ibu->withQueryString()->links('vendor.pagination.custom') }}
    </div>
</div>
@endsection
