@extends('layouts.app')
@section('content')
<div class="flex justify-between items-center mb-4">
  <h1 class="text-xl font-semibold">Thi trực tuyến</h1>
  <a href="{{ route('attempts.index') }}" class="text-blue-600">Kết quả của tôi</a>
</div>

<form method="get" class="mb-4 grid md:grid-cols-3 gap-3">
  {{-- Chọn LỚP trước --}}
  <select name="classroom_id" class="border p-2 rounded" onchange="this.form.submit()">
    <option value="">-- Chọn lớp --</option>
    @foreach($classrooms as $c)
      <option value="{{ $c->id }}" @selected(($filters['classroom_id'] ?? '')==$c->id)>{{ $c->name }}</option>
    @endforeach
  </select>

  {{-- Sau khi đã chọn lớp, mới cho lọc theo Môn --}}
  <select name="subject_id" class="border p-2 rounded" @disabled(empty($filters['classroom_id']))>
    <option value="">-- Môn học (tuỳ chọn) --</option>
    @foreach($subjects as $s)
      <option value="{{ $s->id }}" @selected(($filters['subject_id'] ?? '')==$s->id)>{{ $s->name }}</option>
    @endforeach
  </select>

  <button class="bg-gray-800 text-white rounded px-3">Lọc</button>
</form>

@if(empty($filters['classroom_id']))
  <div class="bg-yellow-50 border-l-4 border-yellow-400 p-3 text-yellow-900 rounded">
    Vui lòng chọn <b>Lớp</b> để xem danh sách đề thi.
  </div>
@else
  @if($exams->count()===0)
    <div class="text-gray-600">Chưa có đề thi nào cho lớp này.</div>
  @else
    <div class="grid md:grid-cols-2 gap-3">
      @foreach($exams as $e)
        <div class="bg-white border rounded p-4 flex justify-between">
          <div>
            <div class="font-medium">{{ $e->title }}</div>
            <div class="text-sm text-gray-600">
              Lớp: {{ $e->classroom->name ?? '—' }} · {{ $e->subject->name ?? '—' }} · {{ $e->duration_minutes }} phút
            </div>
          </div>
          <a href="{{ route('attempts.start',$e) }}" class="px-3 py-2 bg-green-600 text-white rounded self-start">Bắt đầu</a>
        </div>
      @endforeach
    </div>
    <div class="mt-4">{{ $exams->links() }}</div>
  @endif
@endif
@endsection
