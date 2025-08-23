@extends('layouts.app')

@section('content')
<div class="flex justify-between items-center mb-4">
  <h1 class="text-xl font-semibold">Quản lý lớp học</h1>
  <a href="{{ route('classrooms.create') }}" class="px-3 py-2 bg-blue-600 text-white rounded">+ Thêm lớp</a>
</div>

@if(session('ok'))
  <div class="mb-3 px-3 py-2 bg-emerald-50 text-emerald-800 border border-emerald-200 rounded">
    {{ session('ok') }}
  </div>
@endif

@if($errors->any())
  <div class="mb-3 px-3 py-2 bg-red-50 text-red-800 border border-red-200 rounded">
    {{ $errors->first() }}
  </div>
@endif

@if($items->count() === 0)
  <div class="text-gray-600">Chưa có lớp nào.</div>
@else
  <div class="overflow-hidden border rounded bg-white">
    <table class="min-w-full text-left">
      <thead class="bg-gray-100">
        <tr>
          <th class="px-3 py-2 w-20">ID</th>
          <th class="px-3 py-2">Tên lớp</th>
          <th class="px-3 py-2">Ghi chú</th>
          <th class="px-3 py-2 w-40 text-right">Thao tác</th>
        </tr>
      </thead>
      <tbody class="divide-y">
        @foreach($items as $it)
          <tr>
            <td class="px-3 py-2 text-gray-600">#{{ $it->id }}</td>
            <td class="px-3 py-2 font-medium">{{ $it->name }}</td>
            <td class="px-3 py-2">{{ $it->note }}</td>
            <td class="px-3 py-2">
              <div class="flex justify-end gap-2">
                <a href="{{ route('classrooms.edit',$it) }}" class="px-2 py-1 bg-yellow-500 text-white rounded">Sửa</a>
                <form method="post" action="{{ route('classrooms.destroy',$it) }}"
                      onsubmit="return confirm('Xoá lớp \"{{ $it->name }}\"?');">
                  @csrf @method('DELETE')
                  <button class="px-2 py-1 bg-red-600 text-white rounded">Xoá</button>
                </form>
              </div>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <div class="mt-4">{{ $items->links() }}</div>
@endif
@endsection
