@extends('layouts.app')

@section('content')
{{-- Thanh danh mục đỏ --}}
<div class="mb-4">
  <div class="rounded-full bg-red-500 text-white px-4 py-2 shadow flex flex-wrap items-center gap-4">
    <div class="font-semibold flex items-center gap-2">
      <svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24"><path d="M3 6h18v2H3V6zm0 5h12v2H3v-2zm0 5h18v2H3v-2z"/></svg>
      LỚP HỌC
    </div>
    <div class="relative group">
      <button class="hover:underline">CHỌN LỚP</button>
      <div class="hidden group-hover:block absolute z-10 bg-white text-gray-800 mt-2 rounded shadow min-w-[220px] p-2">
        @forelse($classrooms as $c)
          <div class="px-3 py-1 hover:bg-gray-100 rounded">{{ $c->name }}</div>
        @empty
          <div class="px-3 py-1 text-sm text-gray-500">Chưa có lớp</div>
        @endforelse
      </div>
    </div>

    <div class="relative group">
      <button class="hover:underline">KIỂM TRA</button>
      <div class="hidden group-hover:block absolute z-10 bg-white text-gray-800 mt-2 rounded shadow min-w-[220px] p-2">
        @foreach($subjects as $s)
          <div class="px-3 py-1 hover:bg-gray-100 rounded">{{ $s->name }}</div>
        @endforeach
      </div>
    </div>

    <!-- <a class="hover:underline" href="#">THI ĐẤU</a>
    <a class="hover:underline" href="#">ÔN THI TN THPT</a> -->
    <a class="hover:underline" href="#">GIỚI THIỆU</a>

    <div class="ml-auto flex items-center gap-2">
      @guest
        <a href="{{ route('login') }}" class="px-3 py-1.5 bg-gray-900 text-white rounded">Đăng nhập</a>
        <a href="{{ route('register') }}" class="px-3 py-1.5 bg-yellow-300 text-gray-900 rounded">Đăng ký</a>
      @else
        <span class="text-white/90">{{ auth()->user()->name }}</span>
      @endguest
    </div>
  </div>
</div>

{{-- Banner --}}
<div class="mb-6">
  <div class="w-full h-36 md:h-44 rounded-xl bg-gradient-to-r from-orange-300 to-pink-300 flex items-center justify-center shadow">
    <div class="text-white text-xl md:text-2xl font-semibold tracking-wide">
      <img src="{{ asset('banner-pc4.webp') }}" alt="">
    </div>
  </div>
</div>

{{-- Bố cục 2 cột --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
  {{-- Cột trái: nội dung chính --}}
  <div class="lg:col-span-2 space-y-8">
    @forelse($sections as $title => $items)
      <section>
        <div class="text-red-600 font-bold mb-3">{{ $title }}</div>
        <div class="grid md:grid-cols-2 gap-4">
          @foreach($items as $e)
            <div class="bg-white rounded-lg border shadow-sm p-4">
              <div class="font-semibold mb-1">{{ $e->title }}</div>
              <div class="text-sm text-gray-600 mb-3">
                {{ $e->classroom->name ?? '—' }} · {{ $e->subject->name ?? '—' }} · {{ $e->duration_minutes }} phút
              </div>
              <div class="flex gap-2">
                <a href="{{ route('attempts.start',$e) }}"
                   class="px-3 py-2 bg-green-600 text-white rounded">
                  Bắt đầu
                </a>
                <a href="{{ route('exams.catalog', ['classroom_id'=>$e->classroom_id,'subject_id'=>$e->subject_id]) }}"
                   class="px-3 py-2 bg-gray-100 rounded">
                  Xem thêm
                </a>
              </div>
            </div>
          @endforeach
        </div>
      </section>
    @empty
      <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded">
        Chưa có đề thi public để hiển thị. Giáo viên có thể tạo đề và bật <b>Public</b>.
      </div>
    @endforelse
  </div>

  {{-- Cột phải: sidebar --}}
  <aside class="space-y-4">
    <div class="bg-white rounded-lg border p-4">
      <div class="font-semibold mb-2">Đăng ký mua thẻ VIP</div>
      <p class="text-sm text-gray-600 mb-3">Học không giới hạn, không quảng cáo.</p>
      <a href="#" class="px-3 py-2 bg-amber-400 text-gray-900 rounded font-semibold">Đăng ký VIP</a>
    </div>

    <div class="bg-white rounded-lg border p-4">
      <div class="font-semibold mb-2">Hỏi đáp nhanh</div>
      <ul class="text-sm space-y-2">
        <li><a href="#" class="text-blue-600">Mua/đổi mã thẻ</a></li>
        <li><a href="#" class="text-blue-600">Hỗ trợ đăng nhập</a></li>
        <li><a href="#" class="text-blue-600">Báo lỗi nội dung</a></li>
      </ul>
    </div>

    <div class="bg-white rounded-lg border p-4">
      <div class="font-semibold mb-2">Học Tin học</div>
      <ul class="text-sm space-y-1 text-gray-700">
        <li>• Lập trình Python</li>
        <li>• Lập trình Pascal</li>
        <li>• Lập trình Scratch</li>
        <li>• Tin học lớp 3–9</li>
        <li>• Lý thuyết Toán</li>
      </ul>
    </div>
  </aside>
</div>
@endsection
