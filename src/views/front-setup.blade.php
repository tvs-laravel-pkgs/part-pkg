@if(config('part-pkg.DEV'))
    <?php $part_pkg_prefix = '/packages/abs/part-pkg/src';?>
@else
    <?php $part_pkg_prefix = '';?>
@endif

<script type="text/javascript">
    var parts_voucher_list_template_url = "{{asset($part_pkg_prefix.'/public/themes/'.$theme.'/part-pkg/part/part.html')}}";
</script>
<script type="text/javascript" src="{{asset($part_pkg_prefix.'/public/themes/'.$theme.'/part-pkg/part/controller.js')}}"></script>
