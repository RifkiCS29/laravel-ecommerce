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
                                            <th>Alasan Return</th>
                                            <td>{{ $order->return->reason }}</td>
                                        </tr>
                                        <tr>
                                            <th>Rekening Pengembalian Dana</th>
                                            <td>{{ $order->return->refund_transfer }}</td>
                                        </tr>
                                        <tr>
                                            <th>Status</th>
                                            <td>{!! $order->return->status_label !!}</td>
                                        </tr>
                                    </table>
                                    
                                    @if ($order->return->status == 0)
                                    <form action="{{ route('orders.approve_return') }}" onsubmit="return confirm('Kamu Yakin ?');" method="post">
                                        @csrf
                                        <div class="input-group mb-3">
                                            <input type="hidden" name="order_id" value="{{ $order->id }}">
                                            <select name="status" class="form-control" required>
                                                <option value="">Pilih</option>
                                                <option value="1">Terima</option>
                                                <option value="2">Tolak</option>
                                            </select>
                                            <div class="input-group-prepend">
                                                <button class="btn btn-primary btn-sm">Proses Return</button>
                                            </div>
                                        </div>
                                    </form>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <h4>Foto Barang Return</h4>
                                    <img src="{{ asset('storage/returns/' . $order->return->photo) }}" class="img-responsive" height="200" alt="">
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