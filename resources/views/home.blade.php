@extends('layouts.admin')

@section('title')
    <title>Dashboard</title>
@endsection

@section('content')
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
              <div class="col-sm-6">
                <h1 class="m-0 text-dark">Dashboard</h1>
              </div><!-- /.col -->
              <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                  <li class="breadcrumb-item"><a href="#">Home</a></li>
                  <li class="breadcrumb-item active">Dashboard</li>
                </ol>
              </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
        <div class="container">
          <!-- Small boxes (Stat box) -->
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                      <h3 class="card-title">
                        <i class="fas fa-globe mr-1"></i>
                        Aktivitas Toko
                      </h3>
                    </div><!-- /.card-header -->
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-3 col-6">
                                <!-- small box -->
                                <div class="small-box bg-info">
                                  <div class="inner">
                                    <h4>Rp {{ number_format($orders[0]->turnover) }}</h4>
                    
                                    <p>Keseluruhan Omset</p>
                                  </div>
                                  <div class="icon">
                                      <i class="ion ion-stats-bars"></i>
                                  </div>
                                </div>
                              </div>
                              <!-- ./col -->
                              <div class="col-lg-3 col-6">
                                <!-- small box -->
                                <div class="small-box bg-success">
                                  <div class="inner">
                                    <h4>{{ $customers->count() }}</h4>
                    
                                    <p>Customer</p>
                                  </div>
                                  <div class="icon">
                                      <i class="ion ion-person-add"></i>
                                  </div>
                                </div>
                              </div>
                              <!-- ./col -->
                              <div class="col-lg-3 col-6">
                                <!-- small box -->
                                <div class="small-box bg-warning">
                                  <div class="inner">
                                    <h4>{{ $categories->count() }}</h4>
                    
                                    <p>Kategori Produk</p>
                                  </div>
                                  <div class="icon">
                                    <i class="ion ion-pricetag"></i>
                                  </div>
                                </div>
                              </div>
                              <!-- ./col -->
                              <div class="col-lg-3 col-6">
                                <!-- small box -->
                                <div class="small-box bg-danger">
                                  <div class="inner">
                                    <h4>{{ $products->count() }}</h4>
                    
                                    <p>Produk</p>
                                  </div>
                                  <div class="icon">
                                    <i class="ion ion-bag"></i>
                                  </div>
                                </div>
                              </div>
                              <!-- ./col -->
                        </div>
                        <div class="row">
                          <div class="col-lg-3 col-6">
                              <!-- small box -->
                              <div class="small-box bg-info">
                                <div class="inner">
                                  <h4>{{ $orders[0]->newOrder }}</h4>
                  
                                  <p>Orderan Baru</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-stats-bars"></i>
                                </div>
                              </div>
                            </div>
                            <!-- ./col -->
                            <div class="col-lg-3 col-6">
                              <!-- small box -->
                              <div class="small-box bg-success">
                                <div class="inner">
                                  <h4>{{ $orders[0]->processOrder }}</h4>
                  
                                  <p>Order sedang diproses</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-bag"></i>
                                </div>
                              </div>
                            </div>
                            <!-- ./col -->
                            <div class="col-lg-3 col-6">
                              <!-- small box -->
                              <div class="small-box bg-warning">
                                <div class="inner">
                                  <h4>{{ $orders[0]->shipping }}</h4>
                  
                                  <p>Orderan dikirim</p>
                                </div>
                                <div class="icon">
                                  <i class="ion ion-bag"></i>
                                </div>
                              </div>
                            </div>
                            <!-- ./col -->
                            <div class="col-lg-3 col-6">
                              <!-- small box -->
                              <div class="small-box bg-danger">
                                <div class="inner">
                                  <h4>{{ $orders[0]->completeOrder }}</h4>
                  
                                  <p>Orderan Selesai</p>
                                </div>
                                <div class="icon">
                                  <i class="ion ion-bag"></i>
                                </div>
                              </div>
                            </div>
                            <!-- ./col -->
                      </div>

                    </div>
                </div>
            </div>

        </div>
    </section>
  </div>
  <!-- /.content-wrapper -->
@endsection
