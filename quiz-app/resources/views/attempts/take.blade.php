@extends('layouts.app')

@section('content')
<h1 class="text-xl font-semibold mb-2">{{ $exam->title }}</h1>
<p class="mb-4 text-gray-700">
  Thời gian: <b>{{ $exam->duration_minutes }} phút</b>
  · Hết giờ sau: <span id="timer" class="font-semibold">--:--</span>
</p>

@if($answers->isEmpty())
  <div class="bg-yellow-100 text-yellow-900 p-3 rounded border">
    Bài thi chưa có câu hỏi. Vui lòng liên hệ giáo viên.
  </div>
@else
<form id="examForm" method="post" action="{{ route('attempts.submit', $attempt) }}" class="space-y-4">
  @csrf

  @foreach($answers as $idx => $ans)
    @php $q = $ans->question; @endphp
    <div class="bg-white border rounded p-4">
      <div class="font-medium mb-2">{{ $idx + 1 }}. {!! nl2br(e($q->content)) !!}</div>

      {{-- Ảnh minh hoạ câu hỏi (nếu có) --}}
      @if(!empty($q->image_url))
        <div class="mt-2">
          <img src="{{ $q->image_url }}"
               alt="Ảnh minh hoạ câu hỏi #{{ $q->id }}"
               class="max-h-72 w-auto rounded border object-contain">
        </div>
      @endif

      <div class="mt-3 space-y-1">
        @foreach($q->options as $op)
          <label class="block">
            <input type="radio" name="answers[{{ $q->id }}]" value="{{ $op->id }}" class="mr-1">
            {{ $op->text }}
          </label>
        @endforeach
      </div>

      <div class="text-xs text-gray-500 mt-2">
        Môn: {{ $q->subject->name ?? '—' }}
        @if(!empty($q->topic)) · Chủ đề: {{ $q->topic }} @endif
        · {{ strtoupper($q->difficulty) }} · {{ $q->points }}đ
      </div>
    </div>
  @endforeach

  <div class="flex gap-2">
    <button class="px-4 py-2 bg-blue-600 text-white rounded">Nộp bài</button>
    <a href="{{ route('exams.catalog') }}" class="px-3 py-2">Thoát</a>
  </div>
</form>
@endif

{{-- Đồng hồ đếm: dùng mốc thời gian server nếu controller có truyền $serverNow --}}
<script>
  const endAtMs   = {{ $endAt }} * 1000;
  const serverNow = {{ isset($serverNow) ? $serverNow : 0 }} * 1000; // 0 = không có
  let left = Math.max(0, endAtMs - (serverNow > 0 ? serverNow : Date.now()));

  const timerEl = document.getElementById('timer');
  const form    = document.getElementById('examForm');

  function fmt(ms){
    const s = Math.floor(ms/1000), m = Math.floor(s/60), r = s%60;
    return `${String(m).padStart(2,'0')}:${String(r).padStart(2,'0')}`;
  }

  timerEl.textContent = fmt(left);
  const iv = setInterval(()=>{
    left -= 1000;
    if (left <= 0) { clearInterval(iv); form.submit(); }
    else { timerEl.textContent = fmt(left); }
  }, 1000);
</script>
@endsection
