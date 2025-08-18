@extends('layouts.app')

@section('content')
<h1 class="text-xl font-semibold mb-4">Thêm chủ đề</h1>

<form method="post" action="{{ route('topics.store') }}" class="space-y-4 max-w-xl">
  @csrf

  <div>
    <label class="block mb-1 font-medium">Tên chủ đề</label>
    <input name="name"
           value="{{ old('name') }}"
           class="border p-2 rounded w-full"
           placeholder="VD: Đại số, Hình học, Tích phân..."
           required>
  </div>

  <div class="flex gap-2">
    <button class="px-4 py-2 bg-blue-600 text-white rounded">Lưu</button>
    <a href="{{ route('topics.index') }}" class="px-3 py-2">Quay lại</a>
  </div>
</form>
@endsection
