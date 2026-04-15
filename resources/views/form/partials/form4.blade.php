<div class="card">
  <div class="card-body">

    <div class="text-muted mb-3">
      Silakan unduh template, isi dengan lengkap, lalu unggah dokumen yang sudah ditandatangani.
    </div>
    @if(!collect($attachments)->where('label', 'cv')->first() || !collect($attachments)->where('label', 'pernyataan')->first())
    <form method="POST" action="{{ route('attachment.store.cv') }}" enctype="multipart/form-data">
      @csrf

      <div class="row">

        <!-- CV -->
        @if(!collect($attachments)->where('label', 'cv')->first())
        <div class="col-md-6">
          <label class="form-label">Daftar Riwayat Hidup (CV)</label>

          <div class="btn-list mb-2">
            <a href="{{ asset('storage/template/FORM 4 (Formulir Daftar Riwayat Hidup).docx') }}"
              class="btn btn-outline-primary btn-sm">
              <i class="ti ti-download"></i> Template CV
            </a>
          </div>

          <input type="file" id="cvInput" name="cv" hidden accept="application/pdf">


          <div class="upload-box" id="cvBox">
            <label for="cvInput" class="upload-content">
              <i class="ti ti-upload" style="font-size:40px"></i>
              <div class="fw-bold mt-2">Seret file CV di sini</div>
              <div class="text-secondary">atau klik untuk memilih file</div>
            </label>

            <div id="cvPreview" class="file-preview mt-2"></div>
            <div id="cvError" class="text-danger small mt-1"></div>
          </div>

        </div>
        @endif


        <!-- Pernyataan -->
        @if(!collect($attachments)->where('label', 'pernyataan')->first())
        <div class="col-md-6">
          <label class="form-label">Pernyataan Calon Peserta</label>

          <div class="btn-list mb-2">
            <a href="{{ asset('storage/template/FORM 3 (Daftar Pelatihan dan Pengalaman Kerja).docx') }}"
              class="btn btn-outline-primary btn-sm">
              <i class="ti ti-download"></i> Template Pernyataan
            </a>
          </div>

          <input type="file" id="pernyataanInput" name="pernyataan" hidden accept="application/pdf">
          <div class="upload-box" id="pernyataanBox">
            <label for="pernyataanInput" class="upload-content">
              <i class="ti ti-upload" style="font-size:40px"></i>
              <div class="fw-bold mt-2">Seret file Pernyataan di sini</div>
              <div class="text-secondary">atau klik untuk memilih file</div>
            </label>

            <div id="pernyataanPreview" class="file-preview mt-2"></div>
            <div id="pernyataanError" class="text-danger small mt-1"></div>
          </div>

        </div>
        @endif

      </div>

      <div class="mt-3">
        <button type="submit" id="submitBtn" class="btn btn-primary" disabled>
          <i class="ti ti-upload"></i> Unggah Dokumen
        </button>
      </div>

    </form>
    @else
    <div class="alert alert-success my-2">
      <p>Semua dokumen sudah diunggah</p>
    </div>
    @endif


    @if($attachments->count())
    <div class="mt-4">

      <h4>Dokumen yang sudah diunggah</h4>

     <div class="table-responsive">
        <table class="table table-vcenter card-table">
        <thead>
          <tr>
            <th>Label</th>
            <th>Nama File</th>
            <th width="150">Aksi</th>
          </tr>
        </thead>

        <tbody>

          @foreach($attachments as $file)
          <tr>

            <td>
              @if($file->label == 'cv')
              CV
              @elseif($file->label == 'pernyataan')
              Pernyataan
              @endif
            </td>

            <td>
              <a href="{{ asset('storage/'.$file->file_path) }}" target="_blank">
                {{ $file->file_name }}
              </a>
            </td>

            <td>

              <form method="POST" action="{{ route('attachment.delete',$file->id) }}">
                @csrf
                @method('DELETE')

                <button class="btn btn-danger btn-sm"
                  onclick="return confirm('Hapus file ini?')">
                  <i class="ti ti-trash"></i> Hapus
                </button>

              </form>

            </td>

          </tr>
          @endforeach

        </tbody>
      </table>

    </div>
    @endif

    <div class="mt-4 d-flex gap-2">
 

    <a
        href="{{ route('form.step', 'step=5') }}"
        class="btn btn-outline-primary"        
        title="Lanjut ke Formulir 5"
    >
        <i class="ti ti-arrow-right me-1"></i>
        Ke Form 5
    </a>
</div>

  </div>
</div>

<script>
  document.addEventListener("DOMContentLoaded", function() {
    const submitBtn = document.getElementById("submitBtn");

    function checkFormValidity() {
      const inputs = document.querySelectorAll('input[type="file"]');
      let allValid = true;

      inputs.forEach(input => {
        // Jika input kosong atau ada pesan error aktif di bawahnya, maka form belum valid
        const errorMsg = document.getElementById(input.id.replace('Input', 'Error'));
        if (input.files.length === 0 || (errorMsg && errorMsg.innerText !== "")) {
          allValid = false;
        }
      });

      submitBtn.disabled = !allValid;
    }

    function initDrop(boxId, inputId, previewId, errorId) {
      const box = document.getElementById(boxId);
      const input = document.getElementById(inputId);
      const preview = document.getElementById(previewId);
      const errorMsg = document.getElementById(errorId);

      function validateAndShow(file) {
        errorMsg.innerText = "";

        // Validasi PDF & Size
        if (file.type !== "application/pdf") {
          errorMsg.innerText = "Format harus PDF.";
          input.value = "";
          preview.innerHTML = "";
        } else if (file.size > 50 * 1024 * 1024) {
          errorMsg.innerText = "Maksimal 50MB.";
          input.value = "";
          preview.innerHTML = "";
        } else {
          // Jika valid, tampilkan preview
          preview.innerHTML = `
                    <div class="file-row">
                        <i class="ti ti-file"></i>
                        <span>${file.name}</span>
                        <i class="ti ti-trash remove-file"></i>
                    </div>
                `;
          preview.querySelector(".remove-file").onclick = function() {
            input.value = "";
            preview.innerHTML = "";
            errorMsg.innerText = "";
            checkFormValidity();
          };
        }
        checkFormValidity();
      }

      input.addEventListener("change", function() {
        if (this.files.length) validateAndShow(this.files[0]);
      });

      box.addEventListener("dragover", (e) => {
        e.preventDefault();
        box.classList.add("dragover");
      });
      box.addEventListener("dragleave", () => box.classList.remove("dragover"));
      box.addEventListener("drop", function(e) {
        e.preventDefault();
        box.classList.remove("dragover");
        if (e.dataTransfer.files.length) {
          input.files = e.dataTransfer.files;
          validateAndShow(e.dataTransfer.files[0]);
        }
      });
    }

    // Inisialisasi jika elemen ada di DOM
    if (document.getElementById("cvBox")) initDrop("cvBox", "cvInput", "cvPreview", "cvError");
    if (document.getElementById("pernyataanBox")) initDrop("pernyataanBox", "pernyataanInput", "pernyataanPreview", "pernyataanError");
  });
</script>

<style>
  .upload-box {
    border: 2px dashed #dadde1;
    border-radius: 8px;
    padding: 35px 15px;
    text-align: center;
    transition: 0.2s;
    cursor: pointer;
  }

  .upload-box.dragover {
    border-color: #206bc4;
    background: #f0f6ff;
  }

  .upload-content {
    display: flex;
    flex-direction: column;
    align-items: center;
  }

  .file-row {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
  }

  .remove-file {
    cursor: pointer;
    color: #d63939;
  }
</style>