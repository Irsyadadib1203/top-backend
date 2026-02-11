@extends('admin.layouts.app')

@section('title', 'Data Game')

@section('content')
<div
  x-data="{
    showModalTambah: false,
    showModalEdit: false,
    showModalHapus: false,

    idEdit: '',
    nameEdit: '',
    categoryEdit: '',
    activeEdit: '',
    popularEdit: '',
    imageEdit: '',

    deleteId: '',
  }"
  class="p-6 bg-white rounded-lg shadow-md dark:bg-gray-800 relative"
>

  <h4 class="mb-4 text-lg font-semibold text-gray-600 dark:text-gray-300">
    Data Game
  </h4>

  {{-- Tombol Tambah --}}
  <div class="mb-4 flex justify-between items-center">
    <button
      @click="showModalTambah = true"
      class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
      + Tambah Game
    </button>
  </div>

  {{-- Tabel --}}
  <div class="w-full overflow-x-auto rounded-lg shadow">
    <table class="w-full whitespace-no-wrap">
      <thead>
        <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50 dark:bg-gray-700">
          <th class="px-4 py-3">Nama Game</th>
          <th class="px-4 py-3">Kategori</th>
          <th class="px-4 py-3">Popular</th>
          <th class="px-4 py-3">Status</th>
          <th class="px-4 py-3 text-center">Aksi</th>
        </tr>
      </thead>

      <tbody class="bg-white dark:bg-gray-800 divide-y">
        @forelse ($games as $game)
        <tr class="text-gray-700 dark:text-gray-300">
          <td class="px-4 py-3">{{ $game->name }}</td>
          <td class="px-4 py-3">{{ $game->category }}</td>

          <td class="px-4 py-3">
            @if($game->is_popular)
              <span class="px-2 py-1 text-xs bg-green-100 text-green-700 rounded-full">Ya</span>
            @else
              <span class="px-2 py-1 text-xs bg-gray-200 text-gray-600 rounded-full">Tidak</span>
            @endif
          </td>

          <td class="px-4 py-3">
            @if($game->is_active)
              <span class="px-2 py-1 text-xs bg-blue-100 text-blue-700 rounded-full">Aktif</span>
            @else
              <span class="px-2 py-1 text-xs bg-red-100 text-red-700 rounded-full">Nonaktif</span>
            @endif
          </td>

          <td class="px-4 py-3 flex justify-center space-x-2">
            {{-- Edit --}}
            <button
              @click="
                showModalEdit = true;
                idEdit = '{{ $game->id }}';
                nameEdit = '{{ addslashes($game->name) }}';
                categoryEdit = '{{ addslashes($game->category) }}';
                activeEdit = '{{ $game->is_active }}';
                popularEdit = '{{ $game->is_popular }}';
                imageEdit = '{{ addslashes($game->image_url) }}';
              "
              class="text-blue-600 hover:bg-blue-100 dark:hover:bg-gray-700 rounded-lg px-2 py-1">
              ✏️
            </button>

            {{-- Hapus --}}
            <button
              @click="showModalHapus = true; deleteId = '{{ $game->id }}';"
              class="text-red-600 hover:bg-red-100 dark:hover:bg-red-700 rounded-lg px-2 py-1">
              🗑️
            </button>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="5" class="text-center py-4 text-gray-500">
            Tidak ada data game
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>

    <div class="mt-6 flex justify-end">
      {{ $games->links('vendor.pagination.tailwind') }}
    </div>
  </div>

  {{-- MODAL TAMBAH --}}
  <div x-show="showModalTambah" class="fixed inset-0 z-30 flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg w-full max-w-xl">
      <h2 class="text-lg font-semibold mb-4">Tambah Game</h2>

      <form action="{{ route('game.store') }}" method="POST" class="space-y-4">
        @csrf

        <div>
          <label>Nama Game</label>
          <input type="text" name="name" class="w-full p-2 border rounded-lg dark:bg-gray-700" required>
        </div>

        <div>
          <label>Kategori</label>
          <input type="text" name="category" class="w-full p-2 border rounded-lg dark:bg-gray-700" required>
        </div>

        <div>
          <label>Image URL</label>
          <input type="text" name="image_url" class="w-full p-2 border rounded-lg dark:bg-gray-700">
        </div>

        <div class="flex space-x-4">
          <label class="flex items-center space-x-2">
            <input type="checkbox" name="is_active" value="1">
            <span>Aktif</span>
          </label>

          <label class="flex items-center space-x-2">
            <input type="checkbox" name="is_popular" value="1">
            <span>Popular</span>
          </label>
        </div>

        <div class="flex justify-end">
          <button type="button" @click="showModalTambah = false"
            class="px-4 py-2 bg-gray-300 rounded-lg mr-2">Batal</button>
          <button type="submit"
            class="px-4 py-2 bg-blue-600 text-white rounded-lg">Simpan</button>
        </div>
      </form>
    </div>
  </div>

  {{-- MODAL EDIT --}}
  <div x-show="showModalEdit" class="fixed inset-0 z-30 flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg w-full max-w-xl">
      <h2 class="text-lg font-semibold mb-4">Edit Game</h2>

      <form :action="'/game/update/' + idEdit" method="POST" class="space-y-4">
        @csrf @method('PUT')

        <div>
          <label>Nama Game</label>
          <input type="text" name="name" x-model="nameEdit" class="w-full p-2 border rounded-lg dark:bg-gray-700" required>
        </div>

        <div>
          <label>Kategori</label>
          <input type="text" name="category" x-model="categoryEdit" class="w-full p-2 border rounded-lg dark:bg-gray-700" required>
        </div>

        <div>
          <label>Image URL</label>
          <input type="text" name="image_url" x-model="imageEdit" class="w-full p-2 border rounded-lg dark:bg-gray-700">
        </div>

        <div class="flex space-x-4">
          <label class="flex items-center space-x-2">
            <input type="checkbox" name="is_active" :checked="activeEdit == 1">
            <span>Aktif</span>
          </label>

          <label class="flex items-center space-x-2">
            <input type="checkbox" name="is_popular" :checked="popularEdit == 1">
            <span>Popular</span>
          </label>
        </div>

        <div class="flex justify-end">
          <button type="button" @click="showModalEdit = false"
            class="px-4 py-2 bg-gray-300 rounded-lg mr-2">Batal</button>
          <button type="submit"
            class="px-4 py-2 bg-blue-600 text-white rounded-lg">Update</button>
        </div>
      </form>
    </div>
  </div>

  {{-- MODAL HAPUS --}}
  <div x-show="showModalHapus" class="fixed inset-0 z-30 flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg w-96">
      <h2 class="text-lg font-semibold mb-4">Konfirmasi Hapus</h2>
      <p class="mb-6">Yakin ingin menghapus game ini?</p>

      <div class="flex justify-end space-x-3">
        <button type="button" @click="showModalHapus = false"
          class="px-4 py-2 rounded-lg bg-gray-200 dark:bg-gray-700">Batal</button>

        <form :action="'/game/delete/' + deleteId" method="POST">
          @csrf @method('DELETE')
          <button type="submit"
            class="px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700">
            Hapus
          </button>
        </form>
      </div>
    </div>
  </div>

</div>
@endsection
