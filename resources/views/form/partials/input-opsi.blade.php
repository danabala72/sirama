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
                    placeholder="A, AB, B, BC, C, D, E"
                    maxlength="2"
                    pattern="^(A|AB|B|BC|C|D|E)$"
                    oninput="this.value = this.value.toUpperCase(); this.setCustomValidity('');"
                    oninvalid="this.setCustomValidity('Hanya masukkan nilai yang valid: A, AB, B, BC, C, D, atau E')"
                    style="text-transform: uppercase;">
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
                    max="100"
                    step="0.01">
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

        // Karakter pertama hanya boleh A, B, C, D, atau E
        if (v.length >= 1) {
            first = v[0].match(/[A-E]/) ? v[0] : '';
        }

        // Karakter kedua hanya boleh B (jika pertamanya A) atau C (jika pertamanya B)
        if (v.length >= 2 && first !== '') {
            if (first === 'A' && v[1] === 'B') {
                second = 'B';
            } else if (first === 'B' && v[1] === 'C') {
                second = 'C';
            } else {
                second = ''; // Hapus jika mengetik karakter selain itu
            }
        }

        e.target.value = first + second;
    });
</script>