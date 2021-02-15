<footer class="footer main-footer col-12">
    <div class="row text-center font-size12">

        <div class="col-12">
            @guest
            @else
                <a href={{ route('callBack') }} > {{__('Order a call')}} </a>
                @endguest
        </div>
        <div class="col-12">
            <a href={{ route('errorReport') }}> {{__('Notify problem')}} </a>
        </div>
        <div class="col-md-12">
            <a href="{{ route('payMemo') }}">{{ __('Online payment')}}
                <img src="/images/pay/cloudpayments.png" style="max-height: 30px;"><img src="/images/pay/visa-master.png" style="max-height: 30px;">
            </a>
        </div>
        <div class="col-12">
            <!-- Copyright -->
            <div class="footer-copyright text-center py-3">© 2018 - {{date('Y')}} Copyright:
                <a href="https://miras.app"> Miras.app</a>
            </div>
            <!-- Copyright -->
        </div>

    </div>
</footer>