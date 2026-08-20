@extends('dashboard.master')
@section('content')
                   <!--main content start-->
    <section id="main-content">
        <section class="wrapper">
            <!-- page start-->
            <div class="row">
                <div class="col-md-11">
                    <section class="panel">
                        <div class="panel-body">
                            <div class="col-md-6">
                                <div class="pro-img-details">
                                    <img src="{{asset('uploads/product/'.$detail->image_url)}}" alt=""/>
                                </div>
                                <div class="pro-img-list">
									@foreach($detail->images as $image)
                                    <a href="#">
                                        <img height="150" src="{{asset('uploads/product/'.$image->url)}}" alt="">
                                    </a>
                                  @endforeach
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h4 class="pro-d-title">
                                    <a href="#" class="">{{$detail->name}} </a>
                                </h4>

                                <p>
                                   {{$detail->description}}
                                </p>
                                <div class="product_meta">
                                    <span class="posted_in"> <strong>الصنف:</strong> {{$detail->category}}</span>
                                </div>
								<div class="product_meta">
                                    <span class="posted_in"> <strong>الشركة:</strong> {{$detail->brand_name}}</span>
                                </div>
								<div class="product_meta">
                                    <span class="posted_in"> <strong>المحل:</strong> {{$detail->shop_name}}</span>
                                </div>
                                <div class="m-bot15"><strong>السعر : </strong>
                                    <span class="pro-price">{{$detail->unit_price}} ل.س</span></div>
                               <!-- <div class="form-group">
                                    <label>Quantity</label>
                                      <span class="pro-price"> $300.00</span>
								</div>-->
                            </div>
                        </div>
                    </section>

                   <!-- <section class="panel">
                        <header class="panel-heading tab-bg-dark-navy-blue">
                            <ul class="nav nav-tabs ">
                                <li class="active">
                                    <a data-toggle="tab" href="#description">
                                        Description
                                    </a>
                                </li>
                                <li>
                                    <a data-toggle="tab" href="#reviews">
                                        Reviews
                                    </a>
                                </li>

                            </ul>
                        </header>
                        <div class="panel-body">
                            <div class="tab-content tasi-tab">
                                <div id="description" class="tab-pane active">
                                    <h4 class="pro-d-head">Product Description</h4>

                                    <p> Praesent ac condimentum felis. Nulla at nisl orci, at dignissim dolor, The best
                                        product descriptions address your ideal buyer directly and personally. The best
                                        product descriptions address your ideal buyer directly and personally. </p>

                                    <p> Praesent ac condimentum felis. Nulla at nisl orci, at dignissim dolor, The best
                                        product descriptions address your ideal buyer directly and personally. The best
                                        product descriptions address your ideal buyer directly and personally. The best
                                        product descriptions address your ideal buyer directly and personally. The best
                                        product descriptions address your ideal buyer directly and personally. </p>
                                </div>
                                <div id="reviews" class="tab-pane">
                                    <article class="media">
                                        <a class="pull-left thumb p-thumb">
                                            <img src="img/avatar-mini.jpg">
                                        </a>

                                        <div class="media-body">
                                            <a href="#" class="cmt-head">Lorem ipsum dolor sit amet, consectetur
                                                adipiscing elit.</a>

                                            <p><i class="fa fa-time"></i> 1 hours ago</p>
                                        </div>
                                    </article>
                                    <article class="media">
                                        <a class="pull-left thumb p-thumb">
                                            <img src="img/avatar-mini2.jpg">
                                        </a>

                                        <div class="media-body">
                                            <a href="#" class="cmt-head">Nulla vel metus scelerisque ante sollicitudin
                                                commodo</a>

                                            <p><i class="fa fa-time"></i> 23 mins ago</p>
                                        </div>
                                    </article>
                                    <article class="media">
                                        <a class="pull-left thumb p-thumb">
                                            <img src="img/avatar-mini3.jpg">
                                        </a>

                                        <div class="media-body">
                                            <a href="#" class="cmt-head">Donec lacinia congue felis in faucibus. </a>

                                            <p><i class="fa fa-time"></i> 15 mins ago</p>
                                        </div>
                                    </article>
                                </div>
                            </div>
                        </div>
                    </section>-->
                </div>
            </div>
            <!-- page end-->
        </section>
    </section>
    <!--main content end-->
    <!--main content end-->
@endsection
