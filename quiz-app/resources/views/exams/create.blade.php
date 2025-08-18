@extends('layouts.app')
@section('content')
<h1 class="text-xl font-semibold mb-4">Tạo đề thi</h1>
<form method="post" action="{{ route('exams.store') }}" class="space-y-3">
  @csrf
  <div>
    <label class="block mb-1 font-medium">Tiêu đề</label>
    <input name="title" class="border p-2 rounded w-full" value="{{ old('title') }}">
  </div>
  <div class="grid md:grid-cols-2 gap-3">
    <div>
      <label class="block mb-1 font-medium">Môn học</label>
      <select name="subject_id" class="border p-2 rounded w-full">
        <option value="">—</option>
        @foreach($subjects as $s)
          <option value="{{ $s->id }}">{{ $s->name }}</option>
        @endforeach
      </select>
    </div>
    <div>
      <label class="block mb-1 font-medium">Thời lượng (phút)</label>
      <input type="number" name="duration_minutes" min="5" value="{{ old('duration_minutes',30) }}" class="border p-2 rounded w-full">
    </div>
  </div>
  <button class="px-4 py-2 bg-blue-600 text-white rounded">Tạo & tiếp tục</button>
</form>
@endsection
