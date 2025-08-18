@extends('layouts.app')

@section('content')
<h1 class="text-xl font-semibold mb-4">Sửa chủ đề</h1>

<form method="post" action="{{ route('topics.update', $topic) }}" class="space-y-4 max-w-xl">
  @csrf
  @method('PUT')

  <div>
    <label class="block mb-1 font-medium">Tên chủ đề</label>
    <input name="name"
           value="{{ old('name', $topic->name) }}"
           class="border p-2 rounded w-full"
           required>
  </div>

  <div class="flex gap-2">
    <button class="px-4 py-2 bg-blue-600 text-white rounded">Cập nhật</button>
    <a href="{{ route('topics.index') }}" class="px-3 py-2">Quay lại</a>
  </div>
</form>
@endsection
