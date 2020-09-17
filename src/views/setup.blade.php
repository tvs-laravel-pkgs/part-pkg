@if(config('part-pkg.DEV'))
    <?php $part_pkg_prefix = '/packages/abs/part-pkg/src';?>
@else
    <?php $part_pkg_prefix = '';?>
@endif


<script type='text/javascript'>
	app.config(['$routeProvider', function($routeProvider) {
	    $routeProvider.
	    //Part
	    when('/part-pkg/part/list', {
	        template: '<part-list></part-list>',
	        title: 'Parts',
	    }).
	    when('/part-pkg/part/add', {
	        template: '<part-form></part-form>',
	        title: 'Add Part',
	    }).
	    when('/part-pkg/part/edit/:id', {
	        template: '<part-form></part-form>',
	        title: 'Edit Part',
	    }).
	    when('/part-pkg/part/card-list', {
	        template: '<part-card-list></part-card-list>',
	        title: 'Part Card List',
	    }).

	    //By karthick t on 12-08-2020
	    //Aggregate
	    when('/part-pkg/aggregate/list', {
	        template: '<aggregate-list></aggregate-list>',
	        title: 'Aggregate List',
	    }).
	    when('/part-pkg/aggregate/form/:id?', {
	        template: '<aggregate-form></aggregate-form>',
	        title: 'Aggregate Form',
	    }).
	    when('/part-pkg/aggregate/view/:id', {
	        template: '<aggregate-view></aggregate-view>',
	        title: 'Aggregate View',
	    }).
	    //Sub Aggregate
	    when('/part-pkg/sub-aggregate/list', {
	        template: '<sub-aggregate-list></sub-aggregate-list>',
	        title: 'Sub Aggregate List',
	    }).
	    when('/part-pkg/sub-aggregate/form/:id?', {
	        template: '<sub-aggregate-form></sub-aggregate-form>',
	        title: 'Sub Aggregate Form',
	    }).
	    when('/part-pkg/sub-aggregate/view/:id', {
	        template: '<sub-aggregate-view></sub-aggregate-view>',
	        title: 'Sub Aggregate View',
	    }).
	    //Brand
	    when('/part-pkg/brand/list', {
	        template: '<brand-list></brand-list>',
	        title: 'Brand List',
	    }).
	    when('/part-pkg/brand/form/:id?', {
	        template: '<brand-form></brand-form>',
	        title: 'Brand Form',
	    }).
	    when('/part-pkg/brand/view/:id', {
	        template: '<brand-view></brand-view>',
	        title: 'Brand View',
	    }).
	    //Variant
	    when('/part-pkg/variant/list', {
	        template: '<variant-list></variant-list>',
	        title: 'Variant List',
	    }).
	    when('/part-pkg/variant/form/:id?', {
	        template: '<variant-form></variant-form>',
	        title: 'Variant Form',
	    }).
	    when('/part-pkg/variant/view/:id', {
	        template: '<variant-view></variant-view>',
	        title: 'Variant View',
	    }).
	    //Component
	    when('/part-pkg/component/list', {
	        template: '<component-list></component-list>',
	        title: 'Component List',
	    }).
	    when('/part-pkg/component/form/:id?', {
	        template: '<component-form></component-form>',
	        title: 'Component Form',
	    }).
	    when('/part-pkg/component/view/:id', {
	        template: '<component-view></component-view>',
	        title: 'Component View',
	    }).
	    //Rack
	    when('/part-pkg/rack/list', {
	        template: '<rack-list></rack-list>',
	        title: 'Rack List',
	    }).
	    when('/part-pkg/rack/form/:id?', {
	        template: '<rack-form></rack-form>',
	        title: 'Rack Form',
	    }).
	    when('/part-pkg/rack/view/:id', {
	        template: '<rack-view></rack-view>',
	        title: 'Rack View',
	    }).
	    //By karthick t on 12-08-2020


	    //Added by karthick t on 17-09-2020
	    //For Discount Group
	    when('/part-pkg/discount-group/list', {
	    	template: '<discount-group-list></discount-group-list>',
	        title: 'Discount Group List',
	    }).
	    when('/part-pkg/discount-group/:type/:id?', {
	    	template: '<discount-group-form></discount-group-form>',
	        title: 'Discount Group Form',
	    }).
	    //For Price Discount
	    when('/part-pkg/price-discount/list', {
	    	template: '<price-discount-list></price-discount-list>',
	        title: 'Price Discount List',
	    }).
	    when('/part-pkg/price-discount/:type/:id?', {
	    	template: '<price-discount-form></price-discount-form>',
	        title: 'Price Discount Form',
	    });
	    //Added by karthick t on 17-09-2020
	}]);

	//Parts
    var part_list_template_url = '{{asset($part_pkg_prefix.'/public/themes/'.$theme.'/part-pkg/part/list.html')}}';
    var part_form_template_url = '{{asset($part_pkg_prefix.'/public/themes/'.$theme.'/part-pkg/part/form.html')}}';
    var part_card_list_template_url = '{{asset($part_pkg_prefix.'/public/themes/'.$theme.'/part-pkg/part/card-list.html')}}';
    var part_modal_form_template_url = '{{asset($part_pkg_prefix.'/public/themes/'.$theme.'/part-pkg/partials/part-modal-form.html')}}';

    //By karthick t on 12-08-2020
    //Aggregate
    var aggregate_list_template_url = '{{asset($part_pkg_prefix.'/public/themes/'.$theme.'/part-pkg/aggregate/list.html')}}';

    var aggregate_form_template_url = '{{asset($part_pkg_prefix.'/public/themes/'.$theme.'/part-pkg/aggregate/form.html')}}';
    var aggregate_view_template_url = '{{asset($part_pkg_prefix.'/public/themes/'.$theme.'/part-pkg/aggregate/view.html')}}';

    var sub_aggregate_list_template_url = '{{asset($part_pkg_prefix.'/public/themes/'.$theme.'/part-pkg/sub-aggregate/list.html')}}';
    var sub_aggregate_form_template_url = '{{asset($part_pkg_prefix.'/public/themes/'.$theme.'/part-pkg/sub-aggregate/form.html')}}';
    var sub_aggregate_view_template_url = '{{asset($part_pkg_prefix.'/public/themes/'.$theme.'/part-pkg/sub-aggregate/view.html')}}';
    
    //Brand
    var brand_list_template_url = '{{asset($part_pkg_prefix.'/public/themes/'.$theme.'/part-pkg/brand/list.html')}}';
    var brand_form_template_url = '{{asset($part_pkg_prefix.'/public/themes/'.$theme.'/part-pkg/brand/form.html')}}';
    var brand_view_template_url = '{{asset($part_pkg_prefix.'/public/themes/'.$theme.'/part-pkg/brand/view.html')}}';
    
    //Variant
    var variant_list_template_url = '{{asset($part_pkg_prefix.'/public/themes/'.$theme.'/part-pkg/variant/list.html')}}';
    var variant_form_template_url = '{{asset($part_pkg_prefix.'/public/themes/'.$theme.'/part-pkg/variant/form.html')}}';
    var variant_view_template_url = '{{asset($part_pkg_prefix.'/public/themes/'.$theme.'/part-pkg/variant/view.html')}}';
    
    //Component
    var component_list_template_url = '{{asset($part_pkg_prefix.'/public/themes/'.$theme.'/part-pkg/component/list.html')}}';
    var component_form_template_url = '{{asset($part_pkg_prefix.'/public/themes/'.$theme.'/part-pkg/component/form.html')}}';
    var component_view_template_url = '{{asset($part_pkg_prefix.'/public/themes/'.$theme.'/part-pkg/component/view.html')}}';
    
    //Rack
    var rack_list_template_url = '{{asset($part_pkg_prefix.'/public/themes/'.$theme.'/part-pkg/rack/list.html')}}';
    var rack_form_template_url = '{{asset($part_pkg_prefix.'/public/themes/'.$theme.'/part-pkg/rack/form.html')}}';
    var rack_view_template_url = '{{asset($part_pkg_prefix.'/public/themes/'.$theme.'/part-pkg/rack/view.html')}}';
    //By karthick t on 12-08-2020

    //Added by karthick t on 17-09-2020
    //Discount Group
    var discount_group_list_template_url = '{{asset($part_pkg_prefix.'/public/themes/'.$theme.'/part-pkg/discount-group/list.html')}}';
    var discount_group_form_template_url = '{{asset($part_pkg_prefix.'/public/themes/'.$theme.'/part-pkg/discount-group/form.html')}}';
    //Price Discount
    var price_discount_list_template_url = '{{asset($part_pkg_prefix.'/public/themes/'.$theme.'/part-pkg/price-discount/list.html')}}';
    var price_discount_form_template_url = '{{asset($part_pkg_prefix.'/public/themes/'.$theme.'/part-pkg/price-discount/form.html')}}';
    //Added by karthick t on 17-09-2020
    
</script>
<!-- <script type='text/javascript' src='{{asset($part_pkg_prefix.'/public/themes/'.$theme.'/part-pkg/part/controller.js')}}'></script> -->

