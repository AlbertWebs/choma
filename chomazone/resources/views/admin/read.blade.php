@extends('admin.master')

@section('content')
<div id="wrap" >
        

        <!-- HEADER SECTION -->
        @include('admin.top')
        <!-- END HEADER SECTION -->



        <!-- MENU SECTION -->
        @include('admin.left')
        <!--END MENU SECTION -->



        <!--PAGE CONTENT -->
        <div id="content">
             
            <div class="inner" style="min-height: 700px;">
                <div class="row">
                    <div class="col-lg-12">
                        <h1> Read Message </h1>
                    </div>
                </div>
                  <hr />
                 <!--BLOCK SECTION -->
                 <div class="row">
                    <div class="col-lg-12">
                        @include('admin.panel')

                    </div>

                </div>
                  <!--END BLOCK SECTION -->
                <hr />
                  
               
                  <!-- Inner Content Here -->
                 
            <div class="inner">
                

              <div class="row">
               <center>
                 @if(Session::has('message'))
							   <div class="alert alert-success">{{ Session::get('message') }}</div>
				@endif
                 </center>

                 <form class="form-horizontal" method="post"  action="{{url('/admin/reply')}}/{{$Message->id}}" enctype="multipart/form-data">
                    

                        <div class="form-group">
                            <label for="text1" class="control-label col-lg-4">Sender</label>

                            <div class="col-lg-8">
                                <input type="text" readonly id="text1" name="name" value="{{$Message->name}}" placeholder="e.g Designekta Studios" class="form-control" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="text1" class="control-label col-lg-4">Subject</label>

                            <div class="col-lg-8">
                                <input type="text" readonly id="text1" name="subject" value="{{$Message->subject}}" placeholder="e.g Designekta Studios" class="form-control" />
                            </div>
                        </div>

                         <div class="form-group">
                            <label for="text1" class="control-label col-lg-4">Sender Email</label>

                            <div class="col-lg-8">
                                <input type="text" readonly id="text1" name="email" value="{{$Message->email}}" placeholder="e.g Designekta Studios" class="form-control" />
                            </div>
                        </div>
          
                        <div class="col-lg-12">
                            <div class="box">
                                <header>
                                    <div class="icons"><i class="icon-th-large"></i></div>
                                    <h5>Message Body</h5>
                                    <ul class="nav pull-right">
                                        <li>
                                            <div class="btn-group">
                                                <a class="accordion-toggle btn btn-xs minimize-box" data-toggle="collapse"
                                                    href="#div-1">
                                                    <i class="icon-minus"></i>
                                                </a>
                                                 <button class="btn btn-danger btn-xs close-box">
                                                    <i
                                                        class="icon-remove"></i>
                                                </button>
                                            </div>
                                        </li>
                                    </ul>
                                </header>
                                <div id="div-1" class="body collapse in">
                                    
                                        <textarea readonly name="content" class="form-control" rows="10">{{$Message->content}}</textarea>

                                    
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="box">
                                <header>
                                    <div class="icons"><i class="icon-th-large"></i></div>
                                    <h5>Your Reply</h5>
                                    <ul class="nav pull-right">
                                        <li>
                                            <div class="btn-group">
                                                <a class="accordion-toggle btn btn-xs minimize-box" data-toggle="collapse"
                                                    href="#div-1">
                                                    <i class="icon-minus"></i>
                                                </a>
                                                 <button class="btn btn-danger btn-xs close-box">
                                                    <i
                                                        class="icon-remove"></i>
                                                </button>
                                            </div>
                                        </li>
                                    </ul>
                                </header>
                                <div id="div-1" class="body collapse in">
                                    
                                        <textarea name="message" id="wysihtml5" class="form-control" rows="10"></textarea>

                                    
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <br><br>
                    <div class="col-lg-12 text-center">
                      <button type="submit" class="btn btn-success"><i class="icon-check icon-white"></i> Reply</button>
                    </div>
                    
                  
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    
                <form>


            </div>
                  <!-- Inner Content Ends Here -->



                
            </div>

        </div>
        <!--END PAGE CONTENT -->

         <!-- RIGHT STRIP  SECTION -->
        @include('admin.right')
         <!-- END RIGHT STRIP  SECTION -->
    </div>

@endsection