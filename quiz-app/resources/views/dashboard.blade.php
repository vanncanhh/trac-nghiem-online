@extends('layouts.app')
@section('content')
<div class="grid md:grid-cols-3 gap-4">
  <a class="p-4 bg-white rounded shadow" href="{{ route('exams.index') }}">Danh sách đề thi</a>
  @if(auth()->user()->isAdmin() || auth()->user()->isTeacher())
    <a class="p-4 bg-white rounded shadow" href="{{ route('questions.index') }}">Ngân hàng câu hỏi</a>
    <a class="p-4 bg-white rounded shadow" href="{{ route('exams.create') }}">Tạo đề</a>
  @endif
  <a class="p-4 bg-white rounded shadow" href="{{ route('attempts.index') }}">Kết quả của tôi</a>
</div>
@endsection
