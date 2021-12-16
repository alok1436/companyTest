@extends('vendor.layouts.default')
@section('pageTitle', 'Vendor | Promo Code Offers') 
@section('content')

<div class="container-fluid">
  <div class="row">
    <div class="col-12">
      <div class="card-box">
        <div class="dropdown float-right">
          <a href="{{ url('vendor/promoCodeOffer/store') }}" class="btn btn-primary btn-rounded"><i class="fa fa-plus"></i> Add New Promo Code Offer</a>
        </div>
        <h4 class="mt-0 mb-3 header-title">Pomo Code Offers</h4>

        <table id="datatable" class="table">
          <thead>
            <tr>
              <th>#</th>
              <th>Name</th>
              <th>Email</th>
              <th class="text-center">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php $i=1; ?>
            @foreach($promoCodeOffers as $promoCodeOffer)
            <tr>
              <td>{{ $i++ }}</td>
              <td>{{ $promoCodeOffer->name }}</td>
              <td>{{ $promoCodeOffer->slug }}</td>
              <td>
                @if($promoCodeOffer->promoCodeOfferstatus=='Enable')
                  <a href="#" class="badge badge-success">Enable</a>
                @else
                  <a href="#" class="badge badge-danger">Disable</a>
                @endif
              </td>
              <td>
                <div class="dropdown text-center">
                  <a href="#" class="dropdown-toggle arrow-none card-drop" data-toggle="dropdown" aria-expanded="false">
                      <i class="mdi mdi-dots-vertical"></i>
                  </a>
                  <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item" href="{{ url('vendor/promoCodeOffer/edit/'.$promoCodeOffer->id) }}">
                      <i class="fas fa-user-edit"></i> Edit
                    </a>
                    <a class="dropdown-item" href="{{ url('vendor/promoCodeOffer/delete/'.$promoCodeOffer->id) }}" onclick="return confirm('Are you sure?')">
                      <i class="fas fa-trash"></i> Delete
                    </a>
                  </div>
                </div>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div> 
  <!-- end row -->
</div>

@endsection