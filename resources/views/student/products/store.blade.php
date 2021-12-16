@extends('vendor.layouts.default') 
@section('pageTitle', 'Vendor | Add Product') 
@section('content')

<div class="container-fluid product_store_page">
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-body" style="padding-left: 0; ">
          <h4 class="header-title mb-3" style="padding-left: 17px; "> Add New Product</h4>
          {{ Form::open(array('url' => 'vendor/product/store', 'files' => true, 'class' => '','id'=>'form', 'enctype'=>'multipart/form-data')) }}
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
                              {{Form::text('name',null,array('class'=>'form-control')) }}
                            </div>
                            <div class="form-group col-md-6">
                              <label>Product Slug</label>
                              {{Form::text('slug',null,array('class'=>'form-control')) }}
                            </div>
                          </div>

                          <div class="form-row">
                            <div class="form-group col-md-6">
                              <label>Product Tags</label>
                              {{Form::select('tag_id[]',$tag,null,array('class'=>'form-control select2','multiple'=>'multiple')) }}
                            </div>
                            <div class="form-group col-md-6">
                              <label>Sort Order</label>
                              {{Form::number('sort_order',null,array('class'=>'form-control')) }}
                            </div>
                          </div>
                          <div class="form-row">
                            <div class="form-group col-md-6">
                              <label>Stock</label>
                              {{Form::text('stock',null,array('class'=>'form-control')) }}
                            </div>
                            <div class="form-group col-md-6">
                              <label>Status</label>
                              {{Form::select('status',['Enable'=>'Enable','Disable'=>'Disable'],null,array('class'=>'form-control')) }}
                            </div>
                          </div>

                          <div class="form-row">
                            <div class="form-group col-md-12">
                              <label>Description</label>
                              {{Form::textarea('description',null,array('class'=>'form-control','id'=>'summary-ckeditor')) }}
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
                              {{Form::select('category_id',$category,null,array('class'=>'form-control selectCategory')) }}
                            </div>
                            <div class="form-group col-md-6">
                              <label>Sub Category</label>
                              {{Form::select('sub_category_id[]',[],null,array('class'=>'form-control select2','multiple'=>'multiple','id'=>'subCateogry')) }}
                            </div>
                          </div>

                          <div class="form-row">
                            <div class="form-group col-md-6">
                              <label>Select Brand</label>
                              {{Form::select('brand_id',$brand,null,array('class'=>'form-control')) }}
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
                              {{Form::text('sco_tag_title',null,array('class'=>'form-control')) }}
                            </div>
                            <div class="form-group col-md-6">
                              <label>Meta Tag Keywords</label>
                              {{Form::text('sco_tag_keywords',null,array('class'=>'form-control')) }}
                            </div>
                          </div>

                          <div class="form-row">
                            <div class="form-group col-md-12">
                              <label>Meta Tag Description</label>
                              {{Form::textarea('sco_tag_description',null,array('class'=>'form-control')) }}
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
                              {{Form::text('model',null,array('class'=>'form-control')) }}
                            </div>
                            <div class="form-group col-md-6">
                              <label>SKU</label>
                              {{Form::text('sku',null,array('class'=>'form-control')) }}
                            </div>
                          </div>

                          <div class="form-row">
                            <div class="form-group col-md-6">
                              <label>UPC (Universal Product Code)</label>
                              {{Form::text('upc',null,array('class'=>'form-control')) }}
                            </div>
                            <div class="form-group col-md-6">
                              <label>ISBN (International Standard Book Number)</label>
                              {{Form::text('isbn',null,array('class'=>'form-control')) }}
                            </div>
                          </div>

                          <div class="form-row">
                            <div class="form-group col-md-6">
                              <label>MPN (Manufacturer Product Number)</label>
                              {{Form::text('mpn',null,array('class'=>'form-control')) }}
                            </div>
                            <div class="form-group col-md-3">
                              <label>Regular Price</label>
                              {{Form::text('regular_price',null,array('class'=>'form-control')) }}
                            </div>
                            <div class="form-group col-md-3">
                              <label>Sale Price</label>
                              {{Form::text('sale_price',null,array('class'=>'form-control')) }}
                            </div>
                          </div>

                          <div class="form-row">
                            <div class="form-group col-md-6">
                              <label>Quantity</label>
                              {{Form::number('quantity',null,array('class'=>'form-control')) }}
                            </div>
                            <div class="form-group col-md-6">
                              <label>Minimum Quantity</label>
                              {{Form::number('minimum_quantity',null,array('class'=>'form-control')) }}
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
                            </div>
                            <div class="form-group col-md-6">
                              <label>Featured Image</label>
                              {{Form::file('featured_image',array('class'=>'form-control')) }}
                            </div>
                          </div>
                          <div class="form-row">
                            <div class="form-group col-md-12">
                              <label>Video</label>
                              {{Form::textarea('video',null,array('class'=>'form-control','rows'=>5)) }}
                            </div>
                          </div>
                          <div class="form-row">
                            <div class="form-group col-md-12">
                              <label>Gallery Images</label>
                              <div class="dropzone" id="my-dropzone" name="mainFileUploader" style="border: 2px dashed #c6c6c6; padding: 30px; text-align: center;">
                                <div class="fallback">
                                  {{Form::file('featured_image',array('multiple'=>'true')) }}
                                </div>
                              </div>
                              <div id="files_container" class="fw fl p10 b-blue radius-4" style="display:none"></div>
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
                              {{Form::text('dimensions',null,array('class'=>'form-control')) }}
                            </div>
                            <div class="form-group col-md-6">
                              <label>Length Class (Centimeter, Millimeter, Inch)</label>
                              {{Form::select('length_class',['Centimeter'=>'Centimeter','Millimeter'=>'Millimeter','Inch'=>'Inch'],null,array('class'=>'form-control')) }}
                            </div>
                          </div>

                          <div class="form-row">
                            <div class="form-group col-md-6">
                              <label>Weight</label>
                              {{Form::number('weight',null,array('class'=>'form-control')) }}
                            </div>
                            <div class="form-group col-md-6">
                              <label>Weight Class (Kilogram, Gram, Pound)</label>
                              {{Form::select('weight_class',['Kilogram'=>'Kilogram','Gram'=>'Gram','Pound'=>'Pound'],null,array('class'=>'form-control')) }}
                            </div>
                          </div>

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
                        </div> <!-- end col -->
                      </div> <!-- end row -->
                    </div>

                    <div class="tab-pane" id="discount_tab">
                      <div class="row">
                        <div class="col-12">
                          <div class="form-row">
                            
                            <div class="form-group col-md-6">
                              <label>Quantity</label>
                              {{Form::number('discount[quantity]',null,array('class'=>'form-control')) }}
                            </div>
                          </div>

                          <div class="form-row">
                            <div class="form-group col-md-6">
                              <label>Priority</label>
                              {{Form::text('discount[priority]',null,array('class'=>'form-control')) }}
                            </div>
                            <div class="form-group col-md-6">
                              <label>Price</label>
                              {{Form::text('discount[price]',null,array('class'=>'form-control')) }}
                            </div>
                          </div>

                          <div class="form-row">
                             <div class="form-group col-md-6">
                              <label>End Date </label>
                              {{Form::date('discount[start_date]',null,array('class'=>'form-control')) }}
                            </div>
                            <div class="form-group col-md-6">
                              <label>Start Date </label>
                              {{Form::date('discount[end_date]',null,array('class'=>'form-control')) }}
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
<style type="text/css">
.addMorebtn {
  margin-top: 30px;
  text-align: center;
  font-size: 19px;
}
</style>
@endsection