@extends('layouts.app')
@section('content')
<div class="flex justify-between mb-4">
  <h1 class="text-xl font-semibold">Chủ đề</h1>
  <a href="{{ route('topics.create') }}" class="px-3 py-2 bg-blue-600 text-white rounded">+ Thêm</a>
</div>
<div class="bg-white border rounded overflow-hidden">
  <table class="min-w-full">
    <thead class="bg-gray-100"><tr><th class="px-3 py-2">Tên chủ đề</th><th class="px-3 py-2 w-40 text-right">Thao tác</th></tr></thead>
    <tbody class="divide-y">
      @foreach($items as $it)
      <tr>
        <td class="px-3 py-2">{{ $it->name }}</td>
        <td class="px-3 py-2">
          <div class="flex justify-end gap-2">
            <a href="{{ route('topics.edit',$it) }}" class="px-2 py-1 bg-yellow-500 text-white rounded">Sửa</a>
            <form method="post" action="{{ route('topics.destroy',$it) }}" onsubmit="return confirm('Xoá?')">
              @csrf @method('DELETE') <button class="px-2 py-1 bg-red-600 text-white rounded">Xoá</button>
            </form>
          </div>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>
<div class="mt-4">{{ $items->links() }}</div>
@endsection
