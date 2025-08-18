@extends('layouts.app')

@section('content')
<h1 class="text-xl font-semibold mb-4">Đăng nhập</h1>

<form method="post" action="/login" class="space-y-3">
  @csrf
  <input name="email" class="border p-2 w-full" placeholder="Email">
  <input name="password" type="password" class="border p-2 w-full" placeholder="Mật khẩu">
  <label class="inline-flex items-center gap-2">
    <input type="checkbox" name="remember"> Ghi nhớ
  </label>

  <div class="flex items-center gap-3">
    <button type="submit" class="bg-blue-600 text-white px-3 py-2 rounded">
      Đăng nhập
    </button>

    <a href="/register" class="px-3 py-2 rounded border">Đăng ký</a>
  </div>
</form>
@endsection
