@extends('vendor.layouts.default') 
@section('pageTitle', 'Vendor | Edit Product') 
@section('content')

<div class="container-fluid product_edit_page">
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-body" style="padding-left: 0; ">
          <h4 class="header-title mb-3" style="padding-left: 17px; "> Edit Product</h4>
          {{ Form::open(array('url' => 'vendor/product/edit/'.$product->id, 'files' => true, 'class' => '','id'=>'form', 'enctype'=>'multipart/form-data')) }}
            <div id="basicwizard">
              <div class="row">
                <div class="col-md-2">
                  <ul class="nav nav-pills bg-light" style="display: unset;">
                    <li class="nav-item">
                      <a href="#general_tab" data-toggle="tab" class="nav-link rounded-0 pt-2 pb-2">
                        <i class="mdi mdi-account-circle mr-1"></i>
                        <span class="d-none d-sm-inline">General</span>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a href="#brand_cate_tab" data-toggle="tab" class="nav-link rounded-0 pt-2 pb-2"> 
                        <i class="mdi mdi-account-circle mr-1"></i>
                        <span class="d-none d-sm-inline">Brand/Category</span>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a href="#meta_tab" data-toggle="tab" class="nav-link rounded-0 pt-2 pb-2"> 
                        <i class="mdi mdi-account-circle mr-1"></i>
                        <span class="d-none d-sm-inline">Meta</span>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a href="#inventory_tab" data-toggle="tab" class="nav-link rounded-0 pt-2 pb-2">
                        <i class="mdi mdi-face-profile mr-1"></i>
                        <span class="d-none d-sm-inline">Inventory</span>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a href="#media_tab" data-toggle="tab" class="nav-link rounded-0 pt-2 pb-2">
                        <i class="mdi mdi-face-profile mr-1"></i>
                        <span class="d-none d-sm-inline">Media</span>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a href="#attribute_tab" data-toggle="tab" class="nav-link rounded-0 pt-2 pb-2">
                        <i class="mdi mdi-checkbox-marked-circle-outline mr-1"></i>
                        <span class="d-none d-sm-inline">Attribute</span>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a href="#discount_tab" data-toggle="tab" class="nav-link rounded-0 pt-2 pb-2">
                        <i class="mdi mdi-checkbox-marked-circle-outline mr-1"></i>
                        <span class="d-none d-sm-inline">Discount</span>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a href="#pre_order_tab" data-toggle="tab" class="nav-link rounded-0 pt-2 pb-2">
                        <i class="mdi mdi-checkbox-marked-circle-outline mr-1"></i>
                        <span class="d-none d-sm-inline">Pre Order</span>
                      </a>
                    </li>
                  </ul>
                </div>
                <div class="col-md-10">
                  <div class="tab-content border-0" style="padding: 0">
                    <div class="tab-pane" id="general_tab">
                      <div class="row">
                        <div class="col-12">
                          <div class="form-row">
                            <div class="form-group col-md-6">
                              <label>Name</label>
                              {{Form::text('name',$product->name,array('class'=>'form-control')) }}
                            </div>
                            <div class="form-group col-md-6">
                              <label>Product Slug</label>
                              {{Form::text('slug',$product->slug,array('class'=>'form-control')) }}
                            </div>
                          </div>

                          <div class="form-row">
                            <?php $tags = explode(',', $product->tag_id) ?>
                            <div class="form-group col-md-6">
                              <label>Product Tags</label>
                              {{Form::select('tag_id[]',$tag,$tags,array('class'=>'form-control select2','multiple'=>'multiple')) }}
                            </div>
                            <div class="form-group col-md-6">
                              <label>Sort Order</label>
                              {{Form::number('sort_order',$product->sort_order,array('class'=>'form-control')) }}
                            </div>
                          </div>
                          <div class="form-row">
                            <div class="form-group col-md-6">
                              <label>Stock</label>
                              {{Form::text('stock',$product->stock,array('class'=>'form-control')) }}
                            </div>
                            <div class="form-group col-md-6">
                              <label>Status</label>
                              {{Form::select('status',['Enable'=>'Enable','Disable'=>'Disable'],$product->status,array('class'=>'form-control')) }}
                            </div>
                          </div>

                          <div class="form-row">
                            <div class="form-group col-md-12">
                              <label>Description</label>
                              {{Form::textarea('description',$product->description,array('class'=>'form-control','id'=>'summary-ckeditor')) }}
                            </div>
                          </div>

                        </div> <!-- end col -->
                      </div> <!-- end row -->
                    </div>

                    <div class="tab-pane" id="brand_cate_tab">
                      <div class="row">
                        <div class="col-12">
                          
                          <div class="form-row">
                            <div class="form-group col-md-6">
                              <label>Category</label>
                              {{Form::select('category_id',$category,$product->productCategories->category_id,array('class'=>'form-control selectCategory')) }}
                            </div>
                            <?php $subCategories = explode(',', $product->sub_category_id) ?>
                            <div class="form-group col-md-6">
                              <label>Sub Category</label>
                              {{Form::select('sub_category_id[]',[],$product->sub_category_id,array('class'=>'form-control select2','multiple'=>'multiple','id'=>'subCateogry')) }}
                            </div>
                          </div>

                          <div class="form-row">
                            <div class="form-group col-md-6">
                              <label>Select Brand</label>
                              {{Form::select('brand_id',$brand,$product->brand_id,array('class'=>'form-control')) }}
                            </div>
                          </div>

                        </div> <!-- end col -->
                      </div> <!-- end row -->
                    </div>

                    <div class="tab-pane" id="meta_tab">
                      <div class="row">
                        <div class="col-12">
                          <div class="form-row">
                            <div class="form-group col-md-6">
                              <label>Meta Tag Title</label>
                              {{Form::text('sco_tag_title',$product->sco_tag_title,array('class'=>'form-control')) }}
                            </div>
                            <div class="form-group col-md-6">
                              <label>Meta Tag Keywords</label>
                              {{Form::text('sco_tag_keywords',$product->sco_tag_keywords,array('class'=>'form-control')) }}
                            </div>
                          </div>

                          <div class="form-row">
                            <div class="form-group col-md-12">
                              <label>Meta Tag Description</label>
                              {{Form::textarea('sco_tag_description',$product->sco_tag_description,array('class'=>'form-control')) }}
                            </div>
                          </div>
                        </div> <!-- end col -->
                      </div> <!-- end row -->
                    </div>

                    <div class="tab-pane" id="inventory_tab">
                      <div class="row">
                        <div class="col-12">
                          <div class="form-row">
                            <div class="form-group col-md-6">
                              <label>Model (opt)</label>
                              {{Form::text('model',$product->model,array('class'=>'form-control')) }}
                            </div>
                            <div class="form-group col-md-6">
                              <label>SKU</label>
                              {{Form::text('sku',$product->skus->sku,array('class'=>'form-control')) }}
                            </div>
                          </div>

                          <div class="form-row">
                            <div class="form-group col-md-6">
                              <label>UPC (Universal Product Code)</label>
                              {{Form::text('upc',$product->upc,array('class'=>'form-control')) }}
                            </div>
                            <div class="form-group col-md-6">
                              <label>ISBN (International Standard Book Number)</label>
                              {{Form::text('isbn',$product->isbn,array('class'=>'form-control')) }}
                            </div>
                          </div>

                          <div class="form-row">
                            <div class="form-group col-md-6">
                              <label>MPN (Manufacturer Product Number)</label>
                              {{Form::text('mpn',$product->mpn,array('class'=>'form-control')) }}
                            </div>
                            <div class="form-group col-md-3">
                              <label>Regular Price</label>
                              {{Form::text('regular_price',$product->skus->regular_price,array('class'=>'form-control')) }}
                            </div>
                            <div class="form-group col-md-3">
                              <label>Sale Price</label>
                              {{Form::text('sale_price',$product->skus->sale_price,array('class'=>'form-control')) }}
                            </div>
                          </div>

                          <div class="form-row">
                            <div class="form-group col-md-6">
                              <label>Quantity</label>
                              {{Form::number('quantity',$product->skus->quantity,array('class'=>'form-control')) }}
                            </div>
                            <div class="form-group col-md-6">
                              <label>Minimum Quantity</label>
                              {{Form::number('minimum_quantity',$product->skus->minimum_quantity,array('class'=>'form-control')) }}
                            </div>
                          </div>
                        </div> <!-- end col -->
                      </div> <!-- end row -->
                    </div>

                    <div class="tab-pane" id="media_tab">
                      <div class="row">
                        <div class="col-12">
                          <div class="form-row">
                            <div class="form-group col-md-6">
                              <label>Size Chats</label>
                              {{Form::file('size_chart',array('class'=>'form-control')) }}
                              <img width="35" height="35" src="{{ asset($product->getMeta('size_chart')) }}" class="rounded-circle">
                            </div>
                            <div class="form-group col-md-6">
                              <label>Featured Image</label>
                              {{Form::file('featured_image',array('class'=>'form-control')) }}
                              <img width="35" height="35" src="{{asset($product->getMeta('featured_image')) }}" class="rounded-circle">
                            </div>
                          </div>
                          <div class="form-row">
                            <div class="form-group col-md-12">
                              <label>Video</label>
                              {{Form::file('video',array('class'=>'form-control')) }}
                            </div>
                            <video id="my-video" class="video-js" controls="controls" autoplay="false" preload="none" width="100%" height="auto" data-setup="{}" style="margin-bottom: 10px;">
                              <source src="{{ url($product->getMeta('video') ? $product->getMeta('video') : '') }}" type='video/mp4' autoplay="false" mute="true">
                            </video>
                            <!--iframe width="100%" height="auto" src="{{ url($product->getMeta('video') ? $product->getMeta('video') : '') }}"></iframe-->
                          </div>
                          <div class="form-row">
                            <div class="form-group col-md-12">
                              <label>Video Url</label>
                              {{Form::textarea('video_url',$product->getMeta('video_url'),array('class'=>'form-control','rows'=>5)) }}
                            </div>
                          </div>
                          <div class="form-row">
                            <div class="form-group col-md-12">
                              <label>Gallery Images</label>
                              <div class="dropzone" id="my-dropzone" name="mainFileUploader" style="border: 1px dashed #eee;padding: 30px;text-align: center;">
                                <div class="fallback">
                                  {{Form::file('gallery',array('multiple'=>'true')) }}
                                </div>
                              </div>
                              <div id="files_container" class="">
                                <ul class="p0" style="list-style:none;" >
                                  <?php $gallery_images = \App\ProductsImage::where('product_id',$product->id)->get();
                                  if(!empty($gallery_images)){
                                    foreach($gallery_images as $gal){?>
                                      <li class="gallery">
                                        <img class="image" src="{{ asset($gal->image) }}" style="width:100px; height:80px" />
                                        <button type="button" class="del_img c-red delete_btn" data-id="{{ $gal->id }}">
                                        <i class="fa fa-trash text-danger"></i>
                                        </button>
                                        <input class="hiddenfile" type="hidden" name="gallery_images[]" value="{{$gal->image}}" />
                                      </li>
                                  <?php 
                                    }
                                   } 
                                   ?>
                                </ul>
                              </div>
                            </div>
                          </div>
                        </div> <!-- end col -->
                      </div> <!-- end row -->
                    </div>

                    <div class="tab-pane" id="attribute_tab">
                      <div class="row">
                        <div class="col-12">
                          <div class="form-row">
                            <div class="form-group col-md-6">
                              <label>Dimensions (L x W x H)</label>
                              {{Form::text('dimensions',$product->dimensions,array('class'=>'form-control')) }}
                            </div>
                            <div class="form-group col-md-6">
                              <label>Length Class (Centimeter, Millimeter, Inch)</label>
                              {{Form::select('length_class',['Centimeter'=>'Centimeter','Millimeter'=>'Millimeter','Inch'=>'Inch'],$product->length_class,array('class'=>'form-control')) }}
                            </div>
                          </div>

                          <div class="form-row">
                            <div class="form-group col-md-6">
                              <label>Weight</label>
                              {{Form::number('weight',$product->weight,array('class'=>'form-control')) }}
                            </div>
                            <div class="form-group col-md-6">
                              <label>Weight Class (Kilogram, Gram, Pound)</label>
                              {{Form::select('weight_class',['Kilogram'=>'Kilogram','Gram'=>'Gram','Pound'=>'Pound'],$product->weight_class,array('class'=>'form-control')) }}
                            </div>
                          </div>

                          <?php $additional_attribute = $product->getMeta('additional_attribute');?>   
                          @if(!empty($additional_attribute)) 
                            <?php $key = 0; ?>
                            @foreach($additional_attribute as $k=>$v)
                            <div class="form-row html_penel" data-fieldid="{{$key}}" id="attributeId1">
                                <div class="form-group col-md-5">
                                  <label class="a_key">Key</label>
                                  {{Form::text('attribute['.$key.'][key]',$k ,array('class'=>'form-control')) }}
                                </div>
                                <div class="form-group col-md-5">
                                  <label class="a_value">Value</label>
                                  {{Form::text('attribute['.$key.'][value]',$v,array('class'=>'form-control')) }}
                                </div>
                                <div class="form-group col-md-1 addMorebtn btn-supporter">
                                  <?php echo ($key == 0) ?
                                  '<a href="javascript:void(0)" id="add_button"><i class="fa fa-plus"></i></a>'
                                    : '<a href="javascript:void(0)" class="delete_attribute_row"><i class="fa fa-trash" style="color:red"></i></a>' ?>
                                </div>
                            </div>
                             <?php $key++; ?>
                            @endforeach

                          @else 
                          <div class="form-row html_penel" data-fieldid="0" id="attributeId1">
                            <div class="form-group col-md-5">
                              <label class="a_key">Key</label>
                              {{Form::text('attribute[0][key]',null,array('class'=>'form-control')) }}
                            </div>
                            <div class="form-group col-md-5">
                              <label class="a_value">Value</label>
                              {{Form::text('attribute[0][value]',null,array('class'=>'form-control')) }}
                            </div>
                            <div class="form-group col-md-1 addMorebtn btn-supporter">
                              <a href="javascript:void(0)" id="add_button"><i class="fa fa-plus"></i></a>
                            </div>
                          </div>
                          @endif
                        </div> <!-- end col -->
                      </div> <!-- end row -->
                    </div>

                    <div class="tab-pane" id="discount_tab">
                      <div class="row">
                        <div class="col-12">
                          <div class="form-row">
                            <div class="form-group col-md-6">
                              <?php $discount = $product->getMeta('discount');?>
                              <label>Quantity</label>
                              {{Form::number('discount[quantity]',$discount['quantity'],array('class'=>'form-control')) }}
                            </div>
                            <div class="form-group col-md-3">
                              <label>Priority</label>
                              {{Form::text('discount[priority]',$discount['priority'],array('class'=>'form-control')) }}
                            </div>
                            <div class="form-group col-md-3">
                              <label>Price</label>
                              {{Form::text('discount[price]',$discount['price'],array('class'=>'form-control')) }}
                            </div>
                          </div>

                          <div class="form-row">
                             <div class="form-group col-md-6">
                              <label>End Date </label>
                              {{Form::date('discount[start_date]',$discount['start_date'],array('class'=>'form-control')) }}
                            </div>
                            <div class="form-group col-md-6">
                              <label>Start Date </label>
                              {{Form::date('discount[end_date]',$discount['end_date'],array('class'=>'form-control')) }}
                            </div>
                          </div>
                        </div> <!-- end col -->
                      </div> <!-- end row -->
                    </div>

                    <div class="tab-pane" id="pre_order_tab">
                      <div class="row">
                        <div class="col-12">
                          <div class="form-row">
                            <div class="form-group col-md-6">
                              <label>Date Available</label>
                              {{Form::date('date_available',null,array('class'=>'form-control')) }}
                            </div>
                          </div>

                        </div> <!-- end col -->
                      </div> <!-- end row -->
                    </div>
                    <!-- <ul class="list-inline wizard mb-0">
                      <li class="previous list-inline-item">
                        <a href="javascript: void(0);" class="btn btn-secondary">Previous</a>
                      </li>
                      <li class="next list-inline-item float-right">
                        <a href="javascript: void(0);" class="btn btn-secondary">Next</a>
                      </li>
                    </ul> -->
                  </div> <!-- tab-content -->
                </div>
              </div>
            </div> <!-- end #basicwizard-->
            <button type="submit" class="btn btn-primary btn-rounded float-right">SUBMIT</button>
          {{ Form::close() }}
        </div> <!-- end card-body -->
      </div> <!-- end card-->
    </div> <!-- end col -->
  </div>
</div>
<!-- <div class="dropzone" id="dropzone"> </div> -->
<style type="text/css">
.addMorebtn {
  margin-top: 30px;
  text-align: center;
  font-size: 19px;
}
</style>
@endsection