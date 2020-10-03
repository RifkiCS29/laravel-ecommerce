@extends('layouts.admin')

@section('title')
    <title>Detail Pesanan</title>
@endsection

@section('content')
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
              <div class="col-sm-6">
                <h1 class="m-0 text-dark">Detail Order</h1>
              </div><!-- /.col -->
              <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                  <li class="breadcrumb-item"><a href="#">Home</a></li>
                  <li class="breadcrumb-item active">Detail Order</li>
                </ol>
              </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
        <div class="container">
            <div class="row">
                <!-- BAGIAN INI AKAN MENG-HANDLE TABLE LIST PRODUCT  -->
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">
                                Detail Pesanan
                            </h4>
                            <!-- TOMBOL UNTUK MENERIMA PEMBAYARAN -->
                            <div class="float-right">
                                <!-- TOMBOL INI HANYA TAMPIL JIKA STATUSNYA 1 DARI ORDER DAN 0 DARI PEMBAYARAN -->
                                @if ($order->status == 1 && $order->payment->status == 0)
                                <a href="{{ route('orders.approve_payment', $order->invoice) }}" class="btn btn-primary btn-sm">Terima Pembayaran</a>
                                @endif
                            </div>
                        </div>
                        <div class="card-body">
                            @if (session('success'))
                                <div class="alert alert-success">{{ session('success') }}</div>
                            @endif

                            @if (session('error'))
                                <div class="alert alert-danger">{{ session('error') }}</div>
                            @endif
                            <div class="row">
                                <!-- BLOCK UNTUK MENAMPILKAN DATA PELANGGAN -->
                                <div class="col-md-6">
                                    <h4>Detail Pelanggan</h4>
                                    <table class="table table-bordered">
                                        <tr>
                                            <th width="30%">Nama Pelanggan</th>
                                            <td>{{ $order->customer_name }}</td>
                                        </tr>
                                        <tr>
                                            <th>Telp</th>
                                            <td>{{ $order->customer_phone }}</td>
                                        </tr>
                                        <tr>
                                            <th>Email</th>
                                            <td>{{ $order->customer->email }}</td>
                                        </tr>
                                        <tr>
                                            <th>Alamat</th>
                                            <td>{{ $order->customer_address }} {{ $order->customer->district->name }} - {{  $order->customer->district->city->name}}, {{ $order->customer->district->city->province->name }}</td>
                                        </tr>
                                        <tr>
                                            <th>Order Status</th>
                                            <td>{!! $order->status_label !!}</td>
                                        </tr>
                                        <!-- FORM INPUT RESI HANYA AKAN TAMPIL JIKA STATUS LEBIH BESAR 1 -->
                                        @if ($order->status > 1)
                                        <tr>
                                            <th>Nomor Resi</th>
                                            <td>
                                                @if ($order->status == 2)
                                                <form action="{{ route('orders.shipping') }}" method="post">
                                                    @csrf
                                                    <div class="input-group">
                                                        <input type="hidden" name="order_id" value="{{ $order->id }}">
                                                        <input type="text" name="tracking_number" placeholder="Masukkan Nomor Resi" class="form-control" required>
                                                        <div class="input-group-append">
                                                            <button class="btn btn-secondary" type="submit">Kirim</button>
                                                        </div>
                                                    </div>
                                                </form>
                                                @else
                                                {{ $order->tracking_number }}
                                                @endif
                                            </td>
                                        </tr>
                                        @endif
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h4>Detail Pembayaran</h4>
                                    @if ($order->status != 0)
                                    <table class="table table-bordered">
                                        <tr>
                                            <th width="30%">Nama Pengirim</th>
                                            <td>{{ $order->payment->name }}</td>
                                        </tr>
                                        <tr>
                                            <th>Bank Tujuan</th>
                                            <td>{{ $order->payment->transfer_to }}</td>
                                        </tr>
                                        <tr>
                                            <th>Tanggal Transfer</th>
                                            <td>{{ $order->payment->transfer_date }}</td>
                                        </tr>
                                        <tr>
                                            <th>Bukti Pembayaran</th>
                                            <td><a target="_blank" href="{{ asset('storage/payment/' . $order->payment->proof) }}">Lihat</a></td>
                                        </tr>
                                        <tr>
                                            <th>Status</th>
                                            <td>
                                                {!! $order->payment->status_label !!} <br>
                                            </td>
                                        </tr>
                                        @if($order->return_count == 1)
                                        <tr>
                                            <th>Return Status</th>
                                            <td> 
                                                {!! $order->return->status_label !!} 
                                            </td>
                                        </tr>
                                        @endif
                                    </table>
                                    @else
                                    <h5 class="text-center">Belum Konfirmasi Pembayaran</h5>
                                    @endif
                                </div>
                                <div class="col-md-12">
                                    <h4>Detail Produk</h4>
                                    <table class="table table-borderd table-hover">
                                        <tr>
                                            <th>Produk</th>
                                            <th>Harga</th>
                                            <th>Quantity</th>
                                            <th>Berat</th>
                                        </tr>
                                        @foreach ($order->details as $row)
                                        <tr>
                                            <td>{{ $row->product->name }}</td>
                                            <td>Rp {{ number_format($row->price) }}</td>
                                            <td>{{ $row->qty }}</td>
                                            <td>{{ $row->weight }} gr</td>
                                        </tr>
                                        @endforeach
                                        <tr>
                                            <td class="text-center">Subtotal</td>
                                            <td colspan="3" class="text-center">Rp. <b>{{ number_format($order->subtotal) }}</b></td>
                                        </tr>
                                        <tr>
                                            <td class="text-center">Kurir <b>{{ $order->shipping }}</b></td>
                                            <td colspan="3" class="text-center">Rp. <b>{{ number_format($order->cost) }}</b></td>
                                        </tr>
                                        <tr>
                                            <td class="text-center"><b>TOTAL</b></td>
                                            <td colspan="3" class="text-center"><b> Rp. {{ number_format($order->total) }}</b></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
  </div>
  <!-- /.content-wrapper -->
@endsection