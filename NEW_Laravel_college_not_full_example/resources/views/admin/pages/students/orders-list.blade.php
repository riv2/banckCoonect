@foreach($userOrders as $order)
    
    <div class="panel panel-default">
        <div class="panel-heading">
            <h5> 
                <a href="{{route('adminOrderEdit', [$order->id])}}">
                    {{ $order->name }}</a>
                     от {{ date('d-m-Y', strtotime($order->created_at)) }} 
                    <small> {{ $order->number }}</small>
            </h5>
            
        </div>
    </div>

@endforeach