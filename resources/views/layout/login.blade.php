@extends('layout.auth')
@section('title', 'Peminjaman Barang - Masuk')
@section('form')
<form method="POST" action="{{ route('login') }}">
    @csrf
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Kata Sandi" required>
    <button type="submit">Masuk</button>
</form>
<div class="switch">
    <p>Belum punya akun? <a href="{{ route('register') }}">Daftar</a></p>
</div>
@endsection
