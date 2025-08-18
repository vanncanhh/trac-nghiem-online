@extends('layouts.app')

@section('content')
<h1 class="text-xl font-semibold mb-4">Sửa đề: {{ $exam->title }}</h1>

{{-- ====================== FORM LỌC (GET) – TÁCH RIÊNG ====================== --}}
<form id="filterForm" method="get" class="grid md:grid-cols-3 gap-2 mb-4">
  <select name="subject_id" class="border p-2 rounded">
    <option value="">-- Môn học --</option>
    @foreach($subjects as $s)
      <option value="{{ $s->id }}" @selected(($filters['subject_id']??'')==$s->id)>{{ $s->name }}</option>
    @endforeach
  </select>

  <select name="difficulty" class="border p-2 rounded">
    <option value="">-- Độ khó --</option>
    @foreach(['easy'=>'Dễ','med'=>'TB','hard'=>'Khó'] as $k=>$v)
      <option value="{{ $k }}" @selected(($filters['difficulty']??'')==$k)>{{ $v }}</option>
    @endforeach
  </select>

  <button type="submit" class="bg-gray-800 text-white rounded px-3">Lọc</button>
</form>

{{-- ====================== FORM LƯU (PUT) ====================== --}}
<form method="post" action="{{ route('exams.update',$exam) }}" class="space-y-4">
  @csrf
  @method('PUT')

  {{-- Thuộc tính đề --}}
  <div class="grid md:grid-cols-3 gap-3">
    <div class="md:col-span-2">
      <label class="block mb-1 font-medium">Tiêu đề</label>
      <input name="title" value="{{ old('title',$exam->title) }}" class="border p-2 rounded w-full">
    </div>

    <div>
      <label class="block mb-1 font-medium">Thời lượng (phút)</label>
      <input type="number" name="duration_minutes" min="5" value="{{ old('duration_minutes',$exam->duration_minutes) }}" class="border p-2 rounded w-full">
    </div>

    <div>
      <label class="block mb-1 font-medium">Môn học</label>
      <select name="subject_id" class="border p-2 rounded w-full">
        <option value="">—</option>
        @foreach($subjects as $s)
          <option value="{{ $s->id }}" @selected(old('subject_id',$exam->subject_id)==$s->id)>{{ $s->name }}</option>
        @endforeach
      </select>
    </div>
  </div>

  {{-- Danh sách câu hỏi để chọn --}}
  <div class="bg-white border rounded p-3">
    <div class="flex justify-between items-center mb-2">
      <div class="font-medium">Chọn câu hỏi đưa vào đề</div>
      <div class="text-sm text-gray-600">
        Đang chọn: <span id="selCount">{{ count($selectedIds) }}</span>
      </div>
    </div>

    <div class="space-y-2">
      @foreach($questions as $q)
        <label class="block border rounded p-2 bg-gray-50">
          <div class="flex gap-2">
            <input
              type="checkbox"
              name="question_ids[]"
              value="{{ $q->id }}"
              @checked(in_array($q->id,$selectedIds))
            >
            <div>
              <div class="font-medium">
                #{{ $q->id }} · {{ $q->subject->name ?? 'N/A' }} · {{ strtoupper($q->difficulty) }} · {{ $q->points }}đ
              </div>
              <div>{{ $q->content }}</div>
            </div>
          </div>
        </label>
      @endforeach
    </div>

    <div class="mt-3">
      {{ $questions->links() }}
    </div>
  </div>

  <div class="flex gap-2">
    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Lưu đề</button>
    <a href="{{ route('exams.index') }}" class="px-3 py-2">Quay lại</a>
  </div>
</form>

{{-- ====================== TẠO NGẪU NHIÊN ====================== --}}
<div class="mt-6 bg-white border rounded p-3">
  <div class="font-medium mb-2">Tạo đề tự động</div>
  <form method="post" action="{{ route('exams.auto',$exam) }}" class="grid md:grid-cols-5 gap-2 items-end">
    @csrf
    <div>
      <label class="block text-sm mb-1">Môn (tuỳ chọn)</label>
      <select name="subject_id" class="border p-2 rounded w-full">
        <option value="">—</option>
        @foreach($subjects as $s)
          <option value="{{ $s->id }}">{{ $s->name }}</option>
        @endforeach
      </select>
    </div>

    <div>
      <label class="block text-sm mb-1">Tổng số câu</label>
      <input type="number" min="1" name="total" value="20" class="border p-2 rounded w-full">
    </div>

    <div>
      <label class="block text-sm mb-1">% Dễ</label>
      <input type="number" min="0" max="100" name="mix[easy]" value="40" class="border p-2 rounded w-full">
    </div>

    <div>
      <label class="block text-sm mb-1">% TB</label>
      <input type="number" min="0" max="100" name="mix[med]" value="40" class="border p-2 rounded w-full">
    </div>

    <div>
      <label class="block text-sm mb-1">% Khó</label>
      <input type="number" min="0" max="100" name="mix[hard]" value="20" class="border p-2 rounded w-full">
    </div>

    <div class="md:col-span-5">
      <button class="mt-2 px-4 py-2 bg-purple-600 text-white rounded">
        Tạo ngẫu nhiên & ghi đè
      </button>
    </div>
  </form>
</div>

{{-- ====================== SCRIPT ====================== --}}
<script>
  // Đếm nhanh cho UI khi tick trên trang hiện tại (không xử lý phân trang)
  function countSelOnce() {
    const n = document.querySelectorAll('input[name="question_ids[]"]:checked').length;
    document.getElementById('selCount').innerText = n;
  }
</script>

<script>
(function(){
  // Lưu lựa chọn qua nhiều trang bằng localStorage + tiêm hidden khi submit
  const key = 'examSel-{{ $exam->id }}';
  let selected = new Set(@json($selectedIds).map(String));

  // Khôi phục những gì đã chọn ở các trang khác (nếu có)
  try {
    const saved = JSON.parse(localStorage.getItem(key) || '[]');
    saved.forEach(id => selected.add(String(id)));
  } catch(e){}

  const countEl = document.getElementById('selCount');
  const form = document.querySelector('form[action="{{ route('exams.update',$exam) }}"]');

  function updateCount(){ countEl.textContent = selected.size; }

  function syncUIFromState(){
    document.querySelectorAll('input[name="question_ids[]"]').forEach(cb => {
      cb.checked = selected.has(String(cb.value));
    });
    updateCount();
  }

  // Lắng nghe tick/untick trên trang hiện tại
  document.querySelectorAll('input[name="question_ids[]"]').forEach(cb => {
    cb.addEventListener('change', e => {
      const id = String(e.target.value);
      if (e.target.checked) selected.add(id);
      else selected.delete(id);
      localStorage.setItem(key, JSON.stringify([...selected]));
      updateCount();
    });
  });

  // Trước khi submit: tiêm hidden cho mọi ID đã chọn (kể cả không có trên trang hiện tại)
  form.addEventListener('submit', () => {
    form.querySelectorAll('input[type="hidden"][data-injected="1"]').forEach(el => el.remove());
    [...selected].forEach(id => {
      if (!form.querySelector(`input[name="question_ids[]"][value="${id}"]`)) {
        const inp = document.createElement('input');
        inp.type = 'hidden';
        inp.name = 'question_ids[]';
        inp.value = id;
        inp.setAttribute('data-injected','1');
        form.appendChild(inp);
      }
    });
    localStorage.removeItem(key); // dọn sau khi lưu
  });

  // Áp trạng thái khi load trang
  syncUIFromState();
})();
</script>
@endsection
