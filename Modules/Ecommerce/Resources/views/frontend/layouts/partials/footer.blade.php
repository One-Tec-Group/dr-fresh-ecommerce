<section class="section-padding bg-white border-top">
    <div class="container">
       <h2 class="text-center mb-5" style="color: #364a15;">لماذا د.فريش؟</h2>
       <div class="row">
          <div class="col-lg-4 co-md-6 col-sm-12 mb-5">
             <div class="feature-box">
                <i class="mdi mdi-truck-fast mb-2"></i>
                <h5 class="font-weight-bold mb-0">توصيل مجاني!</h5>
                <h6>طلبك هيوصل  في أكتوبر في أقل من 90 دقيقة</h6> 
             </div>
          </div>
          <div class="col-lg-4 co-md-6 col-sm-12 mb-5">
             <div class="feature-box">
                <i class="mdi mdi-basket mb-2"></i>
                <h5 class="font-weight-bold mb-0">جودة عالمية بأسعار محلية</h5>
                 <h6>منتجاتنا  معاملة في مزارعنا معاملات زراعية نظيفة</h6> 
             </div>
          </div>
          <div class="col-lg-4 co-md-6 col-sm-12 mb-5">
             <div class="feature-box">
                <i class="mdi mdi-tag-heart mb-2"></i>
                <h5 class="font-weight-bold mb-0">عروض يومية وخصومات</h5>
                 <h6>عندنا هتلاقي عروض جديدة كل يوم</h6> 
             </div>
          </div>
       </div>
    </div>
 </section>
<!-- Footer -->
<!-- <footer>
   <div style="width:50px">
      <img class="w-100" src="{{asset('frontend/images/logo.png')}}" alt="">
   </div> -->
</footer>
<!--<section class="section-padding footer bg-white border-top">
    <div class="container">
       <div class="row">
          <div class="col-lg-3 col-md-3">
             <h4 class="mb-5 mt-0"><a class="logo" href="{{url('ecommerce')}}"><img src="{{asset('frontend/images/logo.png')}}" class="w-50" alt="DR-Fresh"></a></h4>
             {{--<p class="mb-0"><a class="text-dark" href="#"><i class="mdi mdi-phone"></i>+20 111 350 2132,+20 120 385 5962</a></p>--}}
             <p class="mb-0"><a class="text-success" href="#"><i class="mdi mdi-email"></i> info@onetecgroup.com</a></p>
             <p class="mb-0"><a class="text-primary" href="https://onetecgroup.com/"><i class="mdi mdi-web"></i> www.onetecgroup.com</a></p>
          </div>
          <div class="col-lg-2 col-md-2">
             <h6 class="mb-4">@lang('ecommerce::locale.categories')</h6>
             <ul>
                @forelse(\App\Category::where('business_id', config('constants.business_id'))->paginate(5) as $category)
                  <li><a href="{{url('ecommerce/products?category='.$category->id)}}">{{$category->name ?? ''}}</a></li>
                   @empty
                @endforelse
             <ul>
          </div>
          {{--<div class="col-lg-2 col-md-2">--}}
             {{--<h6 class="mb-4">ABOUT US</h6>--}}
             {{--<ul>--}}
             {{--<li><a href="#">Company Information</a></li>--}}
             {{--<li><a href="#">Careers</a></li>--}}
             {{--<li><a href="#">Store Location</a></li>--}}
             {{--<li><a href="#">Affillate Program</a></li>--}}
             {{--<li><a href="#">Copyright</a></li>--}}
             {{--<ul>--}}
          {{--</div>--}}
       <!--   <div class="col-lg-3 col-md-3">
             <h6 class="mb-4">Download App</h6>
             <div class="app">
                <a href="#"><img src="{{ asset('asset_them/img/google.png')}}" alt=""></a>
                <a href="#"><img src="{{ asset('asset_them/img/apple.png')}}" alt=""></a>
             </div>
             <h6 class="mb-3 mt-4">GET IN TOUCH</h6>
             <div class="footer-social">
                <a target="_blank" class="btn-facebook" href="https://www.facebook.com/D.freshofficial.eg/"><i class="mdi mdi-facebook"></i></a>
                {{--<a target="_blank" class="btn-twitter" href="#"><i class="mdi mdi-twitter"></i></a>--}}
                <a target="_blank" class="btn-instagram" href="https://instagram.com/d.freshofficial?igshid=olkvjx36ce6o"><i class="mdi mdi-instagram"></i></a>
                {{--<a target="_blank" class="btn-whatsapp" href="#"><i class="mdi mdi-whatsapp"></i></a>--}}
                {{--<a target="_blank" class="btn-messenger" href="#"><i class="mdi mdi-facebook-messenger"></i></a>--}}
                {{--<a target="_blank" class="btn-google" href="#"><i class="mdi mdi-google"></i></a>--}}
             </div>
          </div>
       </div>
    </div>
 </section>
 <section class="section-padding footer bg-white">
   <div class="footer-logo text-center">
      <a href="{{url('ecommerce')}}">
         <img src="{{asset('frontend/images/logo.png')}}" alt="logo">
      </a>
      <div class="footer-links">
         <div class="footer-nav-item">
            <a href="{{url('ecommerce')}}" class="footer-link">الرئيسية</a>
         </div>
         {{--<div class="footer-nav-item">--}}
            {{--<a href="" class="footer-link">من نحن</a>--}}
         {{--</div>--}}
         {{--<div class="footer-nav-item">--}}
            {{--<a href="" class="footer-link">الأقسام</a>--}}
         {{--</div>--}}
         <!-- <div class="footer-nav-item hovered-footer-link position-relative">
            <div href="">الأقسام</div>
            <div class="categorized-footer-links">
               <a href="" class="categorized-footer-link">خضراوات</a>
               <a href="" class="categorized-footer-link">فواكه</a>
               <a href="" class="categorized-footer-link">الورقيات</a>
               <a href="" class="categorized-footer-link">العسل</a>
               <a href="" class="categorized-footer-link">المكسرات</a>
            </div>
         </div> -->
         <div class="footer-nav-item">
            <a href="{{url('contact')}}" class="footer-link">اتصل بنا</a>
         </div>
      </div>
   </div>
</section> 
 <!-- End Footer -->
 <!-- Copyright -->
 <section class="pt-4 pb-4 footer-bottom">
    <div class="container">
       <div class="row no-gutters">
          <div class="col-lg-6 col-sm-6">
             <p class="mt-1 mb-0"> <strong>جميع الحقوق محفوظة لدي  {{date('Y')}} </strong> <strong class="text-dark">&copy;<a href="http://dr-fresh.net" target="_blank" class="text-primary">DR.Fresh</a></strong>
             <strong class="mt-1 mb-0">تم التطوير والبرمجة بواسطة <i class="mdi mdi-heart text-danger"></i>  <a href="https://onetecgroup.com/" target="_blank" class="text-primary">OneTecGroup L.L.C</a>
             </strong>
             </p>
          </div>
          {{--<div class="col-lg-6 col-sm-6 text-right">--}}
             {{--<img alt="osahan logo" src="{{ asset('asset_them/img/payment_methods.png')}}">--}}
          {{--</div>--}}
       </div>
    </div>
 </section>
 <!-- End Copyright -->
