@extends('admin.layouts.app')

@section('title', 'Data Provider')

@section('content')
<div
  x-data="{
    showModalTambah: false,
    showModalEdit: false,
    showModalHapus: false,

    idEdit: '',
    nameEdit: '',
    codeEdit: '',
    apiUrlEdit: '',
    apiKeyEdit: '',
    statusEdit: '',

    deleteId: '',
  }"
  class="p-6 bg-white rounded-lg shadow-md dark:bg-gray-800 relative"
>

  <h4 class="mb-4 text-lg font-semibold text-gray-600 dark:text-gray-300">
    Data Provider
  </h4>

  {{-- Tombol Tambah --}}
  <div class="mb-4 flex justify-between items-center">
    <button
      @click="showModalTambah = true"
      class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
      + Tambah Provider
    </button>
  </div>

  {{-- TABEL --}}
  <div class="w-full overflow-x-auto rounded-lg shadow">
    <table class="w-full whitespace-no-wrap">
      <thead>
        <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50 dark:bg-gray-700">
          <th class="px-4 py-3">Nama</th>
          <th class="px-4 py-3">Kode</th>
          <th class="px-4 py-3">Saldo</th>
          <th class="px-4 py-3">Status</th>
          <th class="px-4 py-3 text-center">Aksi</th>
        </tr>
      </thead>

      <tbody class="bg-white dark:bg-gray-800 divide-y">
        @forelse ($providers as $provider)
        <tr class="text-gray-700 dark:text-gray-300">
          <td class="px-4 py-3">{{ $provider->name }}</td>
          <td class="px-4 py-3">{{ $provider->code }}</td>
          <td class="px-4 py-3">
            Rp {{ number_format($provider->balance ?? 0, 0, ',', '.') }}
          </td>

          <td class="px-4 py-3">
            @if($provider->is_active)
              <span class="px-2 py-1 text-xs bg-green-100 text-green-700 rounded-full">Aktif</span>
            @else
              <span class="px-2 py-1 text-xs bg-red-100 text-red-700 rounded-full">Nonaktif</span>
            @endif
          </td>

          <td class="px-4 py-3 flex justify-center space-x-2">
            {{-- EDIT --}}
            <button
              @click="
                showModalEdit = true;
                idEdit = '{{ $provider->id }}';
                nameEdit = '{{ addslashes($provider->name) }}';
                codeEdit = '{{ addslashes($provider->code) }}';
                apiUrlEdit = '{{ addslashes($provider->api_url) }}';
                apiKeyEdit = '{{ addslashes($provider->api_key) }}';
                statusEdit = '{{ $provider->is_active ? 1 : 0 }}';
              "
              class="text-blue-600 hover:bg-blue-100 dark:hover:bg-gray-700 rounded-lg px-2 py-1">
              ✏️
            </button>

            {{-- HAPUS --}}
            <button
              @click="showModalHapus = true; deleteId = '{{ $provider->id }}';"
              class="text-red-600 hover:bg-red-100 dark:hover:bg-red-700 rounded-lg px-2 py-1">
              🗑️
            </button>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="5" class="text-center py-4 text-gray-500">
            Tidak ada data provider
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>

    
  </div>

  {{-- MODAL TAMBAH --}}
  <div x-show="showModalTambah" class="fixed inset-0 z-30 flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg w-full max-w-xl">
      <h2 class="text-lg font-semibold mb-4">Tambah Provider</h2>

      <form action="{{ route('provider.store') }}" method="POST" class="space-y-4">
        @csrf

        <div>
          <label>Nama Provider</label>
          <input type="text" name="name" class="w-full p-2 border rounded-lg dark:bg-gray-700" required>
        </div>

        <div>
          <label>Kode Provider</label>
          <input type="text" name="code" class="w-full p-2 border rounded-lg dark:bg-gray-700" required>
        </div>

        <div>
          <label>API URL</label>
          <input type="text" name="api_url" class="w-full p-2 border rounded-lg dark:bg-gray-700">
        </div>

        <div>
          <label>API Key</label>
          <input type="text" name="api_key" class="w-full p-2 border rounded-lg dark:bg-gray-700">
        </div>

        <div>
          <label>Status</label>
          <select name="is_active" class="w-full p-2 border rounded-lg dark:bg-gray-700">
            <option value="1">Aktif</option>
            <option value="0">Nonaktif</option>
          </select>
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
      <h2 class="text-lg font-semibold mb-4">Edit Provider</h2>

      <form :action="'{{ url('admin/provider') }}/' + idEdit" method="POST" class="space-y-4">
        @csrf @method('PUT')

        <div>
          <label>Nama Provider</label>
          <input type="text" name="name" x-model="nameEdit"
            class="w-full p-2 border rounded-lg dark:bg-gray-700" required>
        </div>

        <div>
          <label>API URL</label>
          <input type="text" name="api_url" x-model="apiUrlEdit"
            class="w-full p-2 border rounded-lg dark:bg-gray-700">
        </div>

        <div>
          <label>API Key</label>
          <input type="text" name="api_key" x-model="apiKeyEdit"
            class="w-full p-2 border rounded-lg dark:bg-gray-700">
        </div>

        <div>
          <label>Status</label>
          <select name="is_active" x-model="statusEdit"
            class="w-full p-2 border rounded-lg dark:bg-gray-700">
            <option value="1">Aktif</option>
            <option value="0">Nonaktif</option>
          </select>
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
      <p class="mb-6">Yakin ingin menghapus provider ini?</p>

      <div class="flex justify-end space-x-3">
        <button type="button" @click="showModalHapus = false"
          class="px-4 py-2 rounded-lg bg-gray-200 dark:bg-gray-700">Batal</button>

        <form :action="'{{ url('admin/provider') }}/' + deleteId" method="POST">
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
