<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('pageTitle')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta content="A fully featured admin theme which can be used to build CRM, CMS, etc." name="description" />
    <meta content="Coderthemes" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('backend/assets/images/favicon.ico') }}" />

    <!--Morris Chart-->
    <link rel="stylesheet" href="{{ asset('backend/assets/libs/morris-js/morris.css') }}" />

    <!-- third party css -->
    <link href="{{ asset('backend/assets/libs/datatables/dataTables.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('backend/assets/libs/datatables/responsive.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('backend/assets/libs/datatables/buttons.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('backend/assets/libs/datatables/select.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <!-- third party css end -->

    <!-- App css -->
    <link href="{{ asset('backend/assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('backend/assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('backend/assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />

    <link rel="stylesheet" type="text/css" href="{{ asset('backend/css/select2.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('backend/css/toastr.min.css') }}">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link href="{{ asset('backend/css/style.css') }}" rel="stylesheet" type="text/css" />
  </head>

  <body>
    <!-- Begin page -->
    <div id="wrapper">
      <?php 
        $user = \App\User::where('id',Auth::User()->id)->get()->first(); 
        //dd($user); 
      ?>
              
      <!-- Topbar Start -->
      <div class="navbar-custom">
        <ul class="list-unstyled topnav-menu float-right mb-0">
          <li class="d-none d-sm-block">
            <form class="app-search">
              <div class="app-search-box">
              </div>
            </form>
          </li>

          <li class="dropdown notification-list">
            <div class="dropdown-menu dropdown-menu-right dropdown-lg">
              <!-- item-->
              <div class="dropdown-item noti-title">
                <h5 class="m-0">
                  <span class="float-right">
                    <a href="" class="text-dark">
                      <small>Clear All</small>
                    </a>
                  </span>
                  Notification
                </h5>
              </div>

              <div class="slimscroll noti-scroll">
                <!-- item-->
                <a href="javascript:void(0);" class="dropdown-item notify-item active">
                  <div class="notify-icon">
                    <img src="{{ asset('backend/assets/images/users/user-1.jpg') }} " class="img-fluid rounded-circle" alt="" />
                  </div>
                  <p class="notify-details">Cristina Pride</p>
                  <p class="text-muted mb-0 user-msg">
                    <small>Hi, How are you? What about our next meeting</small>
                  </p>
                </a>

                <!-- item-->
                <a href="javascript:void(0);" class="dropdown-item notify-item">
                  <div class="notify-icon bg-primary">
                    <i class="mdi mdi-comment-account-outline"></i>
                  </div>
                  <p class="notify-details">
                    Caleb Flakelar commented on Admin
                    <small class="text-muted">1 min ago</small>
                  </p>
                </a>

                <!-- item-->
                <a href="javascript:void(0);" class="dropdown-item notify-item">
                  <div class="notify-icon">
                    <img src="{{ asset('backend/assets/images/users/user-4.jpg') }}" class="img-fluid rounded-circle" alt="" />
                  </div>
                  <p class="notify-details">Karen Robinson</p>
                  <p class="text-muted mb-0 user-msg">
                    <small>Wow ! this admin looks good and awesome design</small>
                  </p>
                </a>

                <!-- item-->
                <a href="javascript:void(0);" class="dropdown-item notify-item">
                  <div class="notify-icon bg-warning">
                    <i class="mdi mdi-account-plus"></i>
                  </div>
                  <p class="notify-details">
                    New user registered.
                    <small class="text-muted">5 hours ago</small>
                  </p>
                </a>

                <!-- item-->
                <a href="javascript:void(0);" class="dropdown-item notify-item">
                  <div class="notify-icon bg-info">
                    <i class="mdi mdi-comment-account-outline"></i>
                  </div>
                  <p class="notify-details">
                    Caleb Flakelar commented on Admin
                    <small class="text-muted">4 days ago</small>
                  </p>
                </a>

                <!-- item-->
                <a href="javascript:void(0);" class="dropdown-item notify-item">
                  <div class="notify-icon bg-secondary">
                    <i class="mdi mdi-heart"></i>
                  </div>
                  <p class="notify-details">
                    Carlos Crouch liked
                    <b>Admin</b>
                    <small class="text-muted">13 days ago</small>
                  </p>
                </a>
              </div>

              <!-- All-->
              <a href="javascript:void(0);" class="dropdown-item text-center text-primary notify-item notify-all">
                View all
                <i class="fi-arrow-right"></i>
              </a>
            </div>
          </li>

          <li class="dropdown notification-list">
            <a class="nav-link dropdown-toggle nav-user mr-0 waves-effect" data-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
              <img src="{{ asset('backend/images/users/user.jpg') }}" alt="user-image" class="rounded-circle" />
              <span class="pro-user-name ml-1"> {{ Auth::user()->first_name }} <i class="mdi mdi-chevron-down"></i> </span>
            </a>
            <div class="dropdown-menu dropdown-menu-right profile-dropdown">
              <!-- item-->
              <div class="dropdown-header noti-title">
                <h6 class="text-overflow m-0">Welcome {{ Auth::user()->first_name }}</h6>
              </div>

              <!-- item-->
              <a href="{{ url('logout') }}" class="dropdown-item notify-item">
                <i class="fe-log-out"></i>
                <span>Logout</span>
              </a>
            </div>
          </li>
        </ul>

        <!-- LOGO -->
        <div class="logo-box">
          <a href="{{ url('/admin/dashboard') }}" class="logo text-center">
            <span class="logo-lg">
              <img src="{{ asset('backend/assets/images/logo-dark.png') }}" alt="" height="50" />
              <!-- <span class="logo-lg-text-light">Xeria</span> -->
            </span>
            <span class="logo-sm">
              <!-- <span class="logo-sm-text-dark">X</span> -->
              <img src="{{ asset('backend/assets/images/logo-sm.png') }}" alt="" height="24" />
            </span>
          </a>
        </div>

        <ul class="list-unstyled topnav-menu topnav-menu-left m-0">
          <li>
            <button class="button-menu-mobile disable-btn waves-effect">
              <i class="fe-menu"></i>
            </button>
          </li>

          <li>
            <h4 class="page-title-main">Dashboard</h4>
          </li>
        </ul>
      </div>
      <!-- end Topbar -->

      <!-- ========== Left Sidebar Start ========== -->
      <div class="left-side-menu">
        <div class="slimscroll-menu">
          <!-- User box -->
          <div class="user-box text-center">
            <img src="{{ asset('backend/images/users/user.jpg') }}" alt="user-img" title="Mat Helme" class="rounded-circle img-thumbnail avatar-lg" />
            <div class="dropdown">
              <a href="#" class="text-dark dropdown-toggle h5 mt-2 mb-1 d-block" data-toggle="dropdown">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</a>
              <div class="dropdown-menu user-pro-dropdown">
                <!-- item-->
                <a href="javascript:void(0);" class="dropdown-item notify-item">
                  <i class="fe-user mr-1"></i>
                  <span>My Account</span>
                </a>

                <!-- item-->
                <a href="javascript:void(0);" class="dropdown-item notify-item">
                  <i class="fe-settings mr-1"></i>
                  <span>Settings</span>
                </a>

                <!-- item-->
                <a href="javascript:void(0);" class="dropdown-item notify-item">
                  <i class="fe-lock mr-1"></i>
                  <span>Lock Screen</span>
                </a>

                <!-- item-->
                <a href="{{ url('logout') }}" class="dropdown-item notify-item">
                  <i class="fe-log-out mr-1"></i>
                  <span>Logout</span>
                </a>
              </div>
            </div>
            <!--p class="text-muted">Admin Head</p-->
            <ul class="list-inline">
              <!--li class="list-inline-item">
                <a href="#" class="text-muted">
                  <i class="mdi mdi-settings"></i>
                </a>
              </li-->

              <li class="list-inline-item">
                <a href="{{ url('logout') }}" class="text-custom">
                  <i class="mdi mdi-power"></i>
                </a>
              </li>
            </ul>
          </div>

          <!--- Sidemenu -->
          <div id="sidebar-menu">
            <ul class="metismenu" id="side-menu">
              <li>
                <a href="{{ url('/delivery/dashboard') }}">
                  <i class="mdi mdi-view-dashboard"></i>
                  <span> Dashboard </span>
                </a>
              </li>

              <li>
                <a href="{{ url('delivery/profile')}}">
                  <i class="fas fa-user-edit"></i>
                  <span> Edit Profile </span>
                </a>
              </li>

              <li>
                <a href="{{ url('delivery/upgrade/profile')}}">
                  <i class="fas fa-shopping-bag"></i>
                  <span>Upgrade account</span>
                </a>
              </li>

              <!-- <li>
                <a href="{{ url('delivery/orders')}}">
                  <i class="fas fa-shopping-bag"></i>
                  <span> Orders </span>
                </a>
              </li> -->
              <li>
                <a href="javascript: void(0);">
                    <i class="fab fa-first-order"></i>
                    <span> Orders </span>
                    <span class="menu-arrow"></span>
                </a>
                <ul class="nav-second-level" aria-expanded="false">
                  <li><a href="{{ url('delivery/orders')}}">All orders</a></li>
                  <li><a href="{{ url('delivery/orders/completed')}}">Completed orders</a></li>
                  <li><a href="{{ url('delivery/orders/cancelled')}}">Cancelled orders </a></li>
                </ul>
              </li> 
              <!--li>
                <a href="{{ url('delivery/reports')}}">
                  <i class="fas fa-shopping-bag"></i>
                  <span> Delivery Reports </span>
                </a>
              </li>

              <li>
                <a href="{{ url('delivery/returns')}}">
                  <i class="fas fa-shopping-bag"></i>
                  <span> Return Management </span>
                </a>
              </li-->

            </ul>
          </div>
          <!-- End Sidebar -->

          <div class="clearfix"></div>
        </div>
        <!-- Sidebar -left -->
      </div>
      <!-- Left Sidebar End -->

      <!-- ============================================================== -->
      <!-- Start Page Content here -->
      <!-- ============================================================== -->

      <div class="content-page">
        <div class="content">
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
          <!--Begin::Content-->
          @yield('content')
          <!--End::Content-->
        </div>
        <!-- content -->

        <!-- Footer Start -->
        <footer class="footer">
          <div class="container-fluid">
            <div class="row">
              <div class="col-md-6"></div>
              <div class="col-md-6">
                <div class="text-md-right footer-links d-none d-sm-block">
                 
                </div>
              </div>
            </div>
          </div>
        </footer>
        <!-- end Footer -->
      </div>

      <!-- ============================================================== -->
      <!-- End Page content -->
      <!-- ============================================================== -->
    </div>
    <!-- END wrapper -->

    <!-- Right Sidebar -->
    <div class="right-bar">
      <div class="rightbar-title">
        <a href="javascript:void(0);" class="right-bar-toggle float-right">
          <i class="dripicons-cross noti-icon"></i>
        </a>
        <h4 class="m-0 text-white">Settings</h4>
      </div>
      <div class="slimscroll-menu">
        <!-- User box -->
        <div class="user-box">
          <div class="user-img">
            <img src="{{ asset('backend/images/users/user.jpg') }}" alt="user-img" title="Mat Helme" class="rounded-circle img-fluid" />
            <a href="javascript:void(0);" class="user-edit"><i class="mdi mdi-pencil"></i></a>
          </div>

          <h5><a href="javascript: void(0);">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</a></h5>
          <p class="text-muted mb-0"><small>Admin Head</small></p>
        </div>

        <!-- Settings -->
        <hr class="mt-0" />
        <h5 class="pl-3">Basic Settings</h5>
        <hr class="mb-0" />

        <div class="p-3">
          <div class="checkbox checkbox-primary mb-2">
            <input id="Rcheckbox1" type="checkbox" checked />
            <label for="Rcheckbox1">
              Notifications
            </label>
          </div>
          <div class="checkbox checkbox-primary mb-2">
            <input id="Rcheckbox2" type="checkbox" checked />
            <label for="Rcheckbox2">
              API Access
            </label>
          </div>
          <div class="checkbox checkbox-primary mb-2">
            <input id="Rcheckbox3" type="checkbox" />
            <label for="Rcheckbox3">
              Auto Updates
            </label>
          </div>
          <div class="checkbox checkbox-primary mb-2">
            <input id="Rcheckbox4" type="checkbox" checked />
            <label for="Rcheckbox4">
              Online Status
            </label>
          </div>
          <div class="checkbox checkbox-primary mb-0">
            <input id="Rcheckbox5" type="checkbox" checked />
            <label for="Rcheckbox5">
              Auto Payout
            </label>
          </div>
        </div>

        <!-- Timeline -->
        <hr class="mt-0" />
        <h5 class="pl-3 pr-3">Messages <span class="float-right badge badge-pill badge-danger">25</span></h5>
        <hr class="mb-0" />
        <div class="p-3">
          <div class="inbox-widget">
            <div class="inbox-item">
              <div class="inbox-item-img"><img src="{{ asset('backend/assets/images/users/user-2.jpg') }}" class="rounded-circle" alt="" /></div>
              <p class="inbox-item-author"><a href="javascript: void(0);" class="text-dark">Tomaslau</a></p>
              <p class="inbox-item-text">I've finished it! See you so...</p>
            </div>
            <div class="inbox-item">
              <div class="inbox-item-img"><img src="{{ asset('backend/assets/images/users/user-3.jpg') }}" class="rounded-circle" alt="" /></div>
              <p class="inbox-item-author"><a href="javascript: void(0);" class="text-dark">Stillnotdavid</a></p>
              <p class="inbox-item-text">This theme is awesome!</p>
            </div>
            <div class="inbox-item">
              <div class="inbox-item-img"><img src="{{ asset('backend/assets/images/users/user-4.jpg') }}" class="rounded-circle" alt="" /></div>
              <p class="inbox-item-author"><a href="javascript: void(0);" class="text-dark">Kurafire</a></p>
              <p class="inbox-item-text">Nice to meet you</p>
            </div>

            <div class="inbox-item">
              <div class="inbox-item-img"><img src="{{ asset('backend/assets/images/users/user-5.jpg') }}" class="rounded-circle" alt="" /></div>
              <p class="inbox-item-author"><a href="javascript: void(0);" class="text-dark">Shahedk</a></p>
              <p class="inbox-item-text">Hey! there I'm available...</p>
            </div>
            <div class="inbox-item">
              <div class="inbox-item-img"><img src="{{ asset('backend/assets/images/users/user-6.jpg') }}" class="rounded-circle" alt="" /></div>
              <p class="inbox-item-author"><a href="javascript: void(0);" class="text-dark">Adhamdannaway</a></p>
              <p class="inbox-item-text">This theme is awesome!</p>
            </div>
          </div>
          <!-- end inbox-widget -->
        </div>
        <!-- end .p-3-->
      </div>
      <!-- end slimscroll-menu-->
    </div>
    <!-- /Right-bar -->

    <!-- Right bar overlay-->
    <div class="rightbar-overlay"></div>

    <script type="text/javascript">var ajaxurl = "{{ url('/') }}"</script>
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="{{ asset('backend/dist/js/lightbox-plus-jquery.min.js') }}"></script>
    <script src="https://code.jquery.com/ui/1.11.0/jquery-ui.js"></script>
    <!-- <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js" ></script> -->
    <script src="{{ asset('backend/js/jquery-3.2.1.min.js') }}"></script>
    <script src="{{ asset('backend/js/toastr.min.js') }}"></script>

    <script type="text/javascript" src="{{ asset('backend/js/jquery-ui.min.js')}}"></script>   
    <script type="text/javascript" src="{{ asset('backend/js/bootstrap-datepicker.min.js')}}"></script>   
    <script type="text/javascript">      
      var $j = jQuery.noConflict();
        $j("#datepicker1").datepicker({
          format: "yyyy-mm-dd"
        }); 
        $j("#datepicker2").datepicker({
          format: "yyyy-mm-dd"
        });
        $j('.datepicker').datepicker({
            format: "yyyy-mm-dd"
        });
    </script>   
    <!-- Vendor js -->
    <script src="{{ asset('backend/assets/js/vendor.min.js') }}"></script>

    <!-- knob plugin -->
    <script src="{{ asset('backend/assets/libs/jquery-knob/jquery.knob.min.js') }}"></script>

    <!--Morris Chart-->
    <!---script src="{{ asset('backend/assets/libs/morris-js/morris.min.js') }}"></script-->
    <script src="{{ asset('backend/assets/libs/raphael/raphael.min.js') }}"></script>

    <!-- Dashboard init js-->
    <!---script src="{{ asset('backend/assets/js/pages/dashboard.init.js') }}"></script--->

    <!-- Plugins js-->
    <script src="{{ asset('backend/assets/libs/twitter-bootstrap-wizard/jquery.bootstrap.wizard.min.js') }}"></script>

    <!-- Init js-->
    <script src="{{ asset('backend/assets/js/pages/form-wizard.init.js') }}"></script>

    <!-- App js -->
    <script src="{{ asset('backend/assets/js/app.min.js') }}"></script>

    <script src="{{ asset('backend/js/select2.min.js') }}"></script>

    <!-- third party js -->
    <script src="{{ asset('backend/assets/libs/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('backend/assets/libs/datatables/dataTables.bootstrap4.js') }}"></script>
    <script src="{{ asset('backend/assets/libs/datatables/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('backend/assets/libs/datatables/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('backend/assets/libs/datatables/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('backend/assets/libs/datatables/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('backend/assets/libs/datatables/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('backend/assets/libs/datatables/buttons.flash.min.js') }}"></script>
    <script src="{{ asset('backend/assets/libs/datatables/buttons.print.min.js') }}"></script>
    <script src="{{ asset('backend/assets/libs/datatables/dataTables.keyTable.min.js') }}"></script>
    <script src="{{ asset('backend/assets/libs/datatables/dataTables.select.min.js') }}"></script>
    <script src="{{ asset('backend/assets/libs/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ asset('backend/assets/libs/pdfmake/vfs_fonts.js') }}"></script>
    <!--script src="{{ asset('backend/assets/libs/dropzone/dist/dropzone.js') }}"></script-->
    <!---link href="{{ asset('backend/assets/libs/dropzone/dist/dropzone.css') }}"-->

    <script src="{{ asset('backend/assets/libs/pdfmake/vfs_fonts.js') }}"></script>
    <!-- third party js ends -->

    <!-- Datatables init -->
    <script src="{{ asset('backend/assets/js/pages/datatables.init.js') }}"></script> 
    <script src="{{asset('vendor/jquery-validation/dist/jquery.validate.js')}}"></script>
    <script src="{{asset('vendor/jquery-validation/dist/additional-methods.js')}}"></script>
    <script type="text/javascript" src="{{ asset('ckeditor/ckeditor.js') }}"></script>
    <!--script type="text/javascript" src="{{ asset('backend/js/dropzone.js') }}"></script--->
    <script type="text/javascript" src="{{ asset('backend/vendor/dropzone/dist/dropzone.js') }}"></script>
    <script type="text/javascript">Dropzone.autoDiscover = false;</script>
    <script type="text/javascript" src="{{ asset('backend/js/dropify.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('backend/js/admin.js')}}"></script> 

  </body>

</html>