<section>
    <header class="mb-4">
        <h3 class="card-title">
            {{ __('Ganti Kata Sandi') }}
        </h3>
        <p class="text-secondary small">
            {{ __('Pastikan akun Anda menggunakan kata sandi yang panjang dan acak agar tetap aman.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="space-y-3">
        @csrf
        @method('put')

        <!-- Kata Sandi Saat Ini -->
        <div class="mb-3">
            <label class="form-label" for="update_password_current_password">{{ __('Kata Sandi Saat Ini') }}</label>
            <input id="update_password_current_password" name="current_password" type="password" 
                class="form-control @error('current_password', 'updatePassword') is-invalid @enderror" 
                autocomplete="current-password" />
            @error('current_password', 'updatePassword')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Kata Sandi Baru -->
        <div class="mb-3">
            <label class="form-label" for="update_password_password">{{ __('Kata Sandi Baru') }}</label>
            <input id="update_password_password" name="password" type="password" 
                class="form-control @error('password', 'updatePassword') is-invalid @enderror" 
                autocomplete="new-password" />
            @error('password', 'updatePassword')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Konfirmasi Kata Sandi -->
        <div class="mb-3">
            <label class="form-label" for="update_password_password_confirmation">{{ __('Konfirmasi Kata Sandi') }}</label>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password" 
                class="form-control @error('password_confirmation', 'updatePassword') is-invalid @enderror" 
                autocomplete="new-password" />
            @error('password_confirmation', 'updatePassword')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Tombol Simpan & Status -->
        <div class="d-flex align-items-center gap-3">
            <button type="submit" class="btn btn-primary">
                {{ __('Simpan') }}
            </button>

            @if (session('status') === 'password-updated')
                <span 
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-success small"
                >
                    <i class="ti ti-check me-1"></i>{{ __('Berhasil disimpan.') }}
                </span>
            @endif
        </div>
    </form>
</section>
