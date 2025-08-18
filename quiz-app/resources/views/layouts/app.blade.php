<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>{{ $title ?? 'Quiz App' }}</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
<nav class="bg-white shadow mb-6">
  <div class="max-w-6xl mx-auto px-4 py-3 flex justify-between">
    <a href="/dashboard" class="font-semibold">Quiz App</a>
    @auth
      @php $u = auth()->user(); @endphp
      <div class="flex items-center gap-3">
        <span>{{ $u->name }} ({{ $u->role }})</span>

        {{-- Student: chỉ thấy Thi & Kết quả --}}
        @if($u->isStudent())
          <a href="{{ route('exams.catalog') }}" class="text-blue-600">Thi trực tuyến</a>
          <a href="{{ route('attempts.index') }}" class="text-blue-600">Kết quả</a>
        @endif

        {{-- Teacher: quản lý ngân hàng & đề thi (và có thể cũng xem catalog) --}}
        @if($u->isTeacher())
          <a href="{{ route('questions.index') }}" class="text-blue-600">Ngân hàng câu hỏi</a>
          <a href="{{ route('exams.index') }}" class="text-blue-600">Đề thi</a>
          <a href="{{ route('exams.catalog') }}" class="text-blue-600">Thi (xem)</a>
          <a href="{{ route('subjects.index') }}" class="text-blue-600">Môn học</a>
          <a href="{{ route('topics.index') }}" class="text-blue-600">Chủ đề</a>
        @endif

        {{-- Admin: mọi thứ --}}
        @if($u->isAdmin())
          <a href="{{ route('questions.index') }}" class="text-blue-600">Ngân hàng câu hỏi</a>
          <a href="{{ route('exams.index') }}" class="text-blue-600">Đề thi</a>
          <a href="{{ route('exams.catalog') }}" class="text-blue-600">Thi trực tuyến</a>
          <a href="{{ route('attempts.index') }}" class="text-blue-600">Kết quả</a>
          {{-- ví dụ: quản lý user nếu bạn có --}}
          {{-- <a href="{{ route('users.index') }}" class="text-blue-600">Người dùng</a> --}}
        @endif

        <form method="post" action="/logout" class="inline">@csrf
          <button class="text-red-600">Đăng xuất</button>
        </form>
      </div>
    @endauth

  </div>
</nav>
<div class="max-w-6xl mx-auto px-4">
  @include('partials.alerts')
  @yield('content')
</div>
</body>
</html>
