   <style>
       #nav-accordion li a {
           display: block;
           padding: 10px;
           cursor: pointer;
           font-size: 16px;
       }
   </style>
   <!--header start-->
   <header class="header white-bg">
       <div class="sidebar-toggle-box">
           <i class="fa fa-bars"></i>
       </div>
       <!--logo start-->
       <a href="index-2.html" class="logo">لوحة<span>التحكم</span></a>
       <!--logo end-->
       <div class="nav notify-row" id="top_menu">

       </div>
       <div class="top-nav ">
           <!--search & user info start-->
           <ul class="nav pull-right top-menu">
               <!-- <li>
                   <input type="text" class="form-control search" placeholder="Search">
               </li>-->
               <!-- user login dropdown start-->
               <li class="dropdown">
                   <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                       <img alt="" src="img/avatar1_small.jpg">
                       <span class="username">{{ Auth::user()->name }}</span>
                       <b class="caret"></b>
                   </a>
                   <ul class="dropdown-menu extended logout">
                       <div class="log-arrow-up"></div>
                       @auth
                       <li>
                           <a class="nav_link"
                               href="{{ route('logout') }}"
                               onclick="event.preventDefault();
                                             document.getElementById('logout-form').submit();">
                               <i class='bx bx-log-out nav_icon'></i>
                               <span class="nav_name">Log Out&nbsp; <i class="fa fa-key"></i> </span>
                           </a>

                           <form id="logout-form" action="{{ route('logout') }}" method="post"
                               style="display: none;">
                               @csrf
                           </form>
                           @endauth
                       </li>
                   </ul>
               </li>
               <li class="sb-toggle-left">
                   <i class="fa  fa-align-right"></i>
               </li>
               <!-- user login dropdown end -->
           </ul>
           <!--search & user info end-->
       </div>
   </header>
   <!--header end-->

   <!--sidebar start-->
   <aside>
       <div id="sidebar" class="nav-collapse ">
           <!-- sidebar menu start-->
           <ul class="sidebar-menu" id="nav-accordion">
               <li>
                   <a href="index-2.html">
                       <i class="fa fa-dashboard"></i>
                       <span>لوحة التحكم</span>
                   </a>
               </li>

               <li class="sub-menu">
                   <a href="javascript:;">
                       <i class="fa fa-laptop"></i>
                       <span>الاقسام</span>
                   </a>
                   <ul class="sub">
                       <li><a href="{{ route('category.index') }}">عرض الاقسام</a></li>
                       <li><a href="{{ route('category.create') }}">اضافة قسم</a></li>
                   </ul>
               </li>

               <li class="sub-menu">
                   <a href="javascript:;">
                       <i class="fa fa-book"></i>
                       <span>المنتجات</span>
                   </a>
                   <ul class="sub">
                       <li><a href="{{route('product.index')}}">عرض المنتجات</a></li>
                       <li><a href="{{route('product.create')}}">إضافة منتج</a></li>
                   </ul>
               </li>
               <li class="sub-menu">
                   <a href="javascript:;">
                       <i class="fa  fa-shopping-cart"></i>
                       <span>الطلبات</span>
                   </a>
                   <ul class="sub">
                       <li><a href="{{route('order.index')}}">عرض الطلبات</a></li>
                       <li><a href="{{route('order.create')}}">إضافة طلب </a></li>
                   </ul>
               </li>
               <li class="sub-menu">
                   <a href="javascript:;">
                       <i class="fa fa-users"></i>
                       <span>المستخدمين</span>
                   </a>
                   <ul class="sub">
                       <li><a href="{{route('user.index')}}">عرض المستخدمين</a></li>
                       <li><a href="{{route('user.create')}}">إضافة مستخدم </a></li>
                   </ul>
               </li>
               <!-- <li class="sub-menu">
                   <a href="javascript:;">
                       <i class="fa fa-tasks"></i>
                       <span>المحلات</span>
                   </a>
                   <ul class="sub">
                       <li><a href="{{route('shop.index')}}">عرض المحلات</a></li>
                       <li><a href="{{route('shop.create')}}">إضافة محل </a></li>
                   </ul>
               </li> -->
               <li>
                   <a href="{{route('brand.index')}}">
                       <i class="fa fa-dashboard"></i>
                       <span>الشركات </span>
                   </a>
               </li>
               <li>
                   <a href="{{url('admin/slider')}}">
                       <i class="fa fa-dashboard"></i>
                       <span>صور السلايدر </span>
                   </a>
               </li>
               <li>
                   <a href="{{route('contact.message')}}">
                       <i class="fa fa-dashboard"></i>
                       <span>رسائل الموقع </span>
                   </a>
               </li>
               <li>
                   <a href="{{route('setting.index')}}">
                       <i class="fa fa-dashboard"></i>
                       <span>الضبط </span>
                   </a>
               </li>
               <li>
                   <a href="{{route('admin.association-rules.index')}}">
                       <i class="fa fa-line-chart"></i>
                       <span>تحليل Apriori</span>
                   </a>
               </li>
               <li>
                   <a href="{{route('apriori.index')}}">
                       <i class="fa fa-chart-line"></i>
                       <span>Association Rules (Apriori)</span>
                   </a>
               </li>
           </ul>
           <!-- sidebar menu end-->
       </div>
   </aside>
   <!--sidebar end-->