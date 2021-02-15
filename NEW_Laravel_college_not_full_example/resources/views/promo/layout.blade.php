<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Styles -->
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('assets/css/bootstrap-select.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('assets/css/bootstrap-datetimepicker.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('assets/css/font-awesome.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('assets/css/fontawesome-stars.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/bxslider/4.2.12/jquery.bxslider.css">

    <style type="text/css">
        
        .nav-pills>li.active>a, .nav-pills>li.active>a:focus, .nav-pills>li.active>a:hover {
            
            border-radius: 0px;

            color: #333;
            background-color: #f9f9f9;
        }
        

        .nav-pills>li.active>a, .nav-pills>li.active>a:focus {
            background: rgba(255, 255, 255, 0);
            color: #f9f9f9;
        }

        a {
            color: #333;
        }
        a:focus, a:hover {
            color: #555;
        }
        .tab-pane hr-off {
          border-bottom: 4px solid #333;
        }
        .page-footer .col-style {
            /*border-bottom: 4px solid #333;*/
            padding: 20px 20px;
        }

        .page-footer .col-style:not(:last-child) {
            /*border-bottom: 4px solid #333;*/
        }
        @media only screen and (max-width: 767px) {
            .page-footer .col-style:not(:last-child) {
                border-right: none;
            }
        }
        .btn-primary {
            border-radius: 0px;
            background: #555;
            border-color: #333;
        }
        .btn-primary:hover, .btn-primary.active, .btn-primary:active, .btn-primary:focus, .btn-primary.active.focus, .btn-primary.active:focus, .btn-primary.active:hover, .btn-primary:active.focus, .btn-primary:active:focus, .btn-primary:active:hover, .open>.dropdown-toggle.btn-primary.focus, .open>.dropdown-toggle.btn-primary:focus, .open>.dropdown-toggle.btn-primary:hover {
            background: #777;
            border-color: #333;
        }
        .back-to-top {
            cursor: pointer;
            position: fixed;
            bottom: 20px;
            right: 20px;
            display:none;
            z-index: 999;
        }
        ul.side-menu li{
            background-color: #fff;
            margin-top: 0px;
        }
        ul.side-menu li.active {
            background: rgba(255,255,255,0.0);

        }
        .side-menu-off {
          border: 4px solid #333;
        }

        .bx-wrapper { margin: 60px auto; }

        .bx-wrapper {
          moz-box-shadow: unset;
          -webkit-box-shadow: unset;
          box-shadow: unset;
          border: unset;
          background: unset;
        }

        .gradient-bottom-border {
          --borderWidth: 4px;
          background: #fff;
          position: relative;
          margin-bottom: 30px;
        }
        .gradient-bottom-border:after {
          content: '';
          position: absolute;
          top:0px;
          height: calc(100% + var(--borderWidth) * 1);
          width: calc(100% + var(--borderWidth) * 0);
          background: linear-gradient(223deg, #ffc125, #ff4e4e, #ff61a5, #e919ff);
          
          z-index: -1;
          animation: animatedgradient 5s ease alternate infinite;
          background-size: 300% 300%;
        }

        .gradient-border, .bx-wrapper {
          --borderWidth: 4px;
          position: relative;
        }
        .gradient-border:after, .bx-wrapper:after {
          content: '';
          position: absolute;
          top: calc(-1 * var(--borderWidth));
          left: calc(-1 * var(--borderWidth));
          height: calc(100% + var(--borderWidth) * 2);
          width: calc(100% + var(--borderWidth) * 2);
          background: linear-gradient(223deg, #ffc125, #ff4e4e, #ff61a5, #e919ff);
          z-index: -1;
          animation: animatedgradient 5s ease alternate infinite;
          background-size: 300% 300%;
        }

        /*.nav-pills>li.active>a, .nav-pills>li.active>a:focus, .nav-pills>li.active>a:hover {
          background: linear-gradient(223deg, #ffc125, #ff4e4e, #ff61a5, #e919ff);
          animation: animatedgradient 5s ease alternate infinite;
          background-size: 300% 300%;
        }*/


        @keyframes animatedgradient {
          0% {
            background-position: 0% 50%;
          }
          50% {
            background-position: 100% 50%;
          }
          100% {
            background-position: 0% 50%;
          }
        }

        #svg-logo {height: 30vh; margin: -40px auto; margin-bottom: -30px; fill:#11f;}
        .cls-1 {fill: url('#gradient')}

        animate {
            -webkit-transition: .5s ease;
            transition: .5s ease;
        }

        #stop1 {
            animation: change-color1 5s ease infinite;
        }
        #stop3 {
            animation: change-color2 5s ease infinite;
        }
        @keyframes change-color1 {
            00% {stop-color: #e919ff;}
            50% {stop-color: #ff4e4e;}
            100% {stop-color: #e919ff;}
        }
        @keyframes change-color2 {
            0% {stop-color: #ff4e4e;}
            50% {stop-color: #ffc125;}
            100% {stop-color: #ff4e4e;}
        }
        .tab-content {
            margin-top: 20px;
        }
        a.phone-link {
            text-decoration: underline;
        }
    </style>
    

</head>
<body>
    <div id="app">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                  <a href="#">
                    

                    <svg id="svg-logo" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 850 850">
                       <defs>
                               <linearGradient id="gradient" x1="0" y1="50%"   gradientUnits="userSpaceOnUse">
                                   <stop id="stop1" offset="10%" stop-color="#ff4e4e"></stop>
                                   <stop id="stop2" offset="50%" stop-color="#ff4e4e"></stop>
                                   <stop id="stop3" offset="70%" stop-color="#ffc125;"></stop>
                         </linearGradient>
                       </defs>
                         <g id="miras-logo" fill="url(#gradient)">
                           <polygon  points="361.77 374.15 382.23 426.91 404.05 426.91 424.51 374.15 424.51 443.82 454.61 443.82 454.61 325.44 418.76 325.44 393.06 390.72 367.52 325.44 361.77 325.44 331.5 325.44 331.5 443.82 361.77 443.82 361.77 374.15"/>
                           <rect  x="461.37" y="325.45" width="30.27" height="118.37"/>
                           <path  d="M528.5,405.6H537L565,443.82h50.73l4.9-17.08h37.88l4.9,17.08h32.81l-3.95-12.12c8.15,10.15,21.7,14.66,36.92,14.66,23.84,0,42.78-12.18,42.78-36.36,0-24.52-21-32.64-37.37-37.88-10.48-3.55-18.6-6.26-18.6-12.85,0-4.23,3.38-7.61,10.65-7.61,8.29,0,12,4.06,12,10.82h30.1c0-27.73-19.79-39.57-42.11-39.57-22,0-40.92,12.18-40.92,36.36,0,24.86,21,33.14,37.54,38.39,10.15,3.21,18.6,5.92,18.6,11.84,0,4.57-3.55,8.12-12.51,8.12-10,0-14.88-3.38-14.88-11.67H684.24c0,.37,0,.72,0,1.08l-26.57-81.57H621.5L587.9,428.61l-21.35-28.76c12.18-6.76,20-18.94,20-33.48,0-23.84-18.6-40.92-42.61-40.92H498.23V443.82H528.5Zm111.1-45.83,11.67,40.92H627.93Zm-111.1-6.43h15.39c7.1,0,12.34,5.24,12.34,13,0,7.95-5.24,13-12.34,12.85H528.5Z"/>
                           <polygon points="338.14 495.99 366.59 495.99 366.59 489.73 338.14 489.73 338.14 466.21 368.96 466.21 368.96 459.95 338.14 459.95 331.5 459.95 331.5 526.34 334.35 526.34 369.44 526.34 369.44 520.08 338.14 520.08 338.14 495.99"/>
                           <path d="M401.59,460h-27.5v66.39h27.5c18.3,0,31.3-14.89,31.3-33.19S419.89,460,401.59,460Zm0,60.13H380.72V466.21h20.87c14.8,0,24.85,12,24.85,26.93S416.38,520.08,401.59,520.08Z"/>
                           <path d="M475,506.62c0,9.58-6.54,14.42-16.12,14.42s-16.12-4.84-16.12-14.42V460h-6.64v46.66c0,13.56,10.15,20.87,22.76,20.87s22.76-7.3,22.76-20.87V460H475Z"/>
                           <path d="M544,505.48c-3.51,9.2-13,15.55-23.9,15.55C503.35,521,492,508.61,492,493.15s11.38-27.88,28.17-27.88c10.91,0,20.39,6.35,23.9,15.55h7.21c-4-12.9-16.5-22-31.11-22-20.39,0-34.81,15.36-34.81,34.33s14.42,34.33,34.81,34.33c14.61,0,27.12-9.1,31.11-22Z"/>
                           <path d="M571.91,460,546.4,526.34h7L559.58,510h31.49l6.16,16.31h7L578.74,460Zm-10,43.82,13.37-35.57,13.37,35.57Z"/>
                           <polygon points="595.62 459.95 595.62 466.21 614.12 466.21 614.12 526.34 620.75 526.34 620.75 466.21 639.25 466.21 639.25 459.95 620.75 459.95 595.62 459.95"/>
                           <rect x="642" y="459.95" width="6.64" height="66.39"/>
                           <path d="M687.62,458.82a34.33,34.33,0,1,0,34.33,34.33A34.08,34.08,0,0,0,687.62,458.82Zm0,62.22a27.88,27.88,0,1,1,27.69-27.88A27.47,27.47,0,0,1,687.62,521Z"/>
                           <polygon points="764.53 513.07 732.28 459.95 726.59 459.95 726.59 526.34 733.23 526.34 733.23 473.23 765.48 526.34 771.16 526.34 771.16 459.95 764.53 459.95 764.53 513.07"/>
                           <polygon points="78.39 325.44 78.39 380.94 190.72 443.04 302.14 381.44 302.14 325.95 190.72 387.55 78.39 325.44"/>
                           <polygon points="78.39 402.43 78.39 457.91 190.72 520.01 302.14 458.42 302.14 402.92 190.72 464.52 78.39 402.43"/>
                           <polygon points="302.14 527.48 302.14 474.8 206.85 527.48 302.14 527.48"/>
                           <polygon points="78.39 527.48 174.59 527.48 78.39 474.3 78.39 527.48"/>
                         </g>
                     </svg>

                  </a>
                  <div class="pull-right" style="margin-top: 20px;">
                    @if ( Lang::locale() == 'kz' )
                      <a href="{{@getLangURI('ru')}}">Рус</a>
                    @else
                      <a href="{{@getLangURI('kz')}}">Каз</a>
                    @endif
                  </div>
                </div>
                

                <div class="col-sm-12">
                    @if(Session::has('flash_message'))
                        <div class="alert alert-success">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            {{ Session::get('flash_message') }}
                        </div>
                    @endif
                    <div class="message">
                        @if (count($errors) > 0)
                            <div class="alert alert-danger">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>

                @include("promo.content-".Lang::locale())
            </div>
        </div>


        <p> </p>
        <p> </p>
           {{-- @yield('content') --}}

        <div class="carousel container">
          <div class="slider">
            <div><img src="{{ URL::asset('assets/img/promo/1.jpg') }}"></div>
            <div><img src="{{ URL::asset('assets/img/promo/2.jpg') }}"></div>
            <div><img src="{{ URL::asset('assets/img/promo/3.jpg') }}"></div>
            <div><img src="{{ URL::asset('assets/img/promo/7.jpg') }}"></div>
            <div><img src="{{ URL::asset('assets/img/promo/4.jpg') }}"></div>
            <div><img src="{{ URL::asset('assets/img/promo/11.jpg') }}"></div>
            <div><img src="{{ URL::asset('assets/img/promo/5.jpg') }}"></div>
            <div><img src="{{ URL::asset('assets/img/promo/8.jpg') }}"></div>
            <div><img src="{{ URL::asset('assets/img/promo/6.jpg') }}"></div>
            <div><img src="{{ URL::asset('assets/img/promo/10.jpg') }}"></div>
            <div><img src="{{ URL::asset('assets/img/promo/12.jpg') }}"></div>
            <div><img src="{{ URL::asset('assets/img/promo/13.jpg') }}"></div>

          </div>
        </div>

           <a id="back-to-top" href="#" class="btn btn-primary btn-lg back-to-top" role="button" title="Click to return on the top page" data-toggle="tooltip" data-placement="left"><span class="glyphicon glyphicon-chevron-up"></span></a>


            <!-- Footer -->
            <footer class="page-footer font-small bg-secondary  footer mt-auto py-3">
            <div class="container">
                <div class="row" >

                  
                    <div class="col-sm-4 col-style text-center">
                        <a href="http://miras.edu.kz/university/index.php/kz/">{{ __('Full version site') }}</a>
                    </div>
                    <div class="col-sm-4 col-style text-center">
                        <a href="https://www.instagram.com/miras.education/" target="_blank">Инстаграм</a>
                    </div>
                    <div class="col-sm-4 col-style">
                         <!-- Copyright -->
                          <div class="footer-copyright text-center py-3">© 1997 - {{date('Y')}}
                            <a href="https://miras.app"> Miras Education</a>
                          </div>
                          <!-- Copyright -->
                    </div>
                  


              </div>
              <div class="gradient-bottom-border" style="margin-bottom: 15px;"></div>
            </div>
            </footer>
            <!-- Footer -->


            
    </div>

    <!-- Scripts -->
    
    <script src="{{ URL::asset('assets/js/jquery.min.js') }}"></script>
    <script src="{{ URL::asset('assets/js/bootstrap.min.js') }}"></script>
    <script src="{{ URL::asset('assets/js/bootstrap-select.min.js') }}"></script>
    <script src="{{ URL::asset('assets/js/moment.min.js') }}"></script>
    <script src="{{ URL::asset('assets/js/moment-locale-ru.min.js') }}"></script>
    <script src="{{ URL::asset('assets/js/bootstrap-datetimepicker.min.js') }}"></script>

    <script src="{{ URL::asset('assets/js/jquery.barrating.min.js') }}"></script>

    <script type="text/javascript">
        var langURL = '{{ App\Http\Middleware\LocaleMiddleware::getLocale() }}';
        if (langURL != '' ) langURL += '/';
    </script>

    <script type="text/javascript">
        var tab = $('.scrollable-pill li [data-toggle^=pill]');
        tab.on('click', function() {
          if( window.mobilecheck() ) {
            $('html, body').animate({
                scrollTop: parseInt($('.tab-content').offset().top-0)
            }, 800);
          }
        });

        $(document).ready(function(){
          $(window).scroll(function () {
              if ($(this).scrollTop() > 50) {
                  $('#back-to-top').fadeIn();
              } else {
                  $('#back-to-top').fadeOut();
              }
          });
          // scroll body to 0px on click
          $('#back-to-top').click(function () {
              $('#back-to-top').tooltip('hide');
              $('body,html').animate({
                  scrollTop: 0
              }, 800);
              return false;
          });
          
          $('#back-to-top').tooltip('show');

        });
    </script>

    <script>
    $(document).ready(function(){
        var slides =  $(window).width() > 767 ? 3 : 1;
        var slider;

        window.onresize = function(){
            slides =  $(window).width() > 767 ? 3 : 1;
            slider.reloadSlider({minSlides:slides, maxSlides:slides, slideWidth:350, shrinkItems: true});
        }

        slider = $('.slider').bxSlider({minSlides:slides, maxSlides:slides, slideWidth:350, shrinkItems: true});
    });
  </script>
  <script src="https://cdn.jsdelivr.net/bxslider/4.2.12/jquery.bxslider.min.js"></script>

  <script type="text/javascript">
    window.mobilecheck = function() {
  var check = false;
  (function(a){if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4))) check = true;})(navigator.userAgent||navigator.vendor||window.opera);
  return check;
};
  </script>



  <!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-7924497-32"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-7924497-32');
</script>




</body>
</html>
