@extends('layouts.app')
@section('content')
<h1 class="text-xl font-semibold mb-4">Đăng ký</h1>
<form method="post" action="/register" class="space-y-3">@csrf
  <input name="name" class="border p-2 w-full" placeholder="Tên">
  <input name="email" class="border p-2 w-full" placeholder="Email">
  <input name="password" type="password" class="border p-2 w-full" placeholder="Mật khẩu">
  <input name="password_confirmation" type="password" class="border p-2 w-full" placeholder="Nhập lại mật khẩu">
  <select name="role" class="border p-2 w-full">
    <option value="student">student</option>
    <option value="teacher">teacher</option>
  </select>
  <button class="bg-blue-600 text-white px-3 py-2 rounded">Đăng ký</button>
  <a href="/login" class="px-3 py-2 rounded border">Đăng nhập</a>
</form>
@endsection
