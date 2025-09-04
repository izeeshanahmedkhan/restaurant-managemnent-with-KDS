@extends('layouts.store')
@section('title','Shop')

@section('content')
    <div class="row">
        <div class="mb-3">
            <form class="mb-3" method="get" action="{{ route('shop.index') }}">
                <div class="input-group input-group-lg">
                    <input type="text" name="keyword" class="form-control" placeholder="Search products or scan barcode" value="{{ request('keyword') }}">
                    <div class="input-group-append">
                        <button class="btn btn-primary">Search</button>
                    </div>
                </div>
            </form>

            <div class="row">
                @forelse($products ?? [] as $product)
                    <div class="col-6 col-md-3 mb-3">
                        <div class="card product-card h-100">
                            <div class="img-wrap">
                                <img src="{{$product['imageFullPath']}}" onerror="this.src='{{ asset('assets/admin/img/160x160/img2.jpg') }}'" alt="{{ $product->name }}">
                            </div>
                            <div class="card-body">
                                <h6 class="card-title mb-1">{{ $product->name }}</h6>
                                <div class="mb-2">
                                    @php
                                        $branchProduct = $product->shop_product_by_branch->first();
                                        $displayPrice = $branchProduct ? $branchProduct->price : $product->price;
                                    @endphp
                                    <span class="price">{{ \App\CentralLogics\Helpers::set_symbol($displayPrice ?? 0) }}</span>
                                    @if(!empty($product->old_price))<span class="old-price">{{ \App\CentralLogics\Helpers::set_symbol($product->old_price) }}</span>@endif
                                </div>
                                <div class="actions">
                                    <button class="btn btn-outline-primary btn-sm mr-2 quick-view" data-id="{{ $product->id }}"><i class="far fa-eye"></i> Quick view</button>
                                    <button class="btn btn-primary btn-sm add-basic" data-id="{{ $product->id }}"><i class="fas fa-cart-plus"></i> Add</button>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12"><div class="alert alert-info">No products found.</div></div>
                @endforelse
            </div>

            @if(method_exists(($products ?? null), 'links'))
                <div class="mt-3">{{ $products->appends(request()->query())->links() }}</div>
            @endif
        </div>

        <!-- Sidebar cart handles display; removed duplicate inline cart to prevent id conflicts -->
    </div>
@endsection
