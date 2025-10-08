<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{\App\CPU\translate('Ready to Leave')}}?</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">{{\App\CPU\translate('Select "Logout" below if you are ready to end your current session')}}.</div>
            <div class="modal-footer">
                <form action="{{route('admin.auth.logout')}}" method="post">
                    @csrf
                    <button class="btn btn-danger" type="button" data-dismiss="modal">{{\App\CPU\translate('Cancel')}}</button>
                    <button class="btn btn--primary" type="submit">{{\App\CPU\translate('Logout')}}</button>
                </form>
            </div>
        </div>
    </div>
</div>


<!--- product filter start -->

  <div class="modal fade" tabindex="-1" role="dialog" id="productFilterModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{\App\CPU\translate('Product_Filter')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ url()->current() }}" method="GET" id="addForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="modalIdPlaceholder" name="order_id" value="">
                <input type="hidden" value="{{ isset($request_status) ? $request_status : '' }}" name="status">
                <div class="modal-body" style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">
                    <!-- Other existing fields -->

                    <div class="form-group">
                        <label>{{\App\CPU\translate('Category')}}</label>
                         @php($category=\App\Model\Category::where(['position' => 0])->orderBy('name')->get())
                        <select class="form-control" name="category_id" id="category-id" onchange="category_fun()">
                            <option disabled selected >select category</option>
                            @foreach($category as $categories)
                             <option value="{{ $categories->id }}">{{ $categories->name }}</option> 
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>{{\App\CPU\translate('Subcategory')}}</label>
                         @php($sub_category=\App\Model\Category::where(['position' => 1])->orderBy('name')->get())
                        <select class="form-control" name="sub_category_id" id="sub-category-id">
                            <!-- Populate subcategories dynamically based on selected category -->
                            <option disabled selected >select sub-category</option>
                            @foreach($sub_category as $sub_categories)
                           <option value="{{ $sub_categories->id }}">{{ $sub_categories->name }}</option> 
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>{{\App\CPU\translate('Brand')}}</label>
                         @php($brand=\App\Model\Brand::withCount('brandAllProducts')->with(['brandAllProducts'=> function($query){
                             $query->withCount('order_details');
                              }])->orderBy('brands.name')->get())
                        <select class="form-control" name="brand_id">
                            <!-- Populate brands dynamically -->
                             <option disabled selected >select brand</option>
                            @foreach($brand as $brands)
                            <option value="{{ $brands->id }}">{{ $brands->defaultname }}</option> 
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>{{\App\CPU\translate('Minimum Price')}}</label>
                        <input type="number" class="form-control" name="min_price" placeholder="{{\App\CPU\translate('Minimum Price')}}">
                    </div>

                    <div class="form-group">
                        <label>{{\App\CPU\translate('Maximum Price')}}</label>
                        <input type="number" class="form-control" name="max_price" placeholder="{{\App\CPU\translate('Maximum Price')}}">
                    </div>

                    <div class="form-group">
                        <label>{{\App\CPU\translate('Minimum Reviews')}}</label>
                        <input type="number" class="form-control" name="min_reviews" placeholder="{{\App\CPU\translate('Minimum Reviews')}}">
                    </div>

                    <!-- Additional filter fields go here -->

                </div>
                <div class="modal-footer bg-whitesmoke br">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        {{\App\CPU\translate('Close')}}
                    </button>
                    <button class="btn btn--primary" name="filter" value="filter">
                        {{\App\CPU\translate('filter')}}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>



<!--- product filter end -->

<!-- <div class="modal" id="popup-modal">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">
                        <center>
                            <h2 class="__color-8a8a8a">
                                <i class="tio-shopping-cart-outlined"></i> You have new order, Check Please.
                            </h2>
                            <hr>
                            <button onclick="check_order()" class="btn btn--primary">Ok, let me check</button>
                        </center>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> -->

<script>


  

        function category_fun()
        {
                 var category_id =  $("#category-id").val();
               
             var data = {
            category_id: category_id,
            _token: '{!! csrf_token() !!}'
        };
        
        $.ajax({
            url: "{{route('admin.sub-category.record')}}",
            type: 'POST', 
            data: data,
            success: function(response) {
                
               $('#sub-category-id').html(response);
            },
            error: function(error) {
                // Handle errors here
                console.error('Error:', error);
            }
        });
     }
</script>
