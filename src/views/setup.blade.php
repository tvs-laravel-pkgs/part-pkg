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
	    });
	}]);

	//Parts
    var part_list_template_url = '{{asset($part_pkg_prefix.'/public/themes/'.$theme.'/part-pkg/part/list.html')}}';
    var part_form_template_url = '{{asset($part_pkg_prefix.'/public/themes/'.$theme.'/part-pkg/part/form.html')}}';
    var part_card_list_template_url = '{{asset($part_pkg_prefix.'/public/themes/'.$theme.'/part-pkg/part/card-list.html')}}';
    var part_modal_form_template_url = '{{asset($part_pkg_prefix.'/public/themes/'.$theme.'/part-pkg/partials/part-modal-form.html')}}';
</script>
<!-- <script type='text/javascript' src='{{asset($part_pkg_prefix.'/public/themes/'.$theme.'/part-pkg/part/controller.js')}}'></script> -->

