@extends('layouts.ecommerce')

@section('title')
    <title>Wishlists - Ecommerce</title>
@endsection

@section('content')
    <!--================Home Banner Area =================-->
	<section class="banner_area">
		<div class="banner_inner d-flex align-items-center">
			<div class="container">
				<div class="banner_content text-center">
					<h2>Wishlists</h2>
					<div class="page_link">
                        <a href="{{ url('/') }}">Home</a>
                        <a href="{{ route('customer.wishlist') }}">WishLists</a>
					</div>
				</div>
			</div>
		</div>
	</section>
	<!--================End Home Banner Area =================-->

	<!--================Login Box Area =================-->
	<section class="login_box_area p_120">
		<div class="container">
			<div class="row">
				<div class="col-md-3">
					@include('layouts.ecommerce.module.sidebar')
				</div>
				<div class="col-md-9">
                    <div class="row">
						<div class="col-md-12">
							<div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Wishlists</h4>
                                </div>
								<div class="card-body">
                                    @if (session('success'))
                                        <div class="alert alert-success">{{ session('success') }}</div>
                                    @endif 
                                    
                                    @if(session('error'))
                                        <div class="alert alert-danger">{{ session('error') }}</div>
                                    @endif
									<div class="table-responsive">
                                    <table class="table table-hover table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Nama Produk</th>
                                                <th>Gambar</th>
                                                <th>Harga</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($wishlists as $row) 
                                            <tr>
                                                <td>{{ $row->product->name }}</td>
                                                <td>
                                                    <img src="{{ asset('storage/products/' . $row->product->image) }}" width="100px" height="100px" alt="{{ $row->product->image }} ">
                                                </td>
                                                <td>Rp. {{ number_format($row->product->price) }}</td>
                                                <td>
                                                    <form action="{{ route('customer.deleteWishlist', $row->id) }}" onsubmit="return confirm('Kamu Yakin Menghapus Produk ini dari Daftar Wishlist ?');" method="post">
                                                        @csrf
                                                        @method('DELETE')
                                                        <a href="{{ url('/product/'. $row->product->slug) }}" class="btn btn-primary btn-sm mr-1">Lihat Produk</a>
                                                        <button class="btn btn-danger btn-sm">Hapus</button>
                                                    </form>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="7" class="text-center">Tidak ada Wishlist</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                <div class="float-right">
                                    {!! $wishlists->links() !!}
                                </div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
@endsection