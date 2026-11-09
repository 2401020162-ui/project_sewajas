@extends('layouts.app')

@section('title', 'Masuk atau Daftar — PW_SEWAJAS')

@section('isi')
@php
    $tab = request('tab') === 'daftar' || old('nomor_hp') || old('password_confirmation') ? 'daftar' : 'masuk';
@endphp

<section class="auth-section">
    <div class="container">
        <div class="auth-card">
            <div class="auth-visual">
                <span class="eyebrow eyebrow-light">PW_SEWAJAS</span>
                <h2>Tampil percaya diri di setiap momen spesial.</h2>
                <p>Masuk untuk menyewa jas, memantau pesanan, dan mengunggah bukti pembayaran dengan mudah.</p>
                <ul class="auth-benefits">
                    <li>Koleksi jas bersih dan siap pakai</li>
                    <li>Pesan online, ambil di toko</li>
                    <li>Pembayaran transfer, QRIS, atau cash</li>
                </ul>
            </div>

            <div class="auth-form-panel">
                <div class="tabs" role="tablist" aria-label="Pilih masuk atau daftar">
                    <button type="button" role="tab" data-tab="masuk" class="{{ $tab === 'masuk' ? 'active' : '' }}" aria-selected="{{ $tab === 'masuk' ? 'true' : 'false' }}">Masuk</button>
                    <button type="button" role="tab" data-tab="daftar" class="{{ $tab === 'daftar' ? 'active' : '' }}" aria-selected="{{ $tab === 'daftar' ? 'true' : 'false' }}">Daftar</button>
                </div>

                <div class="tab-panel {{ $tab === 'masuk' ? 'active' : '' }}" id="tab-masuk" role="tabpanel">
                    <h1>Selamat datang kembali</h1>
                    <p class="muted">Masuk dengan akun customer atau admin.</p>

                    <form method="POST" action="{{ route('login.proses') }}" class="form">
                        @csrf
                        <div class="field">
                            <label for="login-email">Email</label>
                            <input id="login-email" type="email" name="email" value="{{ old('email') }}" autocomplete="email" placeholder="nama@email.com" required autofocus>
                        </div>
                        <div class="field">
                            <label for="login-password">Kata sandi</label>
                            <input id="login-password" type="password" name="password" autocomplete="current-password" placeholder="Masukkan kata sandi" required>
                        </div>
                        <label class="checkbox">
                            <input type="checkbox" name="remember" value="1" @checked(old('remember'))>
                            <span>Ingat saya</span>
                        </label>
                        <button class="btn btn-primary btn-block" type="submit">Masuk</button>
                    </form>

                    <p class="auth-switch">Belum punya akun? <button type="button" class="link-button" data-tab-target="daftar">Daftar sekarang</button></p>
                </div>

                <div class="tab-panel {{ $tab === 'daftar' ? 'active' : '' }}" id="tab-daftar" role="tabpanel">
                    <h1>Buat akun baru</h1>
                    <p class="muted">Gratis, cepat, dan hanya butuh satu menit.</p>

                    <form method="POST" action="{{ route('register.proses') }}" class="form">
                        @csrf
                        <div class="field">
                            <label for="register-name">Nama lengkap</label>
                            <input id="register-name" name="name" value="{{ old('name') }}" autocomplete="name" placeholder="Nama lengkap" required>
                        </div>
                        <div class="field">
                            <label for="register-email">Email</label>
                            <input id="register-email" type="email" name="email" value="{{ old('email') }}" autocomplete="email" placeholder="nama@email.com" required>
                        </div>
                        <div class="field">
                            <label for="register-phone">Nomor HP</label>
                            <input id="register-phone" name="nomor_hp" value="{{ old('nomor_hp') }}" inputmode="numeric" autocomplete="tel" placeholder="08xxxxxxxxxx" required>
                        </div>
                        <div class="field-row">
                            <div class="field">
                                <label for="register-password">Kata sandi</label>
                                <input id="register-password" type="password" name="password" autocomplete="new-password" placeholder="Minimal 8 karakter" required>
                            </div>
                            <div class="field">
                                <label for="register-password-confirmation">Ulangi kata sandi</label>
                                <input id="register-password-confirmation" type="password" name="password_confirmation" autocomplete="new-password" placeholder="Ulangi kata sandi" required>
                            </div>
                        </div>
                        <button class="btn btn-primary btn-block" type="submit">Daftar</button>
                    </form>

                    <p class="auth-switch">Sudah punya akun? <button type="button" class="link-button" data-tab-target="masuk">Masuk di sini</button></p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    (() => {
        const tabs = document.querySelectorAll('.tabs [data-tab]');
        const panels = document.querySelectorAll('.tab-panel');

        const activate = (name) => {
            tabs.forEach((tab) => {
                const active = tab.dataset.tab === name;
                tab.classList.toggle('active', active);
                tab.setAttribute('aria-selected', active ? 'true' : 'false');
            });
            panels.forEach((panel) => panel.classList.toggle('active', panel.id === 'tab-' + name));
        };

        tabs.forEach((tab) => tab.addEventListener('click', () => activate(tab.dataset.tab)));
        document.querySelectorAll('[data-tab-target]').forEach((button) => {
            button.addEventListener('click', () => activate(button.dataset.tabTarget));
        });
    })();
</script>
@endpush
