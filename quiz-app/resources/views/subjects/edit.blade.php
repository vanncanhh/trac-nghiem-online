@extends('layouts.app')

@section('content')
<h1 class="text-xl font-semibold mb-4">Sửa môn học</h1>

<form method="post" action="{{ route('subjects.update', $subject) }}" class="space-y-4 max-w-xl">
  @csrf
  @method('PUT')

  <div>
    <label class="block mb-1 font-medium">Tên môn học</label>
    <input name="name"
           value="{{ old('name', $subject->name) }}"
           class="border p-2 rounded w-full"
           required>
  </div>

  <div class="flex gap-2">
    <button class="px-4 py-2 bg-blue-600 text-white rounded">Cập nhật</button>
    <a href="{{ route('subjects.index') }}" class="px-3 py-2">Quay lại</a>
  </div>
</form>
@endsection
