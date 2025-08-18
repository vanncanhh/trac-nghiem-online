@extends('layouts.app')
@section('content')
<h1 class="text-xl font-semibold mb-4">Thêm câu hỏi</h1>
<form method="post" action="{{ route('questions.store') }}" class="space-y-3" enctype="multipart/form-data">
  @csrf
  @include('questions._form')
  <button class="px-4 py-2 bg-blue-600 text-white rounded">Lưu</button>
  <a href="{{ route('questions.index') }}" class="px-3 py-2">Huỷ</a>
</form>
@endsection
