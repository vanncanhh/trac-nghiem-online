@extends('layouts.app')
@section('content')
<h1 class="text-xl font-semibold mb-2">{{ $attempt->exam->title }}</h1>
<div class="mb-4">Điểm: <b>{{ $attempt->score }}/{{ $attempt->max_score }}</b></div>

<div class="space-y-3">
@foreach($attempt->answers as $idx=>$ans)
  @php
    $q = $ans->question;
    $correctId = $q->options->firstWhere('is_correct',true)?->id;
  @endphp
  <div class="bg-white border rounded p-3">
    <div class="font-medium mb-1">{{ $idx+1 }}. {{ $q->content }}</div>
    <div class="text-sm text-gray-500 mb-2">Chủ đề: {{ $q->topic ?? '—' }} · {{ strtoupper($q->difficulty) }} · {{ $q->points }}đ</div>
    @foreach($q->options as $op)
      @php
        $isChosen = $ans->selected_option_id === $op->id;
        $isCorrect = $op->id === $correctId;
      @endphp
      <div class="px-2 py-1 rounded
        {{ $isCorrect ? 'bg-green-100' : ($isChosen ? 'bg-red-100' : 'bg-gray-50') }}">
        @if($isChosen) <b>Đã chọn:</b> @endif
        {{ $op->text }}
        @if($isCorrect) <span class="text-green-700"> (Đáp án đúng)</span> @endif
      </div>
    @endforeach
  </div>
@endforeach
</div>

<a href="{{ route('attempts.index') }}" class="inline-block mt-4 text-blue-600">← Quay lại kết quả</a>
@endsection
