@extends('layouts.app')
@section('content')
<h1 class="text-xl font-semibold mb-4">Kết quả của tôi</h1>

<div class="space-y-3">
@forelse($attempts as $a)
  <div class="bg-white border rounded p-3 flex justify-between">
    <div>
      <div class="font-medium">{{ $a->exam->title }}</div>
      <div class="text-sm text-gray-600">
        Bắt đầu: {{ $a->started_at }} · Nộp: {{ $a->submitted_at ? $a->submitted_at : '—' }}
      </div>
    </div>
    <div class="text-right">
      <div class="font-semibold">{{ $a->score }}/{{ $a->max_score }} điểm</div>
      @if($a->submitted_at)
        <a class="text-blue-600" href="{{ route('attempts.show',$a) }}">Xem chi tiết</a>
      @else
        <a class="px-2 py-1 bg-green-600 text-white rounded" href="{{ route('attempts.start',$a->exam) }}">Tiếp tục</a>
      @endif
    </div>
  </div>
@empty
  <div class="text-gray-600">Chưa có bài thi nào.</div>
@endforelse
</div>
@endsection
