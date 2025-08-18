@extends('layouts.app')
@section('content')
<h1 class="text-xl font-semibold mb-4">Sửa câu hỏi #{{ $question->id }}</h1>
<form method="post" action="{{ route('questions.update',$question) }}" class="space-y-3" enctype="multipart/form-data">
  @csrf @method('PUT')
  @include('questions._form')
  <button class="px-4 py-2 bg-blue-600 text-white rounded">Cập nhật</button>
  <a href="{{ route('questions.index') }}" class="px-3 py-2">Quay lại</a>
</form>
@endsection
