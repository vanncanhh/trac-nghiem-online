@extends('layouts.app')

@section('content')
<div class="flex justify-between items-center mb-4">
  <h1 class="text-xl font-semibold">Quản lý đề thi</h1>
  <a href="{{ route('exams.create') }}" class="px-3 py-2 bg-blue-600 text-white rounded">+ Tạo đề mới</a>
</div>

<form method="get" class="mb-4 grid md:grid-cols-3 gap-3">
  <select name="classroom_id" class="border p-2 rounded">
    <option value="">-- Lớp --</option>
    @foreach($classrooms as $c)
      <option value="{{ $c->id }}" @selected(($filters['classroom_id'] ?? '') == $c->id)>{{ $c->name }}</option>
    @endforeach
  </select>
  <select name="subject_id" class="border p-2 rounded">
    <option value="">-- Môn học --</option>
    @foreach($subjects as $s)
      <option value="{{ $s->id }}" @selected(($filters['subject_id'] ?? '') == $s->id)>{{ $s->name }}</option>
    @endforeach
  </select>
  <button class="bg-gray-800 text-white rounded px-3">Lọc</button>
</form>

@if($exams->count() === 0)
  <div class="text-gray-600">Chưa có đề thi.</div>
@else
  <div class="overflow-hidden border rounded">
    <table class="min-w-full bg-white text-left">
      <thead class="bg-gray-100">
        <tr>
          <th class="px-3 py-2">Tiêu đề</th>
          <th class="px-3 py-2 w-32">Lớp</th>
          <th class="px-3 py-2 w-40">Môn</th>
          <th class="px-3 py-2 w-28">Thời gian</th>
          <th class="px-3 py-2 w-24">Trạng thái</th>
          <th class="px-3 py-2 w-44 text-right">Thao tác</th>
        </tr>
      </thead>
      <tbody class="divide-y">
        @foreach($exams as $e)
          <tr>
            <td class="px-3 py-2">{{ $e->title }}</td>
            <td class="px-3 py-2">{{ $e->classroom->name ?? '—' }}</td>
            <td class="px-3 py-2">{{ $e->subject->name ?? '—' }}</td>
            <td class="px-3 py-2">{{ $e->duration_minutes }} phút</td>
            <td class="px-3 py-2">
              @if($e->is_public)
                <span class="px-2 py-0.5 text-xs bg-green-100 text-green-700 rounded">Public</span>
              @else
                <span class="px-2 py-0.5 text-xs bg-gray-100 text-gray-700 rounded">Private</span>
              @endif
            </td>
            <td class="px-3 py-2">
              <div class="flex justify-end gap-2">
                <a href="{{ route('exams.edit',$e) }}" class="px-2 py-1 bg-yellow-500 text-white rounded">Sửa</a>
                @unless($e->is_public)
                  <form method="post" action="{{ route('exams.publish',$e) }}">
                    @csrf
                    <button class="px-2 py-1 bg-blue-600 text-white rounded">Public</button>
                  </form>
                @endunless
                <form method="post" action="{{ route('exams.destroy',$e) }}" onsubmit="return confirm('Xoá đề này?')">
                  @csrf @method('DELETE')
                  <button class="px-2 py-1 bg-red-600 text-white rounded">Xoá</button>
                </form>
              </div>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <div class="mt-4">{{ $exams->links() }}</div>
@endif
@endsection
