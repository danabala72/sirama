<form id="form-step1" method="POST" action="{{ route('form.step1.store') }}">
    @csrf
    @php
    $isStep1Complete = $mahasiswa
    && filled($mahasiswa->name)
    && filled($mahasiswa->tempat_lahir)
    && filled($mahasiswa->tgl_lahir)
    && filled($mahasiswa->jenis_kelamin)
    && filled($mahasiswa->status_perkawinan)
    && filled($mahasiswa->kebangsaan)
    && filled($mahasiswa->alamat_rumah)
    && filled($mahasiswa->kode_pos)
    && filled($mahasiswa->no_hp)
    && filled($mahasiswa->alamat_kantor)
    && filled($mahasiswa->email);
    @endphp
    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    @if($errors->any())
    <div class="alert alert-danger">
        <div class="fw-semibold mb-1">Periksa kembali input berikut:</div>
        <ul class="mb-0 ps-3">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="mb-3">
        <label class="form-label">Jurusan</label>
        <input type="text" class="form-control"
            value="{{ $user->jurusan->nama_jurusan ?? 'Belum memilih jurusan' }}"
            disabled>
    </div>

    <div class="mb-3">
        <label class="form-label">Nama Lengkap</label>
        <input
            type="text"
            name="name"
            class="form-control @error('name') is-invalid @enderror"
            placeholder="Masukkan nama lengkap"
            value="{{ old('name', $mahasiswa?->name) }}">
        @error('name')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <input
        type="hidden"
        name="user_id"
        value="{{ old('user_id', $mahasiswa?->user_id) }}">

    <div class="mb-3">
        <label class="form-label">Tempat Lahir</label>
        <input
            type="text"
            name="tempat_lahir"
            class="form-control @error('tempat_lahir') is-invalid @enderror"
            value="{{ old('tempat_lahir', $mahasiswa?->tempat_lahir) }}">
        @error('tempat_lahir')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label class="form-label">Tanggal Lahir</label>
        <input
            type="date"
            name="tgl_lahir"
            class="form-control @error('tgl_lahir') is-invalid @enderror"
            value="{{ old('tgl_lahir', $mahasiswa?->tgl_lahir) }}">
        @error('tgl_lahir')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label class="form-label">Jenis Kelamin</label>
        <select name="jenis_kelamin" class="form-select @error('jenis_kelamin') is-invalid @enderror">
            <option value="">Pilih jenis kelamin</option>
            <option value="L" {{ old('jenis_kelamin', $mahasiswa?->jenis_kelamin) === 'L' ? 'selected' : '' }}>Laki-laki</option>
            <option value="P" {{ old('jenis_kelamin', $mahasiswa?->jenis_kelamin) === 'P' ? 'selected' : '' }}>Perempuan</option>
        </select>
        @error('jenis_kelamin')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label class="form-label">Status Perkawinan</label>
        <select name="status_perkawinan" class="form-select @error('status_perkawinan') is-invalid @enderror">
            <option value="Belum Kawin" {{ old('status_perkawinan', $mahasiswa?->status_perkawinan) === 'Belum Kawin' ? 'selected' : '' }}>Belum Kawin</option>
            <option value="Kawin" {{ old('status_perkawinan', $mahasiswa?->status_perkawinan) === 'Kawin' ? 'selected' : '' }}>Kawin</option>
        </select>
        @error('status_perkawinan')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label class="form-label">Kebangsaan</label>
        <input
            type="text"
            name="kebangsaan"
            class="form-control @error('kebangsaan') is-invalid @enderror"
            value="{{ old('kebangsaan', $mahasiswa?->kebangsaan) }}">
        @error('kebangsaan')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label class="form-label">Alamat</label>
        <textarea
            name="alamat_rumah"
            class="form-control @error('alamat_rumah') is-invalid @enderror"
            rows="3">{{ old('alamat_rumah', $mahasiswa?->alamat_rumah) }}</textarea>
        @error('alamat_rumah')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label class="form-label">Kode Pos</label>
        <input
            type="text"
            name="kode_pos"
            class="form-control @error('kode_pos') is-invalid @enderror"
            value="{{ old('kode_pos', $mahasiswa?->kode_pos) }}">
        @error('kode_pos')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label class="form-label">No HP</label>
        <input
            type="text"
            name="no_hp"
            class="form-control @error('no_hp') is-invalid @enderror"
            value="{{ old('no_hp', $mahasiswa?->no_hp) }}">
        @error('no_hp')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label class="form-label">Alamat Kantor</label>
        <input
            type="text"
            name="alamat_kantor"
            class="form-control @error('alamat_kantor') is-invalid @enderror"
            value="{{ old('alamat_kantor', $mahasiswa?->alamat_kantor) }}">
        @error('alamat_kantor')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label class="form-label">Email</label>
        <input
            type="email"
            name="email"
            class="form-control @error('email') is-invalid @enderror"
            value="{{ old('email', $mahasiswa?->email) }}">
        @error('email')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

     <div class="hr-text hr-text-left text-primary">Data Pendidikan Menengah</div>
    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label class="form-label">Nama Sekolah</label>
                <input type="text" name="nama_sekolah" class="form-control @error('nama_sekolah') is-invalid @enderror" 
                       value="{{ old('nama_sekolah', $mahasiswa?->nama_sekolah) }}" placeholder="Nama SMA/SMK/MA">
                @error('nama_sekolah')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <label class="form-label">Alamat Sekolah</label>
                <input type="text" name="alamat_sekolah" class="form-control @error('alamat_sekolah') is-invalid @enderror" 
                       value="{{ old('alamat_sekolah', $mahasiswa?->alamat_sekolah) }}">
                @error('alamat_sekolah')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
        <div class="col-md-2">
            <div class="mb-3">
                <label class="form-label">Tahun Lulus</label>
                <input type="number" name="tahun_lulus_sekolah" class="form-control @error('tahun_lulus_sekolah') is-invalid @enderror" 
                       value="{{ old('tahun_lulus_sekolah', $mahasiswa?->tahun_lulus_sekolah) }}" placeholder="YYYY">
                @error('tahun_lulus_sekolah')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
    </div>

    <div class="hr-text hr-text-left text-primary">Data Perguruan Tinggi (Jika Ada)</div>
    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label class="form-label">Nama Perguruan Tinggi</label>
                <input type="text" name="nama_pt" class="form-control @error('nama_pt') is-invalid @enderror" 
                       value="{{ old('nama_pt', $mahasiswa?->nama_pt) }}">
                @error('nama_pt')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <label class="form-label">Jurusan / Program Studi</label>
                <input type="text" name="prodi_pt" class="form-control @error('prodi_pt') is-invalid @enderror" 
                       value="{{ old('prodi_pt', $mahasiswa?->prodi_pt) }}">
                @error('prodi_pt')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label class="form-label">Program (D3/S1/Lainnya)</label>
                <input type="text" name="program_pt" class="form-control @error('program_pt') is-invalid @enderror" 
                       value="{{ old('program_pt', $mahasiswa?->program_pt) }}" placeholder="Contoh: D3 Akuntansi">
                @error('program_pt')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <label class="form-label">Tahun Lulus</label>
                <input type="number" name="tahun_lulus_pt" class="form-control @error('tahun_lulus_pt') is-invalid @enderror" 
                       value="{{ old('tahun_lulus_pt', $mahasiswa?->tahun_lulus_pt) }}" placeholder="YYYY">
                @error('tahun_lulus_pt')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
    </div>

    <div class="mt-4 d-flex gap-2">
        <button id="btn-simpan-step1" type="submit" class="btn btn-primary">
            <i class="ti ti-device-floppy me-1 fs-2" data-icon-default></i>
            <span class="spinner-border spinner-border-sm me-2 d-none" role="status" aria-hidden="true" data-icon-loading></span>
            <span data-label-default>Simpan</span>
            <span class="d-none" data-label-loading>Menyimpan...</span>
        </button>

        <a
            href="{{ $isStep1Complete ? route('form.step', 'step=2') : '#' }}"
            class="btn btn-outline-primary {{ $isStep1Complete ? '' : 'disabled' }}"
            aria-disabled="{{ $isStep1Complete ? 'false' : 'true' }}"
            tabindex="{{ $isStep1Complete ? '0' : '-1' }}"
            @if(!$isStep1Complete) onclick="return false;" @endif
            title="{{ $isStep1Complete ? 'Lanjut ke Formulir 2' : 'Isi dan simpan data Formulir 1 terlebih dahulu' }}">
            <i class="ti ti-arrow-right me-1"></i>
            Ke Form 2
        </a>
    </div>
</form>

<script>
    (() => {
        const form = document.getElementById('form-step1');
        const submitBtn = document.getElementById('btn-simpan-step1');
        if (!form || !submitBtn) return;

        form.addEventListener('submit', () => {
            submitBtn.disabled = true;
            submitBtn.querySelector('[data-icon-default]')?.classList.add('d-none');
            submitBtn.querySelector('[data-icon-loading]')?.classList.remove('d-none');
            submitBtn.querySelector('[data-label-default]')?.classList.add('d-none');
            submitBtn.querySelector('[data-label-loading]')?.classList.remove('d-none');
        });
    })();
</script>