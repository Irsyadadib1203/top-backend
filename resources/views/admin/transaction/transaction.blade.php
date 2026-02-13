@extends('admin.layouts.app')

@section('title', 'Transaksi')

@section('content')

<div x-data="{
    // State untuk modal (semua false agar tidak muncul otomatis)
    showDetailModal: false,
    showEditModal: false,
    showDeleteModal: false,

    // Data untuk masing-masing modal
    trx: {},      // Untuk detail transaksi
    edit: {},     // Untuk edit transaksi
    delete: {},   // Untuk hapus transaksi

    // Method untuk membuka modal detail
    openDetail(data) {
      this.trx = data;
      this.showDetailModal = true;
    },

    // Method untuk membuka modal edit
    openEdit(data) {
      this.edit = data;
      this.showEditModal = true;
    },

    // Method untuk membuka modal hapus
    openDelete(id, invoice) {
      this.delete = { id, invoice };
      this.showDeleteModal = true;
    },

    // Method untuk menutup semua modal
    closeModal() {
      this.showDetailModal = false;
      this.showEditModal = false;
      this.showDeleteModal = false;
    }
  }"
  class="p-6 bg-white rounded-lg shadow-md dark:bg-gray-800 relative">
  <h4 class="mb-4 text-lg font-semibold text-gray-600 dark:text-gray-300">Daftar Transaksi</h4>

  <!-- FILTER -->

 <!-- FILTER -->
<form method="GET" class="mb-6">
  <!-- Search di baris sendiri -->
  <div class="mb-4">
    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Cari ID / Invoice</label>
    <input type="text" name="search" value="{{ request('search') }}"
      placeholder="Cari ID / Invoice"
      oninput="this.form.submit()"
      class="w-full p-2 border rounded-md dark:bg-gray-700 dark:text-gray-200">
  </div>

  <!-- 4 filter lainnya dalam satu baris -->
  <div class="flex flex-wrap gap-4 items-end">
    <div class="flex-1 min-w-0">
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Game</label>
      <select name="game" onchange="this.form.submit()" class="w-full p-2 border rounded-md dark:bg-gray-700 dark:text-gray-200">
        <option value="">Semua Game</option>
        @foreach($games as $game)
          <option value="{{ $game->id }}" {{ request('game') == $game->id ? 'selected' : '' }}>
            {{ $game->name }}
          </option>
        @endforeach
      </select>
    </div>

    <div class="flex-1 min-w-0">
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status Pesanan</label>
      <select name="status_pesanan" onchange="this.form.submit()" class="w-full p-2 border rounded-md dark:bg-gray-700 dark:text-gray-200">
        <option value="">Status Pesanan</option>
        <option value="pending" {{ request('status_pesanan')=='pending'?'selected':'' }}>Pending</option>
        <option value="success" {{ request('status_pesanan')=='success'?'selected':'' }}>Success</option>
        <option value="failed" {{ request('status_pesanan')=='failed'?'selected':'' }}>Failed</option>
      </select>
    </div>

    <div class="flex-1 min-w-0">
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status Pembayaran</label>
      <select name="status_pembayaran" onchange="this.form.submit()" class="w-full p-2 border rounded-md dark:bg-gray-700 dark:text-gray-200">
        <option value="">Status Pembayaran</option>
        <option value="paid" {{ request('status_pembayaran')=='paid'?'selected':'' }}>Paid</option>
        <option value="unpaid" {{ request('status_pembayaran')=='unpaid'?'selected':'' }}>Unpaid</option>
      </select>
    </div>

    <div class="flex-1 min-w-0">
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tanggal</label>
      <input type="date" name="tanggal" value="{{ request('tanggal') }}"
        onchange="this.form.submit()"
        class="w-full p-2 border rounded-md dark:bg-gray-700 dark:text-gray-200">
    </div>
  </div>
</form>

  <!-- TABLE -->

  <div class="w-full overflow-hidden rounded-lg shadow-xs mt-4">
    <div class="w-full overflow-x-auto">
    <table class="w-full whitespace-no-wrap">
      <thead>
        <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b dark:border-gray-700">
          <th class="px-4 py-3">Invoice</th>
          <th class="px-4 py-3">Game</th>
          <th class="px-4 py-3">User ID</th>
          <th class="px-4 py-3">Total</th>
          <th class="px-4 py-3">Status Pesanan</th>
          <th class="px-4 py-3">Status Pembayaran</th>
          <th class="px-4 py-3">Tanggal</th>
          <th class="px-4 py-3">Aksi</th>
        </tr>
      </thead>
      <tbody class="bg-white divide-y dark:divide-gray-700 dark:bg-gray-800">
        @forelse ($transaksis as $trx)
        <tr class="text-gray-700 dark:text-gray-300">
          <td class="px-4 py-3 font-semibold">{{ $trx->invoice }}</td>
          <td class="px-4 py-3">{{ $trx->game->name ?? '-' }}</td>
          <td class="px-4 py-3">{{ $trx->user_id }}</td>
          <td class="px-4 py-3">Rp {{ number_format($trx->total,0,',','.') }}</td>
          <td class="px-4 py-3">
            <span class="px-2 py-1 rounded text-white
              {{ $trx->status_pesanan=='success'?'bg-green-600':($trx->status_pesanan=='pending'?'bg-yellow-500':'bg-red-600') }}">
              {{ ucfirst($trx->status_pesanan) }}
            </span>
          </td>
          <td class="px-4 py-3">
            <span class="px-2 py-1 rounded text-white {{ $trx->status_pembayaran=='paid'?'bg-blue-600':'bg-gray-500' }}">
              {{ ucfirst($trx->status_pembayaran) }}
            </span>
          </td>
          <td class="px-4 py-3 text-sm">{{ $trx->created_at->format('d-m-Y H:i') }}</td>
          <td class="p-2 flex gap-2">

        {{-- Detail --}}
        <button
          @click="openDetail({
            id: {{ $trx->id }},
            invoice: '{{ $trx->invoice }}',
            game: '{{ $trx->game->name ?? "-" }}',
            total: '{{ number_format($trx->total) }}',
            tanggal: '{{ $trx->created_at->format('d M Y H:i') }}',
            status_pesanan: '{{ $trx->status_pesanan }}',
            status_pembayaran: '{{ $trx->status_pembayaran }}',
            serial_number: '{{ $trx->serial_number }}'
          })"
          class="text-blue-600 hover:underline"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5s8.268 2.943 9.542 7c-1.274 4.057-5.065 7-9.542 7S3.732 16.057 2.458 12z" />
                                <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2" />
            </svg>
        </button>

        {{-- Edit --}}
        <button
          @click="openEdit({
            id: {{ $trx->id }},
            status_pesanan: '{{ $trx->status_pesanan }}',
            status_pembayaran: '{{ $trx->status_pembayaran }}',
            serial_number: '{{ $trx->serial_number }}'
          })"
          class="text-yellow-600 hover:underline"
        >
          ✏️
        </button>

        {{-- Hapus --}}
        <button
          @click="openDelete({{ $trx->id }}, '{{ $trx->invoice }}')"
          class="text-red-600 hover:underline"
        >
          🗑️
        </button>

      </td>
        </tr>
        @empty
        <tr>
          <td colspan="7" class="px-4 py-6 text-center text-gray-500">Data transaksi kosong</td>
        </tr>
        @endforelse
      </tbody>
    </table>

  <!-- PAGINATION -->

  <div class="mt-6">
    {{ $transaksis->appends(request()->query())->links('vendor.pagination.tailwind') }}
  </div>
  </div>
</div>


  <!-- Modal Detail Transaksi -->
<div 
  x-show="showDetailModal"
  x-transition:enter="transition ease-out duration-150"
  x-transition:enter-start="opacity-0"
  x-transition:enter-end="opacity-100"
  x-transition:leave="transition ease-in duration-150"
  x-transition:leave-start="opacity-100"
  x-transition:leave-end="opacity-0"
  x-cloak
  class="fixed inset-0 z-30 flex items-end bg-black bg-opacity-50 sm:items-center sm:justify-center"
>
  <div
    x-show="showDetailModal"
    x-transition:enter="transition ease-out duration-150"
    x-transition:enter-start="opacity-0 transform translate-y-1/2"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0 transform translate-y-1/2"
    @click.away="showDetailModal = false"
    @keydown.escape="showDetailModal = false"
    class="bg-white dark:bg-gray-800 rounded-lg shadow-lg w-full max-w-3xl p-6 relative"
  >

    <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-6">
      Detail Transaksi
    </h2>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div>
        <p class="text-sm text-gray-500 dark:text-gray-400">Invoice</p>
        <p class="font-semibold text-gray-800 dark:text-gray-200" x-text="trx.invoice"></p>
      </div>

      <div>
        <p class="text-sm text-gray-500 dark:text-gray-400">Game</p>
        <p class="font-semibold text-gray-800 dark:text-gray-200" x-text="trx.game"></p>
      </div>

      <div>
        <p class="text-sm text-gray-500 dark:text-gray-400">Total</p>
        <p class="font-semibold text-gray-800 dark:text-gray-200">
          Rp <span x-text="trx.total"></span>
        </p>
      </div>

      <div>
        <p class="text-sm text-gray-500 dark:text-gray-400">Tanggal</p>
        <p class="font-semibold text-gray-800 dark:text-gray-200" x-text="trx.tanggal"></p>
      </div>

      <div>
        <p class="text-sm text-gray-500 dark:text-gray-400">Status Pesanan</p>
        <span
          class="px-3 py-1 rounded text-sm font-semibold"
          :class="trx.status_pesanan === 'success'
            ? 'bg-green-500 text-white'
            : trx.status_pesanan === 'pending'
            ? 'bg-yellow-500 text-black'
            : 'bg-red-500 text-white'"
          x-text="trx.status_pesanan"
        ></span>
      </div>

      <div>
        <p class="text-sm text-gray-500 dark:text-gray-400">Status Pembayaran</p>
        <span
          class="px-3 py-1 rounded text-sm font-semibold"
          :class="trx.status_pembayaran === 'paid'
            ? 'bg-green-500 text-white'
            : 'bg-gray-500 text-white'"
          x-text="trx.status_pembayaran"
        ></span>
      </div>
    </div>

    <div class="flex justify-end mt-8">
      <button
        type="button"
        @click="showDetailModal = false"
        class="px-4 py-2 bg-gray-300 dark:bg-gray-700 rounded-lg hover:bg-gray-400 dark:hover:bg-gray-600"
      >
        Tutup
      </button>
    </div>

  </div>
</div>

  
<!-- Modal Edit Galeri -->
<div 
  x-show="showEditModal" 
  x-transition:enter="transition ease-out duration-150"
  x-transition:enter-start="opacity-0"
  x-transition:enter-end="opacity-100"
  x-transition:leave="transition ease-in duration-150"
  x-transition:leave-start="opacity-100"
  x-transition:leave-end="opacity-0"
  x-cloak
        class="fixed inset-0 z-30 flex items-end bg-black bg-opacity-50 sm:items-center sm:justify-center"
>
  <div x-show="showEditModal"
  x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 transform translate-y-1/2"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0  transform translate-y-1/2"
        @click.away="closeModal"
        @keydown.escape="closeModal"
  class="bg-white dark:bg-gray-800 rounded-lg shadow-lg w-full max-w-4xl p-6 relative 
              max-h-[90vh] overflow-y-auto">
              
    <h2 class="text-lg font-semibold mb-4">Edit Transaksi</h2>

    <form :action="`/transaction/update/${edit.id}`" method="POST">
      @csrf
      @method('PUT')

      <div class="mb-4">
        <label>Status Pembayaran</label>
        <select name="status_pembayaran" class="w-full border rounded p-2"
            x-model="edit.status_pembayaran">
            <option value="paid">Paid</option>
            <option value="unpaid">Unpaid</option>
            <option value="failed">Failed</option>
        </select>
      </div>

      <div class="mb-4">
        <label>Status Pesanan</label>
        <select name="status_pesanan" class="w-full border rounded p-2"
          x-model="edit.status_pesanan">
          <option value="pending">Pending</option>
          <option value="success">Success</option>
          <option value="failed">Failed</option>
        </select>
      </div>

      <div class="mb-4">
        <label>Serial Number</label>
        <input type="text"
          name="serial_number"
          class="w-full border rounded p-2"
          x-model="edit.serial_number">
      </div>

      <div class="flex justify-end gap-2">
        <button type="button" @click="showEditModal=false"
          class="px-4 py-2 bg-gray-300 rounded">
          Batal
        </button>
        <button class="px-4 py-2 bg-blue-600 text-white rounded">
          Simpan
        </button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Hapus -->
<div 
   x-show="showDeleteModal"
    x-transition:enter="transition ease-out duration-150"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-30 flex items-end bg-black bg-opacity-50 sm:items-center sm:justify-center"
    x-cloak
  >
    <div 
    x-show="showDeleteModal"
    x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 transform translate-y-1/2"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0  transform translate-y-1/2"
        @click.away="closeModal"
        @keydown.escape="closeModal"
    class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-lg w-96">
      <h2 class="text-lg font-semibold mb-4">Hapus Transaksi</h2>

    <p>
      Yakin ingin menghapus transaksi
      <strong x-text="delete.invoice"></strong>?
    </p>

  <form :action="/transaction/delete/${delete.id}" method="POST">
      @csrf
      @method('DELETE')


      <div class="flex justify-end gap-2">
        <button type="button" @click="showDeleteModal=false"
          class="px-4 py-2 bg-gray-300 rounded">
          Batal
        </button>
        <button class="px-4 py-2 bg-red-600 text-white rounded">
          Hapus
        </button>
      </div>
    </form>
    </div>
  </div>

</div>
@endsection
