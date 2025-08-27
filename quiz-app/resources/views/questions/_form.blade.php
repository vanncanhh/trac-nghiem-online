@php
  $topics = $topics ?? collect();
@endphp

@php
  $isEdit = isset($question);
  $title  = $isEdit ? "Sửa câu hỏi #{$question->id}" : "Thêm câu hỏi";
  $opts = collect(old('options', $isEdit
      ? $question->options->map(fn($o)=>['text'=>$o->text,'is_correct'=>$o->is_correct])->toArray()
      : [
          ['text'=>'', 'is_correct'=>false],
          ['text'=>'', 'is_correct'=>false],
          ['text'=>'', 'is_correct'=>false],
          ['text'=>'', 'is_correct'=>false],
        ]
  ));
  $correctIndex = (int) $opts->search(fn($o)=>!empty($o['is_correct']));
@endphp

<div class="space-y-4">
  <div class="grid md:grid-cols-3 gap-3">
    <div>
      <label class="block mb-1 font-medium">Lớp</label>
      <select name="classroom_id" class="border p-2 rounded w-full" required>
        @foreach($classrooms as $c)
          <option value="{{ $c->id }}"
            @selected(old('classroom_id', $question->classroom_id ?? '') == $c->id)>{{ $c->name }}</option>
        @endforeach
      </select>
    </div>
    <div>
      <label class="block mb-1 font-medium">Môn học</label>
      <select name="subject_id" class="border p-2 rounded w-full" required>
        @foreach($subjects as $s)
          <option value="{{ $s->id }}"
            @selected(old('subject_id', $question->subject_id ?? '') == $s->id)>{{ $s->name }}</option>
        @endforeach
      </select>
    </div>
    
    <div>
      <label class="block mb-1 font-medium">Chủ đề</label>
      <div class="flex gap-2">
        <select name="topic_id" class="border p-2 rounded w-full">
          <option value="">—</option>
          @foreach($topics as $t)
            <option value="{{ $t->id }}"
              @selected((string)old('topic_id', $question->topic_id ?? '') === (string)$t->id)>
              {{ $t->name }}
            </option>
          @endforeach
        </select>
        <a href="{{ route('topics.create') }}" target="_blank" title="Thêm chủ đề" class="px-3 py-2 bg-gray-200 rounded">+</a>
      </div>
    </div>

    <div>
      <label class="block mb-1 font-medium">Độ khó</label>
      <select name="difficulty" class="border p-2 rounded w-full" required>
        @foreach(['easy'=>'Dễ','med'=>'Trung bình','hard'=>'Khó'] as $k=>$v)
          <option value="{{ $k }}" @selected(old('difficulty', $question->difficulty ?? 'med')==$k)>{{ $v }}</option>
        @endforeach
      </select>
    </div>
    <div>
      <label class="block mb-1 font-medium">Điểm</label>
      <input type="number" min="1" name="points" value="{{ old('points',$question->points ?? 1) }}"
             class="border p-2 rounded w-full" required>
    </div>
  </div>
  <div>
    <label class="block mb-1 font-medium">Nguồn (tuỳ chọn)</label>
    <input name="source" value="{{ old('source',$question->source ?? '') }}" class="border p-2 rounded w-full" placeholder="VD: Sách A - Chương 3 / Đề thi 2023">
  </div>

  <div>
    <label class="block mb-1 font-medium">Nội dung câu hỏi</label>
    <textarea name="content" rows="3" class="border p-2 rounded w-full" required>{{ old('content',$question->content ?? '') }}</textarea>
  </div>

  <div class="bg-white border rounded p-3">
    <div class="font-medium mb-2">Hình ảnh (tuỳ chọn)</div>
    @if($isEdit && !empty($question->image_path))
      <div class="mb-2">
        <img
          src="{{ $question->image_url ?? asset('storage/'.$question->image_path) }}"
          class="max-h-40 rounded border"
          alt="preview">
      </div>
      <label class="inline-flex items-center gap-2 mb-2">
        <input type="checkbox" name="remove_image" value="1"> Xoá ảnh hiện tại
      </label>
    @endif
    <input type="file" name="image" accept="image/*" class="block">
    <p class="text-sm text-gray-500 mt-1">Hỗ trợ JPG/PNG/WebP/GIF, tối đa 4MB.</p>
  </div>

  <div class="bg-white border rounded p-3">
    <div class="flex justify-between items-center mb-2">
      <div class="font-medium">Phương án trả lời (chọn 1 đáp án đúng)</div>
      <button type="button" onclick="addOption()" class="text-blue-600">+ Thêm phương án</button>
    </div>

    <input type="hidden" id="correctIndex" name="__correct_index" value="{{ $correctIndex >= 0 ? $correctIndex : 0 }}">

    <div id="optWrap" class="space-y-2">
      @foreach($opts as $i => $op)
        <div class="flex items-center gap-3 bg-gray-50 border rounded p-2">
          <input type="radio" name="__correct" value="{{ $i }}" class="mt-0.5"
                 @checked($i === $correctIndex || ($correctIndex<0 && $i===0))
                 onclick="syncCorrect(this.value)">
          <input type="text" name="options[{{ $i }}][text]" value="{{ $op['text'] ?? '' }}"
                 placeholder="Nội dung đáp án" class="border p-2 rounded w-full" required>

          {{-- field boolean để controller nhận ra đáp án đúng --}}
          <input type="hidden" name="options[{{ $i }}][is_correct]" value="{{ !empty($op['is_correct']) ? 1 : 0 }}">
          <button type="button" class="text-red-600" onclick="removeOption(this)">X</button>
        </div>
      @endforeach
    </div>

    <p class="text-sm text-gray-500 mt-2">
      Lưu ý: Chỉ 1 phương án là đáp án đúng. Hệ thống sẽ tự đặt <code>is_correct=1</code> cho phương án được chọn.
    </p>
  </div>
</div>

<script>
let optIndex = {{ $opts->count() }};

function syncCorrect(idx){
  document.getElementById('correctIndex').value = idx;
  // set tất cả is_correct về 0, riêng idx về 1
  document.querySelectorAll('#optWrap input[type="hidden"][name^="options"]').forEach((el, i)=>{
    el.value = (i == idx) ? 1 : 0;
  });
}

function addOption(){
  const wrap = document.getElementById('optWrap');
  const idx  = optIndex++;

  const row = document.createElement('div');
  row.className = "flex items-center gap-3 bg-gray-50 border rounded p-2";
  row.innerHTML = `
    <input type="radio" name="__correct" value="${idx}" onclick="syncCorrect(${idx})">
    <input type="text" name="options[${idx}][text]" placeholder="Nội dung đáp án"
           class="border p-2 rounded w-full" required>
    <input type="hidden" name="options[${idx}][is_correct]" value="0">
    <button type="button" class="text-red-600" onclick="removeOption(this)">X</button>
  `;
  wrap.appendChild(row);
}

function removeOption(btn){
  const wrap = document.getElementById('optWrap');
  if (wrap.children.length <= 2) { alert('Cần ít nhất 2 phương án.'); return; }
  const row = btn.parentElement;
  const radio = row.querySelector('input[type="radio"]');
  const wasCorrect = radio && radio.checked;
  row.remove();
  // nếu vừa xoá đáp án đúng → set đáp án đầu tiên còn lại là đúng
  if (wasCorrect) {
    const firstRadio = wrap.querySelector('input[type="radio"]');
    if (firstRadio){ firstRadio.checked = true; syncCorrect(firstRadio.value); }
  }
}
</script>
