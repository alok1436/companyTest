@extends('admin.layouts.default') 
@section('pageTitle', 'Admin | Edit Banner') 
@section('content')
<div class="container-fluid">
  <div class="row">
    <div class="col-md-12">
        <div class="card-box">
            <h4 class="m-t-0 header-title">Edit Banner</h4>
            {{ Form::open(array('url' => 'admin/banner/edit/'.$banner->id, 'files' => true, 'class' => '','id'=>'form')) }}
              <div class="form-row">
                <div class="form-group col-md-6">
                  <label class="col-form-label">Title</label>
                  {{Form::text('title',$banner->title,array('class'=>'form-control' )) }}
                </div>
                <div class="form-group col-md-6">
                  <label class="col-form-label">Sub Title</label>
                  {{Form::text('sub_title',$banner->sub_title,array('class'=>'form-control' )) }}
                  {{Form::hidden('product_id',$banner->id,array('id'=>'product_id' )) }}
                </div>
              </div>
              <div class="form-row">
                <div class="form-group col-md-6">
                  <label class="col-form-label">Slug</label>
                  {{Form::text('slug',$banner->slug,array('class'=>'form-control' )) }}
                </div>
                <div class="form-group col-md-6">
                  <label class="col-form-label">Image</label>
                  {{Form::file('image',array('class'=>'form-control' )) }}
                  @if(isset($banner->image) && $banner->image !=null)
                  <img width="50" height="50" src="{{ $banner->image }}" class="rounded-circle" />
                  @endif
                </div>
                <div class="form-group col-md-6">
                  <label class="col-form-label">Banner Type</label>
                  {{Form::select('offer_type',['brand'=>'Brand','category'=>'Category'],$banner->offer_type,array('class'=>'form-control required discount_type','id'=>'offer_type')) }}
                </div>
                <div class="form-group col-md-6" id="brand_row" style="display: {{ $banner->offer_type == 'brand' ? '' : 'none' }}">
                  <label class="col-form-label">Brands</label>
                  {{Form::select('brand_id[]',$brands,$banner->brands->pluck('id'),array('class'=>'form-control')) }}
                </div>  
              </div>
              <div class="form-row" id="category_row" style="display: {{ $banner->offer_type == 'category' ? '' : 'none' }}">
                <div class="form-group col-md-4">
                  <label>Level 1 Category<span class="red">*</span></label>
                  {{Form::select('category_level_1',$category,$banner->categories,array('class'=>'form-control selectCategory','id'=>'level_1','data-level'=>1,'data-type'=>'banner')) }}
                </div>
                <div class="form-group col-md-4">
                  <label>Level 2 Category<span class="red">*</span></label>
                  {{Form::select('category_level_2',[],null,array('class'=>'form-control level_subcategory_2','id'=>'level_2','data-level'=>2,'data-type'=>'banner')) }}
                </div>
                <div class="form-group col-md-4">
                  <label>Level 3 Category<span class="red">*</span></label>
                  {{Form::select('category_level_3',[],null,array('class'=>'form-control level_subcategory_3','id'=>'level_3','data-level'=>3,'data-type'=>'banner')) }}
                </div>                
              </div> 
              <div class="form-row">
                <div class="form-group col-md-6">
                  <label class="col-form-label">Video</label>
                  {{Form::file('video',array('class'=>'form-control')) }}
                  @if(isset($banner->video) && $banner->video !=null)
                  <video width="400" height="200" controls>
                    <source src="{{ $banner->video }}" type="video/mp4">
                  </video>
                  <a class="btn btn-danger" href="{{ url('admin/banner/removeVideo/'.$banner->id) }}" onclick="return confirm('Are you sure?')">
                    <i class="fas fa-trash"></i> Remove Video
                  </a>
                  @endif
                </div>
              </div> 
              <button type="submit" class="btn btn-primary">SUBMIT</button>
            {{ Form::close() }}
        </div>
    </div>
  </div>
</div>
@endsection