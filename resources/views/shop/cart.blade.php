@extends('layouts.store')
@section('title','Your Cart')

@section('content')
    <div class="row">
        <div class="col-lg-8 mb-3">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Your Cart</h5>
                    <button class="btn btn-sm btn-outline-danger" id="btn-empty"><i class="far fa-trash-alt"></i> Empty</button>
                </div>
                <div class="card-body p-0" id="cart-container">
                    <div class="p-3 text-center text-muted">Loading cart…</div>
                </div>
            </div>
        </div>
        <aside class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <a href="{{ route('shop.checkout.page') }}" class="btn btn-primary btn-block"><i class="fas fa-shopping-bag"></i> Proceed to Checkout</a>
                </div>
            </div>
        </aside>
    </div>
@endsection
