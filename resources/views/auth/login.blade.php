<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <title>School | Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="" />
    <meta content="" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('backend/assets/images/favicon.ico')}}">

    <!-- App css -->
    <link href="{{ asset('backend/assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('backend/assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('backend/assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />
  </head>
  <body class="authentication-bg">
    <!--div class="home-btn d-none d-sm-block">
      <a href="index.html"><i class="fas fa-home h2 text-dark"></i></a>
    </div-->
    <div class="account-pages mt-5 mb-5">
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-md-8 col-lg-6 col-xl-5">
            <div class="text-center">
              <a href="index.html">
                <span>
                  <img src="{{ asset('backend/assets/images/logo-dark.png') }}" alt="" height="100">
                </span>
              </a>
            </br>
            </div>
            <div class="card">
              <div class="card-body p-4">
                <div class="text-center mb-4">
                  <h4 class="text-uppercase mt-0">Sign In</h4>
                </div>
                @if( session('success'))
                    <div class="alert alert-success pt10 pb10 mt10 mb10">
                        {{ session('success') }}
                    </div>
                @endif

                @if( session('error'))
                    <div class="alert alert-danger mt10 mb10  pt10 pb10">
                        {{ session('error') }}
                    </div>
                @endif
                <form method="POST" action="{{ route('login') }}">
                  @csrf
                  <div class="form-group mb-3">
                    <label for="emailaddress">Email address</label>
                    <input type="email" name="email" id="emailaddress" class="form-control @error('email') is-invalid @enderror" placeholder="Enter your email"/>
                    @error('email')
                      <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                      </span>
                    @enderror
                  </div>
                  <div class="form-group mb-3">
                    <label for="password">Password</label>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" id="password" placeholder="Enter your password"/>
                    <input type="hidden" name="auth_role" value="{{ \Crypt::encryptString('admin') }}">
                    @error('password')
                      <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                      </span>
                    @enderror
                  </div>
                  <div class="form-group mb-3">
                    <div class="custom-control custom-checkbox">
                      <input type="checkbox" class="custom-control-input" id="checkbox-signin" checked>
                      <label class="custom-control-label" for="checkbox-signin">Remember me</label>
                    </div>
                  </div>
                  <div class="form-group mb-0 text-center">
                    <input type="hidden" name="decice_token" id="decice_token">
                    <button class="btn btn-primary btn-block" type="submit"> Log In </button>
                  </div>
                </form>
                <!-- New registration start -->
                  <!---p class="mt-3"><a href="{{ url('register') }}" class="text-muted ml-1"><i class="fa fa-lock mr-1"></i>New Registration</a></p-->
                <!-- New registration end -->
              </div> <!-- end card-body -->

            </div>
            <!-- end card -->

            <!-- <div class="row mt-3">
                <div class="col-12 text-center">
                  <p> <a href="#" class="text-muted ml-1"><i class="fa fa-lock mr-1"></i>New Registration</a></p>
          
                </div>
            </div> -->
            <!-- end row -->
          </div> <!-- end col -->
        </div>
        <!-- end row -->
      </div>
      <!-- end container -->
    </div>
    <!-- end page -->

    <!-- Vendor js -->
    <script src="{{ asset('backend/assets/js/vendor.min.js') }}"></script>

    <!-- App js -->
    <script src="{{ asset('backend/assets/js/app.min.js') }}"></script>
  </body>
</html>