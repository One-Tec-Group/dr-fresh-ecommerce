@extends('ecommerce::frontend.layouts.master')
@section('title',trans('ecommerce::locale.product'))
@section('facebookpixel')
<!-- Facebook Pixel Code -->
    <script>
        !function (f, b, e, v, n, t, s) {
            if (f.fbq) return;
            n = f.fbq = function () {
                n.callMethod ?
                    n.callMethod.apply(n, arguments) : n.queue.push(arguments)
            };
            if (!f._fbq) f._fbq = n;
            n.push = n;
            n.loaded = !0;
            n.version = '2.0';
            n.queue = [];
            t = b.createElement(e);
            t.async = !0;
            t.src = v;
            s = b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t, s)
        }(window, document, 'script',
            'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '226602388937605');

                fbq('track', 'Contact');

        // end handles
    </script>
    <noscript>
        <img height="1" width="1"
             src="https://www.facebook.com/tr?id=226602388937605&ev=PageView
&noscript=1"/>
    </noscript>
    <!-- End Facebook Pixel Code -->
@endsection
@section('content')

 <!-- Contact Us -->
 <section class="section-padding">
    <div class="container">
       <div class="row">
          <div class="col-lg-4 col-md-4">
             <h3 class="mt-1 mb-5">@lang('ecommerce::master.Get_In_Touch')</h3>
             <h6 class="text-dark"><i class="mdi mdi-home-map-marker"></i> @lang('ecommerce::master.Address') :</h6>
             <p> 6 اكتوبر - الحي الرابع - المجاورة الثانية - 320 شارع الخزان</p>
             {{-- <h6 class="text-dark"><i class="mdi mdi-phone"></i> @lang('ecommerce::master.Phone') :</h6>
             <p>+91 12345-67890, (+91) 123 456 7890</p> --}}
             <h6 class="text-dark"><i class="mdi mdi-deskphone"></i> @lang('ecommerce::master.Mobile') :</h6>
             <p>(+20)1113502132</p>
             <h6 class="text-dark"><i class="mdi mdi-email"></i> @lang('ecommerce::master.Email') :</h6>
             <p>doctorfresh9@gmail.com</p>
             <h6 class="text-dark"><i class="mdi mdi-link"></i> @lang('ecommerce::master.Website') :</h6>
             <p>http://dr-fresh.net</p>
             <div class="footer-social"><span>Follow : </span>
                <a href="https://www.facebook.com/D.freshofficial.eg" target="_blank"><i class="mdi mdi-facebook"></i></a>
                {{-- <a href="#"><i class="mdi mdi-twitter"></i></a> --}}
                <a href="https://instagram.com/d.freshofficial?igshid=olkvjx36ce6o" target="_blank"><i class="mdi mdi-instagram"></i></a>
                {{-- <a href="#"><i class="mdi mdi-google"></i></a> --}}
             </div>
          </div>
          <div class="col-lg-8 col-md-8">
             <div class="card">
                <div class="card-body">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m23!1m12!1m3!1d3456.8158340167174!2d30.918992715514978!3d29.955975529700424!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!4m8!3e0!4m0!4m5!1s0x14585673058f34df%3A0x11dab3042ebf8a66!2s323%20Street%2015%2C%20First%206th%20of%20October%2C%20Giza%20Governorate!3m2!1d29.955970899999997!2d30.9211814!5e0!3m2!1sen!2seg!4v1621778545285!5m2!1sen!2seg" width="100%" height="450" frameborder="0" style="border:0" allowfullscreen loading="lazy"></iframe>


                </div>
             </div>
          </div>
       </div>
    </div>
 </section>
 <!-- End Contact Us -->

 @endsection
