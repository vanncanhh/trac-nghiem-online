@extends('layouts.app')
@section('content')
<div class="flex justify-between items-center mb-4">
  <h1 class="text-xl font-semibold">Thi trực tuyến</h1>
  <a href="{{ route('attempts.index') }}" class="text-blue-600">Kết quả của tôi</a>
</div>

<form method="get" class="mb-4 flex gap-3">
  <select name="subject_id" class="border p-2 rounded">
    <option value="">-- Môn học --</option>
    @foreach($subjects as $s)
      <option value="{{ $s->id }}" @selected(($filters['subject_id']??'')==$s->id)>{{ $s->name }}</option>
    @endforeach
  </select>
  <button class="bg-gray-800 text-white rounded px-3">Lọc</button>
</form>

<div class="grid md:grid-cols-2 gap-3">
@foreach($exams as $e)
  <div class="bg-white border rounded p-4 flex justify-between">
    <div>
      <div class="font-medium">{{ $e->title }}</div>
      <div class="text-sm text-gray-600">{{ $e->subject->name ?? '—' }} · {{ $e->duration_minutes }} phút</div>
    </div>
    <a href="{{ route('attempts.start',$e) }}" class="px-3 py-2 bg-green-600 text-white rounded self-start">Bắt đầu</a>
  </div>
@endforeach
</div>

<div class="mt-4">{{ $exams->links() }}</div>
@endsection
