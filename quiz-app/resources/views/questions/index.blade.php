@extends('layouts.app')

@section('content')
<div class="flex justify-between items-center mb-4">
  <h1 class="text-xl font-semibold">Ngân hàng câu hỏi</h1>
  <a href="{{ route('questions.create') }}" class="px-3 py-2 bg-blue-600 text-white rounded">+ Thêm câu hỏi</a>
</div>

<form method="get" class="mb-4 grid md:grid-cols-5 gap-3">
  <input name="kw" value="{{ $filters['kw'] ?? '' }}" placeholder="Tìm nội dung..." class="border p-2 rounded w-full">

  {{-- chọn CHỦ ĐỀ theo topic_id --}}
  <select name="topic_id" class="border p-2 rounded">
    <option value="">-- Chủ đề --</option>
    @foreach($topics as $t)
      <option value="{{ $t->id }}" @selected(($filters['topic_id'] ?? '') == $t->id)>{{ $t->name }}</option>
    @endforeach
  </select>

  <select name="subject_id" class="border p-2 rounded">
    <option value="">-- Môn học --</option>
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

  <button class="bg-gray-800 text-white rounded px-3">Lọc</button>
</form>

@if($qs->count() === 0)
  <div class="text-gray-600">Chưa có câu hỏi nào.</div>
@else
  <div class="overflow-hidden border rounded">
    <table class="min-w-full bg-white">
      <thead class="bg-gray-100 text-left">
        <tr>
          <th class="px-3 py-2 w-16">ID</th>
          <th class="px-3 py-2">Nội dung</th>
          <th class="px-3 py-2 w-40">Môn học</th>
          <th class="px-3 py-2 w-40">Chủ đề</th>   {{-- đưa Chủ đề lên trước / tuỳ bạn --}}
          <th class="px-3 py-2 w-28">Độ khó</th>
          <th class="px-3 py-2 w-20 text-center">Điểm</th>
          <th class="px-3 py-2 w-40">Nguồn</th>
          <th class="px-3 py-2 w-40 text-right">Thao tác</th>
        </tr>
      </thead>
      <tbody class="divide-y">
        @foreach($qs as $q)
          <tr>
            <td class="px-3 py-2 text-gray-600">#{{ $q->id }}</td>
            <td class="px-3 py-2">{{ \Illuminate\Support\Str::limit($q->content, 120) }}</td>
            <td class="px-3 py-2">{{ $q->subject->name ?? '—' }}</td>
            <td class="px-3 py-2">{{ $q->topicRef->name ?? '—' }}</td> {{-- lấy theo quan hệ --}}
            <td class="px-3 py-2">{{ strtoupper($q->difficulty) }}</td>
            <td class="px-3 py-2 text-center">{{ $q->points }}</td>
            <td class="px-3 py-2">{{ $q->source ?? '—' }}</td>
            <td class="px-3 py-2">
              <div class="flex justify-end gap-2">
                <a href="{{ route('questions.edit',$q) }}" class="px-2 py-1 bg-yellow-500 text-white rounded">Sửa</a>
                <form method="post" action="{{ route('questions.destroy',$q) }}" onsubmit="return confirm('Xoá câu hỏi này?')">
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

  <div class="mt-4">{{ $qs->links() }}</div>
@endif
@endsection
