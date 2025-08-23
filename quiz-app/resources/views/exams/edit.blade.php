@extends('layouts.app')

@section('content')
<h1 class="text-xl font-semibold mb-4">Sửa đề thi: {{ $exam->title }}</h1>

{{-- Form thuộc tính đề --}}
<form method="post" action="{{ route('exams.update',$exam) }}" class="space-y-4 mb-6 border rounded p-4">
  @csrf @method('PUT')

  <div class="grid md:grid-cols-2 gap-3">
    <div>
      <label class="block mb-1 font-medium">Tiêu đề</label>
      <input name="title" value="{{ old('title',$exam->title) }}" class="border p-2 rounded w-full" required>
    </div>

    <div>
      <label class="block mb-1 font-medium">Lớp</label>
      <select name="classroom_id" class="border p-2 rounded w-full" required>
        @foreach($classrooms as $c)
          <option value="{{ $c->id }}" @selected(old('classroom_id',$exam->classroom_id)==$c->id)>{{ $c->name }}</option>
        @endforeach
      </select>
    </div>

    <div>
      <label class="block mb-1 font-medium">Môn học (tuỳ chọn)</label>
      <select name="subject_id" class="border p-2 rounded w-full">
        <option value="">—</option>
        @foreach($subjects as $s)
          <option value="{{ $s->id }}" @selected(old('subject_id',$exam->subject_id)==$s->id)>{{ $s->name }}</option>
        @endforeach
      </select>
    </div>

    <div>
      <label class="block mb-1 font-medium">Thời lượng (phút)</label>
      <input type="number" min="5" name="duration_minutes" value="{{ old('duration_minutes',$exam->duration_minutes) }}" class="border p-2 rounded w-full" required>
    </div>
  </div>

  <label class="inline-flex gap-2 items-center">
    <input type="checkbox" name="is_public" value="1" @checked(old('is_public',$exam->is_public))>
    Công khai (student thấy trên Catalog)
  </label>

  <div class="flex gap-2">
    <button class="px-4 py-2 bg-blue-600 text-white rounded">Lưu thuộc tính</button>
    @unless($exam->is_public)
      <form method="post" action="{{ route('exams.publish',$exam) }}">
        @csrf
        <button class="px-4 py-2 bg-emerald-600 text-white rounded">Public ngay</button>
      </form>
    @endunless
    <a href="{{ route('exams.index') }}" class="px-3 py-2">Quay lại</a>
  </div>
</form>

{{-- Bộ lọc câu hỏi --}}
<form method="get" class="mb-4 grid md:grid-cols-4 gap-3">
  <input type="hidden" name="tab" value="qs">
  <select name="subject_id" class="border p-2 rounded">
    <option value="">-- Lọc theo môn --</option>
    @foreach($subjects as $s)
      <option value="{{ $s->id }}" @selected(($filters['subject_id'] ?? '') == $s->id)>{{ $s->name }}</option>
    @endforeach
  </select>
  <select name="difficulty" class="border p-2 rounded">
    <option value="">-- Độ khó --</option>
    @foreach(['easy'=>'Dễ','med'=>'Trung bình','hard'=>'Khó'] as $k=>$v)
      <option value="{{ $k }}" @selected(($filters['difficulty'] ?? '') == $k)>{{ $v }}</option>
    @endforeach
  </select>
  <button class="bg-gray-800 text-white rounded px-3">Lọc câu hỏi</button>
</form>

{{-- Danh sách câu hỏi để chọn --}}
<form method="post" action="{{ route('exams.update',$exam) }}" class="space-y-3">
  @csrf @method('PUT')

  {{-- giữ lại các thuộc tính hiện tại khi submit danh sách câu hỏi --}}
  <input type="hidden" name="title"            value="{{ $exam->title }}">
  <input type="hidden" name="classroom_id"     value="{{ $exam->classroom_id }}">
  <input type="hidden" name="subject_id"       value="{{ $exam->subject_id }}">
  <input type="hidden" name="duration_minutes" value="{{ $exam->duration_minutes }}">
  <input type="hidden" name="is_public"        value="{{ $exam->is_public ? 1 : 0 }}">

  @if($questions->count()===0)
    <div class="text-gray-600">Không có câu hỏi phù hợp bộ lọc.</div>
  @else
    <div class="overflow-hidden border rounded bg-white">
      <table class="min-w-full text-left">
        <thead class="bg-gray-100">
          <tr>
            <th class="px-3 py-2 w-10"></th>
            <th class="px-3 py-2">Nội dung</th>
            <th class="px-3 py-2 w-36">Môn</th>
            <th class="px-3 py-2 w-28">Độ khó</th>
            <th class="px-3 py-2 w-20 text-center">Điểm</th>
          </tr>
        </thead>
        <tbody class="divide-y">
          @foreach($questions as $q)
            <tr>
              <td class="px-3 py-2">
                <input type="checkbox" name="question_ids[]" value="{{ $q->id }}"
                       @checked(in_array($q->id,$selectedIds))>
              </td>
              <td class="px-3 py-2">{{ \Illuminate\Support\Str::limit($q->content, 120) }}</td>
              <td class="px-3 py-2">{{ $q->subject->name ?? '—' }}</td>
              <td class="px-3 py-2">{{ strtoupper($q->difficulty) }}</td>
              <td class="px-3 py-2 text-center">{{ $q->points }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <div class="mt-3">{{ $questions->links() }}</div>

    <div class="flex gap-2">
      <button class="px-4 py-2 bg-blue-600 text-white rounded">Lưu danh sách câu hỏi</button>
      <button form="autoGenForm" class="px-4 py-2 bg-gray-700 text-white rounded">Tạo tự động…</button>
    </div>
  @endif
</form>

{{-- Form tạo tự động theo tỉ lệ --}}
<form id="autoGenForm" method="post" action="{{ route('exams.auto',$exam) }}" class="mt-6 border rounded p-4 space-y-3">
  @csrf
  <div class="grid md:grid-cols-4 gap-3">
    <div>
      <label class="block mb-1 font-medium">Tổng số câu</label>
      <input type="number" name="total" min="1" value="20" class="border p-2 rounded w-full" required>
    </div>
    <div>
      <label class="block mb-1 font-medium">% Dễ</label>
      <input type="number" name="mix[easy]" min="0" max="100" value="30" class="border p-2 rounded w-full" required>
    </div>
    <div>
      <label class="block mb-1 font-medium">% Trung bình</label>
      <input type="number" name="mix[med]" min="0" max="100" value="50" class="border p-2 rounded w-full" required>
    </div>
    <div>
      <label class="block mb-1 font-medium">% Khó</label>
      <input type="number" name="mix[hard]" min="0" max="100" value="20" class="border p-2 rounded w-full" required>
    </div>
  </div>
  <div>
    <label class="block mb-1 font-medium">Giới hạn theo môn (tuỳ chọn)</label>
    <select name="subject_id" class="border p-2 rounded w-full">
      <option value="">—</option>
      @foreach($subjects as $s)
        <option value="{{ $s->id }}">{{ $s->name }}</option>
      @endforeach
    </select>
  </div>
</form>
@endsection
