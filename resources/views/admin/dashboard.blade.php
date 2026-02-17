@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('content')
  <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200">
    Dashboard
  </h2>

  <div class="grid gap-6 mb-8 md:grid-cols-2 xl:grid-cols-4">

  <!-- Total Pesanan -->
  <div class="flex items-center p-4 bg-white rounded-lg shadow-xs dark:bg-gray-800">
    <div class="p-3 mr-4 text-blue-500 bg-blue-100 rounded-full">
      📦
    </div>
    <div>
      <p class="mb-2 text-sm text-gray-600 dark:text-gray-400">Total Pesanan</p>
      <p class="text-lg font-semibold">{{ $totalPesanan }}</p>
    </div>
  </div>

  <!-- Pesanan Harian -->
  <div class="flex items-center p-4 bg-white rounded-lg shadow-xs dark:bg-gray-800">
    <div class="p-3 mr-4 text-yellow-500 bg-yellow-100 rounded-full">
      ⏱️
    </div>
    <div>
      <p class="mb-2 text-sm text-gray-600 dark:text-gray-400">Pesanan Hari Ini</p>
      <p class="text-lg font-semibold">{{ $pesananHarian }}</p>
    </div>
  </div>

  <!-- Total Penjualan -->
  <div class="flex items-center p-4 bg-white rounded-lg shadow-xs dark:bg-gray-800">
    <div class="p-3 mr-4 text-green-500 bg-green-100 rounded-full">
      💰
    </div>
    <div>
      <p class="mb-2 text-sm text-gray-600 dark:text-gray-400">Total Penjualan</p>
      <p class="text-lg font-semibold">
        Rp {{ number_format($totalPenjualan,0,',','.') }}
      </p>
    </div>
  </div>

  <!-- Admin Fee Harian -->
  <div class="flex items-center p-4 bg-white rounded-lg shadow-xs dark:bg-gray-800">
    <div class="p-3 mr-4 text-purple-500 bg-purple-100 rounded-full">
      🧾
    </div>
    <div>
      <p class="mb-2 text-sm text-gray-600 dark:text-gray-400">Admin Fee Hari Ini</p>
      <p class="text-lg font-semibold">
        Rp {{ number_format($adminFeeHarian,0,',','.') }}
      </p>
    </div>
  </div>

</div>


  <!-- Table APBDes -->
  <div class="w-full overflow-hidden rounded-lg shadow-md">
  <div class="w-full overflow-x-auto">
    <table class="w-full whitespace-no-wrap">
      <thead>
        <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b">
          <th class="px-4 py-3">Invoice</th>
          <th class="px-4 py-3">Game</th>
          <th class="px-4 py-3">Total</th>
          <th class="px-4 py-3">Status</th>
          <th class="px-4 py-3">Waktu</th>
        </tr>
      </thead>
      <tbody class="bg-white divide-y dark:bg-gray-800">
        @foreach($transaksiRealtime as $trx)
        <tr>
          <td class="px-4 py-3 font-semibold">{{ $trx->invoice_number }}</td>
          <td class="px-4 py-3">{{ $trx->game->name ?? '-' }}</td>
          <td class="px-4 py-3">
            Rp {{ number_format($trx->total_amount,0,',','.') }}
          </td>
          <td class="px-4 py-3">
            <span class="px-2 py-1 rounded text-black text-xs font-bold
              {{ $trx->status == 'success' ? 'bg-green-600' :
                 ($trx->status == 'pending' ? 'bg-yellow-500' : 'bg-red-600') }}">
              {{ ucfirst($trx->status) }}
            </span>
          </td>
          <td class="px-4 py-3 text-sm">
            {{ $trx->created_at->diffForHumans() }}
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endsection
