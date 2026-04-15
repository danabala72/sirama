<form action="{{ route('attachment.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="card">
        <div class="card-body">
            <div class="d-flex align-items-center mb-3">
                <!-- Icon Box Biru seperti di gambar -->
                <div class="avatar bg-blue-lt m-3">
                    <i class="ti ti-cloud-upload text-blue p-2" style="font-size: 30px;"></i>
                </div>
                <div>
                    <h3 class="h2 mb-1">Unggah Bukti</h3>
                    <div class="text-muted">
                        Unggah file bukti capaian, sertifikat, video pengerjaan, ijazah, transkrip nilai, dan dokumen pendukung lainnya.
                    </div>
                </div>
            </div>

            <!-- Form Tanpa Opsi/Select -->
            <form action="{{ route('attachment.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">

                    <div class="mb-3">
                        <label class="form-label">Label / Kategori Dokumen</label>
                        <select name="label" class="form-select @error('label') is-invalid @enderror" required style="max-width:300px">
                            <option value="" selected disabled>Pilih Kategori...</option>
                            <option value="sertifikat_pelatihan">Sertifikat Pelatihan</option>
                            <option value="ijazah">Ijazah</option>
                            <option value="transkrip">Transkrip Nilai</option>
                            <option value="pengalaman">Pengalaman kerja</option>
                            <option value="sertifikat_kompetensi">Sertifikat kompetensi</option>
                            <option value="video">Video di tempat kerja</option>
                        </select>
                        @error('label')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <input type="file" name="files[]" class="form-control form-control" multiple required
                            accept=".pdf" style="max-width:300px">
                        <small class="form-hint mt-2">
                            @error('files')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            @if($errors->has('files.*'))
                            @foreach($errors->get('files.*') as $messages)
                            @foreach($messages as $message)
                            <div class="text-danger mt-1 small">{{ $message }}</div>
                            @endforeach
                            @endforeach
                            @endif
                            Seluruh lampiran wajib berformat PDF. Khusus konten video, harap menyertakan link/URL video ke dalam file PDF yang diunggah
                        </small>
                    </div>
                    <div class="d-flex justify-content-start">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="ti ti-upload me-2"></i> Upload
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</form>

<!-- List Hasil Upload di Bawahnya -->
<div class="card mt-3">
    <div class="table-responsive">
        <table class="table table-vcenter card-table table-striped">
            <thead>
                <tr>
                    <th>Nama Berkas</th>
                    <th>Kategori</th>
                    <th>Ukuran</th>
                    <th class="w-1"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($attachments as $item)
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            @if($item->file_type == 'video')
                            <i class="ti ti-video text-blue me-2"></i>
                            @elseif($item->file_type == 'image')
                            <i class="ti ti-photo text-green me-2"></i>
                            @else
                            <i class="ti ti-file-text text-orange me-2"></i>
                            @endif
                            <span class="text-truncate">{{ $item->file_name }}</span>
                        </div>
                    </td>
                    <td class="text-muted">{{$item->label}}</td>
                    <td class="text-muted">{{ number_format($item->file_size / 1048576, 2) }} MB</td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ asset('storage/' . $item->file_path) }}" target="_blank" class="btn btn-icon btn-ghost-primary" title="Lihat">
                                <i class="ti ti-eye"></i>
                            </a>
                            <form action="{{ route('attachment.delete', $item->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-icon btn-ghost-danger" title="Hapus">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="text-center text-muted py-4">Belum ada file yang diunggah.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4 d-flex gap-2">
 

    <a
        href="{{ route('form.step', 'step=3') }}"
        class="btn btn-outline-primary"        

        title="Lanjut ke Formulir 3"
    >
        <i class="ti ti-arrow-right me-1"></i>
        Ke Form 3
    </a>
</div>