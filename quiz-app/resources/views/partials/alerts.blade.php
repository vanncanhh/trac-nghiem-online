@if(session('ok'))
  <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('ok') }}</div>
@endif
@if ($errors->any())
  <div class="mb-4 p-3 bg-red-100 text-red-800 rounded">
    <div class="font-semibold mb-1">Có lỗi xảy ra:</div>
    <ul class="list-disc ml-5">
      @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
    </ul>
  </div>
@endif
