@extends('layouts.app')
@section('content')
<div class="flex justify-between items-center mb-4">
  <h1 class="text-xl font-semibold">Đề thi</h1>
  <a class="px-3 py-2 bg-blue-600 text-white rounded" href="{{ route('exams.create') }}">+ Tạo đề</a>
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

<div class="space-y-3">
@foreach($exams as $e)
  <div class="bg-white rounded shadow p-4 flex justify-between">
    <div>
      <div class="font-medium">{{ $e->title }}</div>
      <div class="text-sm text-gray-600">{{ $e->subject->name ?? '—' }} · {{ $e->duration_minutes }} phút · {{ $e->is_public ? 'Public' : 'Private' }}</div>
    </div>
    <div class="flex gap-2">
      @unless($e->is_public)
        <form method="post" action="{{ route('exams.publish',$e) }}">@csrf
          <button class="px-2 py-1 bg-green-600 text-white rounded">Public</button>
        </form>
      @endunless
      <a href="{{ route('exams.edit',$e) }}" class="px-2 py-1 bg-yellow-500 text-white rounded">Sửa</a>
      <form method="post" action="{{ route('exams.destroy',$e) }}" onsubmit="return confirm('Xoá đề?')">
        @csrf @method('DELETE')
        <button class="px-2 py-1 bg-red-600 text-white rounded">Xoá</button>
      </form>
    </div>
  </div>
@endforeach
</div>

<div class="mt-4">{{ $exams->links() }}</div>
@endsection
