<div id="input-opsi"
    class="col-md-12"
    @if(!$errors->any() && !isset($editMk)) style="display:none;" @endif>

    <fieldset class="form-fieldset bg-light border-dashed">
        <div class="row g-3">

            <!-- Bukti Pendukung -->
            <div class="col-12">
                <label class="form-label">Bukti Pendukung</label>

                <select required name="attachment_ids[]" id="attachment-select" class="form-select" multiple>
                    @foreach($attachment as $file)
                    <option value="{{ $file->id }}"
                        data-label="{{ strtoupper(str_replace('_', ' ', $file->label)) }}"
                        @if(isset($editMk) && $editMk->attachment->contains($file->id)) selected @endif>
                        {{ $file->file_name }}
                    </option>
                    @endforeach
                </select>

            </div>


            <!-- SKS -->
            <div class="col-md-2">
                <label class="form-label">SKS Asal</label>
                <div class="input-icon">
                    <input type="number" required min="1" max="20" name="sks" id="in-sks" class="form-control text-center" placeholder="Jumlah SKS Asal">
                </div>
            </div>


            <!-- Nilai Huruf -->
            <div class="col-md-2">
                <label class="form-label">Nilai Huruf</label>
                <input type="text"
                    required
                    name="nilai_huruf"
                    id="nilai_huruf"
                    value="{{ $editMk->nilai_huruf ?? '' }}"
                    class="form-control text-center"
                    placeholder="Misal A, B, C dan lain-lain"
                    maxlength="2"
                    pattern="^[A-Ea-e][+-]?$"
                    oninput="this.value = this.value.toUpperCase()"
                    title="Masukkan format nilai yang valid (A, B, C, A+, B-, dll)">

            </div>


            <!-- Nilai Angka -->
            <div class="col-md-2">
                <label class="form-label">Nilai Angka</label>

                <input type="number"
                    required
                    name="nilai_angka"
                    value="{{ $editMk->nilai_angka ?? '' }}"
                    class="form-control text-center"
                    placeholder="Nilai Mata Kuliah Asal"
                    min="0"
                    max="100">
            </div>


            <!-- Button -->
            <div class="col-md-2 d-flex align-items-end">

                <button type="submit" class="btn btn-success w-100">
                    <i class="ti ti-device-floppy me-2"></i>

                    {{ isset($editMk) ? 'Update' : 'Simpan' }}

                </button>

            </div>

        </div>
    </fieldset>
</div>

<script>
    document.getElementById('nilai_huruf').addEventListener('input', function(e) {
        let v = e.target.value.toUpperCase();

        let first = '';
        let second = '';

        // Ambil huruf pertama hanya A-E
        if (v.length >= 1) {
            first = v[0].match(/[A-E]/) ? v[0] : '';
        }

        // Ambil karakter kedua hanya + atau -
        if (v.length >= 2) {
            second = v[1].match(/[+-]/) ? v[1] : '';
        }

        e.target.value = first + second;
    });
</script>