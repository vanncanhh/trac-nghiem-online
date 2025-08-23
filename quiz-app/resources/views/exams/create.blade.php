@extends('layouts.app')

@section('content')
<h1 class="text-xl font-semibold mb-4">Tạo đề thi</h1>

<form method="post" action="{{ route('exams.store') }}" class="space-y-4 max-w-3xl">
  @csrf

  <div class="grid md:grid-cols-2 gap-3">
    <div>
      <label class="block mb-1 font-medium">Tiêu đề</label>
      <input name="title" value="{{ old('title') }}" class="border p-2 rounded w-full" required>
    </div>

    <div>
      <label class="block mb-1 font-medium">Lớp</label>
      <select name="classroom_id" class="border p-2 rounded w-full" required>
        @foreach($classrooms as $c)
          <option value="{{ $c->id }}" @selected(old('classroom_id')==$c->id)>{{ $c->name }}</option>
        @endforeach
      </select>
    </div>

    <div>
      <label class="block mb-1 font-medium">Môn học (tuỳ chọn)</label>
      <select name="subject_id" class="border p-2 rounded w-full">
        <option value="">—</option>
        @foreach($subjects as $s)
          <option value="{{ $s->id }}" @selected(old('subject_id')==$s->id)>{{ $s->name }}</option>
        @endforeach
      </select>
    </div>

    <div>
      <label class="block mb-1 font-medium">Thời lượng (phút)</label>
      <input type="number" min="5" name="duration_minutes" value="{{ old('duration_minutes',45) }}" class="border p-2 rounded w-full" required>
    </div>
  </div>

  <label class="inline-flex gap-2 items-center">
    <input type="checkbox" name="is_public" value="1" @checked(old('is_public'))>
    Công khai (student có thể thấy trên Catalog)
  </label>

  <div class="flex gap-2">
    <button class="px-4 py-2 bg-blue-600 text-white rounded">Tạo & tiếp tục</button>
    <a href="{{ route('exams.index') }}" class="px-3 py-2">Huỷ</a>
  </div>
</form>
@endsection
