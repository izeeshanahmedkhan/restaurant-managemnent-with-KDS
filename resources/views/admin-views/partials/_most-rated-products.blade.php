<div class="card-header d-flex justify-content-between gap-10">
    <h5 class="mb-0">{{translate('Most_Rated_Products')}}</h5>
    <!-- Review functionality removed -->
</div>

<div class="card-body">
    <div class="grid-item-wrap">
        @foreach($most_rated_products as $key=>$item)
            @php($product=\App\Model\Product::find($item['product_id']))
            @if(isset($product))
                <a class="grid-item text-dark" href='{{route('admin.product.view',[$item['product_id']])}}'>
                    <div class="d-flex align-items-center gap-2">
                        <img class="rounded avatar"
                                src="{{ $item->product->imageFullPath }}"
                                alt="{{$product->name}}-image">
                        <span class=" font-weight-semibold text-capitalize media-body">
                            {{isset($product)?substr($product->name,0,18) . (strlen($product->name)>18?'...':''):'not exists'}}
                        </span>
                    </div>
                    <div>
                        <!-- Review functionality removed -->
                        <span class="text-muted">{{translate('No ratings available')}}</span>
                    </div>
                </a>
            @endif
        @endforeach
    </div>
</div>
