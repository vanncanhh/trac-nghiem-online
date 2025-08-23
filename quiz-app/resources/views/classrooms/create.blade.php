@extends('layouts.app')

@section('content')
<h1 class="text-xl font-semibold mb-4">Thêm lớp</h1>

@if($errors->any())
  <div class="mb-3 px-3 py-2 bg-red-50 text-red-800 border border-red-200 rounded">
    {{ $errors->first() }}
  </div>
@endif

<form method="post" action="{{ route('classrooms.store') }}" class="space-y-4 max-w-xl">
  @csrf

  <div>
    <label class="block mb-1 font-medium">Tên lớp</label>
    <input name="name" value="{{ old('name') }}" class="border p-2 rounded w-full" placeholder="VD: 12A1 / CNTT-K45" required>
  </div>

  <div>
    <label class="block mb-1 font-medium">Ghi chú (tuỳ chọn)</label>
    <input name="note" value="{{ old('note') }}" class="border p-2 rounded w-full" placeholder="Ghi chú thêm...">
  </div>

  <div class="flex gap-2">
    <button class="px-4 py-2 bg-blue-600 text-white rounded">Lưu</button>
    <a href="{{ route('classrooms.index') }}" class="px-3 py-2">Quay lại</a>
  </div>
</form>
@endsection
