{{-- Order Card Partial for KDS --}}
@php
    $statusClass = match($order->order_status) {
        'pending', 'confirmed', 'processing' => 'new',
        'cooking' => 'cooking',
        'done', 'completed' => 'done',
        default => 'new'
    };
    
    $statusText = match($order->order_status) {
        'pending' => 'New',
        'confirmed' => 'Confirmed',
        'processing' => 'Processing',
        'cooking' => 'Cooking',
        'done', 'completed' => 'Done',
        default => ucfirst($order->order_status)
    };
    
    $timeAgo = \Carbon\Carbon::parse($order->created_at)->diffForHumans();
    $orderItems = $order->details ?? collect();
@endphp

<div class="kds-card kds-card--clickable" data-order-id="{{ $order->id }}" data-order-data="{{ json_encode($order) }}">
    <div class="kds-card__header">
        <h3 class="kds-card__number">#{{ $order->id }}</h3>
        <div class="kds-card__status">
            <span class="kds-chip kds-chip--{{ $statusClass }}">{{ $statusText }}</span>
        </div>
    </div>
    
    <div class="kds-card__meta">
        @if($order->delivery_man_id && $order->delivery_man)
            <div class="kds-card__token">Delivery: {{ $order->delivery_man->f_name ?? 'Assigned' }}</div>
        @elseif($order->customer_id && $order->customer)
            <div class="kds-card__token">Customer: {{ $order->customer->f_name ?? 'Guest' }}</div>
        @elseif($order->token)
            <div class="kds-card__token">Token: {{ $order->token }}</div>
        @endif
        
        <div class="kds-card__time">
            <i class="fas fa-clock"></i>
            {{ $timeAgo }}
        </div>
    </div>
    
    <div class="kds-card__items">
        @if($orderItems->count() > 0)
            @foreach($orderItems->take(3) as $item)
                <div class="kds-card__item">
                    <span class="kds-card__item-quantity">{{ $item->quantity ?? 1 }}</span>
                    <span class="kds-card__item-name">{{ $item->product->name ?? $item->product_name ?? 'Unknown Item' }}</span>
                    
                    @php
                        // Parse variations for display
                        $variations = $item->variation ?? [];
                        $variationText = '';
                        if (is_string($variations)) {
                            try {
                                $variationData = json_decode($variations, true);
                                if (is_array($variationData)) {
                                    $variationLabels = [];
                                    foreach ($variationData as $variation) {
                                        if (isset($variation['values']) && is_array($variation['values'])) {
                                            foreach ($variation['values'] as $value) {
                                                if (isset($value['label'])) {
                                                    $variationLabels[] = $value['label'];
                                                }
                                            }
                                        }
                                    }
                                    $variationText = implode(', ', $variationLabels);
                                }
                            } catch (\Exception $e) {
                                $variationText = '';
                            }
                        }
                        
                        // Parse addons for display
                        $addons = $item->add_on_ids ?? [];
                        $addonText = '';
                        if (is_string($addons)) {
                            try {
                                $addonIds = json_decode($addons, true);
                                if (is_array($addonIds)) {
                                    $addonQuantities = is_string($item->add_on_qtys) ? json_decode($item->add_on_qtys, true) : $item->add_on_qtys;
                                    $addonQuantities = $addonQuantities ?: [];
                                    
                                    $addonLabels = [];
                                    foreach ($addonIds as $index => $addonId) {
                                        $addon = \App\Model\AddOn::find($addonId);
                                        if ($addon) {
                                            $quantity = $addonQuantities[$index] ?? 1;
                                            $addonLabels[] = $addon->name . ($quantity > 1 ? ' (x' . $quantity . ')' : '');
                                        }
                                    }
                                    $addonText = implode(', ', $addonLabels);
                                }
                            } catch (\Exception $e) {
                                $addonText = '';
                            }
                        }
                    @endphp
                    
                    @if($variationText)
                        <div class="kds-card__item-variations">{{ $variationText }}</div>
                    @endif
                    
                    @if($addonText)
                        <div class="kds-card__item-addons">+ {{ $addonText }}</div>
                    @endif
                </div>
            @endforeach
            
            @if($orderItems->count() > 3)
                <div class="kds-card__item">
                    <span class="kds-card__item-name">+{{ $orderItems->count() - 3 }} more items</span>
                </div>
            @endif
        @else
            <div class="kds-card__item">
                <span class="kds-card__item-name">No items</span>
            </div>
        @endif
    </div>
    
    <div class="kds-card__actions">
        @switch($order->order_status)
            @case('pending')
            @case('confirmed')
            @case('processing')
                <button class="kds-btn kds-btn--primary btn-mark-processing" 
                        data-id="{{ $order->id }}"
                        aria-label="Mark order #{{ $order->id }} as processing">
                    <i class="fas fa-play"></i>
                    Mark Processing
                </button>
                @break
                
            @case('cooking')
                <button class="kds-btn kds-btn--success btn-mark-done" 
                        data-id="{{ $order->id }}"
                        aria-label="Mark order #{{ $order->id }} as done">
                    <i class="fas fa-check"></i>
                    Mark Done
                </button>
                @break
                
            @case('done')
            @case('completed')
                <button class="kds-btn kds-btn--warn btn-reopen" 
                        data-id="{{ $order->id }}"
                        aria-label="Reopen order #{{ $order->id }}">
                    <i class="fas fa-undo"></i>
                    Reopen
                </button>
                @break
        @endswitch
    </div>
</div>
