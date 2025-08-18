@extends('layouts.app')
@section('content')
<h1 class="text-xl font-semibold mb-4">Thêm môn học</h1>
<form method="post" action="{{ route('subjects.store') }}" class="space-y-3">@csrf
  <input name="name" class="border p-2 rounded w-full" placeholder="Tên môn" required>
  <button class="px-4 py-2 bg-blue-600 text-white rounded">Lưu</button>
</form>
@endsection
