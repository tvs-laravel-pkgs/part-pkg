app.component('brandList', {
    templateUrl: brand_list_template_url,
    controller: function(HelperService, $rootScope, $routeParams, $scope, $http, $timeout, $location) {
        var self = this;
        self.hasPermission = HelperService.hasPermission;
        var tables = [];
        var cols = [
            { data: 'action', class: 'action', searchable: false },
            { data: 'name', name: 'name', searchable: true },
        ];
        var dataTable = $('#brand-table').DataTable({
            "dom": dom_structure_2,
            "language": {
                "search": "",
                "searchPlaceholder": "Search",
                "lengthMenu": "Rows Per Page _MENU_",
                "paginate": {
                    "next": '<i class="fas fa-chevron-right">',
                    "previous": '<i class="fas fa-chevron-left">'
                },
            },
            "pageLength": 10,
            "paging": true,
            stateSave: true,
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: {
                url: laravel_routes['getBrandList'],
                data: function(d) {

                }
            },
            columns: cols,
            rowCallback: function(row, data, index) {},
            "ordering": false,
            infoCallback: function(settings, start, end, max, total, pre) {
                $('#table_info').html(total)
                $('.foot_info').html('Showing ' + start + ' to ' + end + ' of ' + max + ' entries')
            },
        });
        $('.dataTables_length select').select2();
        $('.title-block').html('<h1 class="title">Brand<span class="badge badge-secondary" id="table_info">0</span></h1>');
        $('.page-header-content-left .search-block .dataTables_filter label').append('<button class="btn-clear search_clear">Clear</button>');
        $('.page-header-content-left .search-block .dataTables_filter').addClass('search_filter');

        $('li').removeClass('active');
        $('.master_link').addClass('active').trigger('click');

        $('.page-header-content-right .button-block').html(
            '<a href="#!/part-pkg/brand/form" type="button" class="btn btn-primary">' +
            'Add New' +
            '</a>'
        );

        $('.page-header-content-left .button-block').html(
            '<button type="button" class="btn btn-refresh refresh_table"><img src="' + refresh_img_url + '" class="img-responsive btn-refresh-icon"></button>' +
            '</button>'
        );
        $('.page-header-content-left .button-block').addClass('pad-lf-rt');
        $('.refresh_table').on("click", function() {
            $('#brand-table').DataTable().ajax.reload();
        });
        $('.search_clear').on("click", function() {
            $('#brand-table').DataTable().search('').draw();
        });
        $rootScope.loading = false;
    }
});
app.component('brandForm', {
    templateUrl: brand_form_template_url,
    controller: function(HelperService, $rootScope, $routeParams, $scope, $http, $location) {
        var id = $routeParams.id;
        var self = this;
        self.hasPermission = HelperService.hasPermission;
        self.angular_routes = angular_routes;
        $http.get(
            laravel_routes['getBrandFormDetails'], {
                params: {
                    id: typeof($routeParams.id) == 'undefined' ? null : $routeParams.id,
                }
            }
        ).then(function(response) {
            self.action = response.data.action;
            self.brand = response.data.brand_details;
        });

        var form_id = form_ids = '#form';
        var v = jQuery(form_ids).validate({

            errorPlacement: function(error, element) {
                if (element.hasClass("name")) {
                    error.appendTo($('.name_error'));
                } else {
                    error.insertAfter(element)
                }
            },
            ignore: '',
            rules: {
                'name': {
                    required: true,
                },
            },
            submitHandler: function(form) {
                let formData = new FormData($(form_id)[0]);
                $('#submit').button('loading');
                $.ajax({
                        url: laravel_routes['saveBrand'],
                        method: "POST",
                        data: formData,
                        processData: false,
                        contentType: false,
                    })
                    .done(function(res) {
                        if (!res.success) {
                            $('#submit').button('reset');
                            showErrorNoty(res);
                        } else {
                            custom_noty('success', res.message);
                            $('#submit').button('reset');
                            $location.path('/part-pkg/brand/list')
                            $scope.$apply()
                        }
                    })
                    .fail(function(xhr) {
                        $('#submit').button('reset');
                        custom_noty('error', 'Something went wrong at server');
                    });
            },
        });
    }
});
app.component('brandView', {
    templateUrl: brand_view_template_url,
    controller: function(HelperService, $rootScope, $routeParams, $scope, $http, $location) {
        var id = $routeParams.id;
        var self = this;
        self.hasPermission = HelperService.hasPermission;
        self.angular_routes = angular_routes;
        $http.get(
            laravel_routes['getBrandFormDetails'], {
                params: {
                    id: typeof($routeParams.id) == 'undefined' ? null : $routeParams.id,
                }
            }
        ).then(function(response) {
            self.brand = response.data.brand_details;
        });
    }
});