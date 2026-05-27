<x-app-layout>
    <div class="container-xl p-2">
        <!-- Header & Tombol Kembali -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3 gap-2">

            <div>
                <h2 class="text-2xl font-bold text-gray-800 mb-0">
                    Daftar Mata Kuliah Pilihan
                </h2>
            </div>

            <div>
                <a href="{{ route('asesmen.index') }}"
                    class="btn btn-sm btn-outline-secondary">
                    <i class="ti ti-arrow-left me-1"></i>
                    Kembali
                </a>
            </div>

        </div>

        <div class="card mb-4 shadow-sm">

            <div class="card-header bg-light">
                <div class="fw-bold text-uppercase small">
                    Informasi Mahasiswa
                </div>
            </div>

            <div class="card-body">

                <div class="row g-3">

                    {{-- IDENTITAS --}}
                    <div class="col-md-6">
                        <div class="p-3 border rounded bg-white">

                            <div class="fw-bold mb-2">Identitas</div>

                            <div class="small mb-1">
                                <span class="text-muted">Nama:</span>
                                <span class="fw-semibold">{{ $mahasiswa->name ?? '' }}</span>
                            </div>

                            <div class="small mb-1">
                                <span class="text-muted">Jenis Kelamin:</span>
                                <span class="fw-semibold">
                                    {{ $mahasiswa->jenis_kelamin ? ($mahasiswa->jenis_kelamin === 'L' ? 'Laki-Laki' : 'Perempuan') : '' }}
                                </span>
                            </div>

                            <div class="small mb-1">
                                <span class="text-muted">TTL:</span>
                                <span class="fw-semibold">
                                    {{ $mahasiswa->tempat_lahir ?? '' }},
                                    {{ $mahasiswa->tgl_lahir ?? '' }}
                                </span>
                            </div>

                            <div class="small">
                                <span class="text-muted">Status:</span>
                                <span class="fw-semibold">{{ $mahasiswa->status_perkawinan ?? '' }}</span>
                            </div>

                        </div>
                    </div>

                    {{-- KONTAK --}}
                    <div class="col-md-6">
                        <div class="p-3 border rounded bg-white">

                            <div class="fw-bold mb-2">Kontak</div>

                            <div class="small mb-1">
                                <span class="text-muted">Email:</span>
                                <span class="fw-semibold">{{ $mahasiswa->email ?? '' }}</span>
                            </div>

                            <div class="small mb-1">
                                <span class="text-muted">No HP:</span>
                                <span class="fw-semibold">{{ $mahasiswa->no_hp ?? '' }}</span>
                            </div>

                            <div class="small">
                                <span class="text-muted">Alamat:</span><br>
                                <span class="fw-semibold">{{ $mahasiswa->alamat_rumah ?? '' }}</span>
                            </div>

                        </div>
                    </div>

                    {{-- PENDIDIKAN --}}
                    <div class="col-12">
                        <div class="p-3 border rounded bg-white">

                            <div class="fw-bold mb-2">Riwayat Pendidikan</div>

                            <div class="row g-2 small">

                                <div class="col-md-6">
                                    <span class="text-muted">SMA:</span>
                                    <span class="fw-semibold">{{ $mahasiswa->nama_sekolah ?? '' }}</span>

                                    <div class="text-muted">
                                        {{ $mahasiswa->alamat_sekolah ?? '' }}
                                        {{ $mahasiswa->tahun_lulus_sekolah ? '(' . $mahasiswa->tahun_lulus_sekolah . ')' : '' }}
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <span class="text-muted">Perguruan Tinggi:</span>
                                    <span class="fw-semibold">{{ $mahasiswa->nama_pt ?? '' }}</span>

                                    <div class="text-muted">
                                        {{ $mahasiswa->prodi_pt ?? '' }}
                                        {{ $mahasiswa->program_pt ? ' - ' . $mahasiswa->program_pt : '' }}
                                        {{ $mahasiswa->tahun_lulus_pt ? '(' . $mahasiswa->tahun_lulus_pt . ')' : '' }}
                                    </div>
                                </div>

                            </div>

                        </div>
                    </div>

                </div>

            </div>
        </div>

        <!-- Detail Singkat Card -->
        <div class="card shadow-sm border-0 mb-3 bg-gray-50">
            <div class="card-body p-3">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="text-muted small">Program Studi</div>
                        <div class="font-medium text-gray-900">{{ $mahasiswa->user->jurusan->nama_jurusan ?? '-' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabel List Mata Kuliah -->
        <div class="card shadow-sm border-0">
            <div class="table-responsive">
                <table class="table table-vcenter card-table table-hover">
                    <thead>
                        <tr class="bg-gray-100 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            <th class="w-1">No</th>
                            <th>Mata Kuliah Pilihan</th>
                            <th>SKS</th>
                            <th>Penilaian</th>
                            <th>Metode Penilaian</th>
                            <th class="text-center">Status Penilaian</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pilihanMk as $index => $mk)
                        <tr class="transition">
                            <td class="text-muted text-sm">{{ $index + 1 }}</td>
                            <td class="text-sm">
                                <div class="font-medium text-gray-900">{{ $mk->mataKuliah->kode_mk ?? '-' }}</div>
                                <div class="text-muted small">{{ $mk->mataKuliah->nama_mk }}</div>
                            </td>
                            <td class="text-sm text-muted">
                                {{ $mk->mataKuliah->sks ?? 0 }} SKS
                            </td>
                            <td class="text-sm text-center">
                                @php
                                $hasAnyValue = false;

                                // Cek input nilai angka Formal
                                if ($mk->transferSks) {
                                $pFormal = $mk->transferSks->penilaian->where('asesor_id', $asesorId)->first();
                                if ($pFormal && !is_null($pFormal->hasil)) {
                                $hasAnyValue = true;
                                }
                                }

                                // Cek input nilai angka Non-Formal
                                if ($mk->transferSksNonFormal) {
                                $pNonFormal = $mk->transferSksNonFormal->penilaian->where('asesor_id', $asesorId)->first();
                                if ($pNonFormal && !is_null($pNonFormal->nilai)) {
                                $hasAnyValue = true;
                                }
                                }
                                @endphp

                                @if($hasAnyValue)
                                <span class="badge bg-success-lt">
                                    Dinilai
                                </span>
                                @else
                                <span class="badge bg-danger-lt">
                                    Belum
                                </span>
                                @endif
                            </td>
                            <td class="text-sm">
                                <div class="d-flex flex-wrap gap-1">

                                    @php
                                    $formalDone = false;

                                    if ($mk->transferSks) {

                                    $formalPenilaian = $mk->transferSks->penilaian
                                    ->where('asesor_id', $asesorId)
                                    ->first();

                                    $formalDone =
                                    $formalPenilaian &&
                                    !is_null($formalPenilaian->kesenjangan) &&
                                    !is_null($formalPenilaian->catatan_asesor) &&
                                    !is_null($formalPenilaian->hasil);
                                    }
                                    @endphp

                                    @if($mk->transferSks)

                                    <span class="badge {{ $formalDone ? 'bg-success-lt' : 'bg-red-lt' }}">
                                        Formal
                                    </span>

                                    @endif

                                    @php
                                    $nonFormalDone = false;

                                    if ($mk->transferSksNonFormal) {

                                    $nonFormalPenilaian = $mk->transferSksNonFormal->penilaian
                                    ->where('asesor_id', $asesorId)
                                    ->first();

                                    $nonFormalDone =
                                    $nonFormalPenilaian &&
                                    !is_null($nonFormalPenilaian->kesenjangan) &&
                                    !is_null($nonFormalPenilaian->catatan_asesor) &&
                                    !is_null($nonFormalPenilaian->nilai);
                                    }
                                    @endphp

                                    @if($mk->transferSksNonFormal)

                                    <span class="badge {{ $nonFormalDone ? 'bg-success-lt' : 'bg-red-lt' }}">
                                        Non-Formal
                                    </span>

                                    @endif


                                    {{-- ================= EMPTY ================= --}}
                                    @if(!$mk->transferSks && !$mk->transferSksNonFormal)

                                    <span class="badge bg-secondary-lt">
                                        -
                                    </span>

                                    @endif

                                </div>
                            </td>

                            <td class="text-sm text-center">

                                @php

                                $isFormalDone = false;

                                if ($mk->transferSks) {

                                $pNilai = $mk->transferSks->penilaian
                                ->where('asesor_id', $asesorId)
                                ->first();

                                $isFormalDone =
                                $pNilai &&
                                !is_null($pNilai->kesenjangan) &&
                                !is_null($pNilai->hasil);
                                }

                                // ================= CPMK =================
                                $isCpDone = $mk->cpLevels->isNotEmpty() &&
                                $mk->cpLevels->every(function ($cp) use ($asesorId) {

                                $penilaian = $cp->penilaian
                                ->where('asesor_id', $asesorId)
                                ->first();

                                return $penilaian &&
                                $penilaian->valid == 1 &&
                                $penilaian->asli == 1 &&
                                $penilaian->terkini == 1 &&
                                $penilaian->memadai == 1;
                                });

                                @endphp

                                @if($isFormalDone && $isCpDone)

                                <span class="badge bg-success-lt">
                                    Selesai Dinilai
                                </span>

                                @else

                                <span class="badge bg-warning-lt">
                                    Belum Lengkap
                                </span>

                                @endif

                            </td>
                            <td class="text-sm text-center">
                                <!-- Tombol Menuju Evaluasi Form Tunggal -->
                                <a href="{{ route('asesmen.review.detail', $mk->id) }}" class="btn btn-sm btn-primary">
                                    <i class="ti ti-edit me-1"></i> Mulai Asesmen
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <div class="text-muted small uppercase fw-bold">Tidak ada data mata kuliah pilihan</div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>