@extends('admin.layouts.app')

@section('title', 'Data Nominal')

@section('content')
<div
  x-data="{
    showModalTambah: false,
    showModalEdit: false,
    showModalHapus: false,
    showModalFetch: false,

    idEdit: '',
    gameEdit: '',
    nameEdit: '',
    baseEdit: '',
    sellEdit: '',
    activeEdit: '',

    deleteId: '',
  }"
  class="p-6 bg-white rounded-lg shadow-md dark:bg-gray-800 relative"
>

  <h4 class="mb-4 text-lg font-semibold text-gray-600 dark:text-gray-300">
    Data Nominal Game
  </h4>

  {{-- Tombol Tambah dan Fetch --}}
  <div class="mb-4 flex justify-between items-center">
    <div class="flex space-x-2">
      <button
        @click="showModalTambah = true"
        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
        + Tambah Nominal
      </button>
      <button
        @click="showModalFetch = true"
        class="px-4 py-2 bg-green-600 text-black rounded-lg hover:bg-green-700">
        📥 Sync Provider
      </button>
    </div>
  </div>
  {{-- FILTER --}}
<form method="GET" class="mb-6 bg-white dark:bg-gray-800 p-4 rounded-lg shadow">

  {{-- Search --}}
  <div class="mb-4">
    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
      Cari Nominal
    </label>
    <input type="text"
      name="search"
      value="{{ request('search') }}"
      placeholder="Cari nama nominal..."
      oninput="this.form.submit()"
      class="w-full p-2 border rounded-md dark:bg-gray-700 dark:text-gray-200">
  </div>

  {{-- Filter Game & Status --}}
  <div class="flex items-end space-x-6">

    {{-- Game --}}
    <div class="flex-1">
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
        Game
      </label>
      <select name="game"
        onchange="this.form.submit()"
        class="w-full p-2 border rounded-md dark:bg-gray-700 dark:text-gray-200">
        <option value="">Semua Game</option>
        @foreach($games as $game)
          <option value="{{ $game->id }}"
            {{ request('game') == $game->id ? 'selected' : '' }}>
            {{ $game->name }}
          </option>
        @endforeach
      </select>
    </div>
    {{-- Provider --}}
  <div class="flex-1">
    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
      Provider
    </label>
    <select name="provider"
      onchange="this.form.submit()"
      class="w-full p-2 border rounded-md dark:bg-gray-700 dark:text-gray-200">
      <option value="">Semua Provider</option>
      @foreach($provider as $prov)
        <option value="{{ $prov->id }}"
          {{ request('provider') == $prov->id ? 'selected' : '' }}>
          {{ $prov->name }}
        </option>
      @endforeach
    </select>
  </div>


    {{-- Status --}}
    <div class="flex-1">
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
        Status
      </label>
      <select name="status"
        onchange="this.form.submit()"
        class="w-full p-2 border rounded-md dark:bg-gray-700 dark:text-gray-200">
        <option value="">Semua Status</option>
        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>
          Aktif
        </option>
        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>
          Nonaktif
        </option>
      </select>
    </div>

  </div>
</form>


  {{-- Tabel --}}
  <div class="w-full overflow-x-auto rounded-lg shadow">
    <table class="w-full whitespace-no-wrap">
      <thead>
        <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50 dark:bg-gray-700">
          <th class="px-4 py-3">Game</th>
          <th class="px-4 py-3">Nominal</th>
          <th class="px-4 py-3">Provider</th>
          <th class="px-4 py-3">Harga Provider</th>
          <th class="px-4 py-3">Harga Jual</th>
          <th class="px-4 py-3">Status</th>
          <th class="px-4 py-3 text-center">Aksi</th>
        </tr>
      </thead>

      <tbody class="bg-white dark:bg-gray-800 divide-y">
        @forelse ($nominals as $row)
        <tr class="text-gray-700 dark:text-gray-300">
          <td class="px-4 py-3">{{ $row->game->name }}</td>
          <td class="px-4 py-3">{{ $row->name }}</td>
          <td class="px-4 py-3">{{ $row->provider->name ?? 'Tidak Ada Provider' }}</td>
          <td class="px-4 py-3">
            Rp {{ number_format($row->base_price,0,',','.') }}
          </td>
          <td class="px-4 py-3 font-semibold text-green-600">
            Rp {{ number_format($row->selling_price,0,',','.') }}
          </td>

          <td class="px-4 py-3">
            @if($row->is_active)
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
                idEdit = '{{ $row->id }}';
                gameEdit = '{{ $row->game_id }}';
                nameEdit = '{{ addslashes($row->name) }}';
                baseEdit = '{{ $row->base_price }}';
                sellEdit = '{{ $row->selling_price }}';
                activeEdit = '{{ $row->is_active }}';
              "
              class="text-blue-600 hover:bg-blue-100 dark:hover:bg-gray-700 rounded-lg px-2 py-1">
              ✏️
            </button>

            {{-- Hapus --}}
            <button
              @click="showModalHapus = true; deleteId = '{{ $row->id }}';"
              class="text-red-600 hover:bg-red-100 dark:hover:bg-red-700 rounded-lg px-2 py-1">
              🗑️
            </button>

          </td>
        </tr>
        @empty
        <tr>
          <td colspan="6" class="text-center py-4 text-gray-500">
            Tidak ada data nominal
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>

    <div class="mt-6 flex justify-end">
      {{ $nominals->links('vendor.pagination.tailwind') }}
    </div>
  </div>

  {{-- MODAL TAMBAH --}}
  <div x-show="showModalTambah" class="fixed inset-0 z-30 flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg w-full max-w-xl">
      <h2 class="text-lg font-semibold mb-4">Tambah Nominal</h2>

      <form action="{{ route('nominal.store') }}" method="POST" class="space-y-4">
        @csrf

        <div>
          <label>Game</label>
          <select name="game_id" class="w-full p-2 border rounded-lg dark:bg-gray-700" required>
            <option value="">-- Pilih Game --</option>
            @foreach($games as $game)
              <option value="{{ $game->id }}">{{ $game->name }}</option>
            @endforeach
          </select>
        </div>

        <div>
          <label>Provider</label>
          <select name="provider_id" class="w-full p-2 border rounded-lg dark:bg-gray-700" required>
            <option value="">-- Pilih Provider --</option>
            @foreach($provider as $prov)
              <option value="{{ $prov->id }}">{{ $prov->name }}</option>
            @endforeach
          </select>
        </div>

        <div>
          <label>Nama Nominal</label>
          <input type="text" name="name" class="w-full p-2 border rounded-lg dark:bg-gray-700" required>
        </div>

        <div>
          <label>Harga Provider</label>
          <input type="number" name="base_price" class="w-full p-2 border rounded-lg dark:bg-gray-700" required>
        </div>

        <div>
          <label>Harga Jual</label>
          <input type="number" name="selling_price" class="w-full p-2 border rounded-lg dark:bg-gray-700" required>
        </div>

        <label class="flex items-center space-x-2">
          <input type="checkbox" name="is_active" value="1" checked>
          <span>Aktif</span>
        </label>

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
      <h2 class="text-lg font-semibold mb-4">Edit Nominal</h2>

      <form :action="'/admin/nominal/update/' + idEdit" method="POST" class="space-y-4">
        @csrf @method('PUT')

        <div>
          <label>Game</label>
          <select name="game_id" x-model="gameEdit" class="w-full p-2 border rounded-lg dark:bg-gray-700">
            @foreach($games as $game)
              <option value="{{ $game->id }}">{{ $game->name }}</option>
            @endforeach
          </select>
        </div>

        <div>
          <label>Nama Nominal</label>
          <input type="text" name="name" x-model="nameEdit" class="w-full p-2 border rounded-lg dark:bg-gray-700">
        </div>

        <div>
          <label>Harga Provider</label>
          <input type="number" name="base_price" x-model="baseEdit" class="w-full p-2 border rounded-lg dark:bg-gray-700">
        </div>

        <div>
          <label>Harga Jual</label>
          <input type="number" name="selling_price" x-model="sellEdit" class="w-full p-2 border rounded-lg dark:bg-gray-700">
        </div>

        <label class="flex items-center space-x-2">
          <input type="checkbox" name="is_active" :checked="activeEdit == 1">
          <span>Aktif</span>
        </label>

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
      <h2 class="text-lg font-semibold mb-4">Hapus Nominal</h2>
      <p class="mb-6">Yakin ingin menghapus nominal ini?</p>

      <div class="flex justify-end space-x-3">
        <button type="button" @click="showModalHapus = false"
          class="px-4 py-2 rounded-lg bg-gray-200 dark:bg-gray-700">Batal</button>

        <form :action="'/admin/nominal/delete/' + deleteId" method="POST">
          @csrf @method('DELETE')
          <button type="submit"
            class="px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700">
            Hapus
          </button>
        </form>
      </div>
    </div>
  </div>

  {{-- MODAL FETCH DIGIFLAZZ --}}
  <div x-show="showModalFetch" class="fixed inset-0 z-30 flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg w-full max-w-xl">
      <h2 class="text-lg font-semibold mb-4">Sync Produk Provider</h2>
      <p class="mb-4">Pilih provider dan game untuk mengambil produk.</p>

      {{-- ✅ Error messages --}}
        @if ($errors->any())
            <div class="bg-red-100 text-red-700 p-2 rounded mb-4">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

      <form action="{{ route('nominal.sync')  }}" method="POST" class="space-y-4">
        @csrf
        <div>
          <label>Provider</label>
          <select name="provider" class="w-full p-2 border rounded-lg dark:bg-gray-700" required>
              <option value="">-- Pilih Provider --</option>
              @foreach($provider as $prov)
                <option value="{{ $prov->id }}">{{ $prov->name }}</option>
              @endforeach
          </select>
        </div>

        <div>
          <label>Game</label>
          <select name="game_id" class="w-full p-2 border rounded-lg dark:bg-gray-700" required>
            <option value="">-- Pilih Game --</option>
            @foreach($games as $game)
              <option value="{{ $game->id }}">{{ $game->name }}</option>
            @endforeach
          </select>
        </div>

        <div class="flex justify-end">
          <button type="button" @click="showModalFetch = false"
            class="px-4 py-2 bg-gray-300 rounded-lg mr-2">Batal</button>
          <button type="submit"
            class="px-4 py-2 bg-green-600 text-white rounded-lg">Fetch & Simpan</button>
        </div>
      </form>
    </div>
  </div>

</div>
@endsection
