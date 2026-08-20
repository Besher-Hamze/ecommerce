@extends('dashboard.master')
@section('content')
   

<body>
<section id="container" class="">
        <!--main content start-->
        <section id="main-content">
            <section class="wrapper">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <ul class="breadcrumb">
                                    <li>
                                        <h1>رسائل</h1>
                                    </li>
                                    <li><a>الموقع</a>
                                    </li>
                                  
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- page start-->
                <div class="row">
                    <div class="col-lg-12">
                        <section class="panel">
                            <!-- page start-->
                            <div class="row">
                                <div class="col-lg-12">
                                    <section class="panel">
                                   
                                        <!--tab nav start-->
                                        <section class="panel">
                                            <header class="panel-heading tab-bg-dark-navy-blue ">
                                           
                                            </header>
                                         
                                   @foreach($messages as $message)
                                 <div id="messages-list">
                                <p><strong>Name:</strong> {{ $message['name'] }}</p>
                                <p><strong>Email:</strong> {{ $message['email'] }}</p>
                                <p><strong>Message:</strong> {{ $message['message'] }}</p>
                                 <p><strong>Created At:</strong> {{ $message['created_at'] }}</p>
                                     </div>

                                             <hr>
                                      @endforeach

        
                                        </section>
                                    </section>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </section>

        </section>

    </section>
</body>

@endsection
