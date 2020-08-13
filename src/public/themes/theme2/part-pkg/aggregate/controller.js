app.component('aggregateList', {
    templateUrl: aggregate_list_template_url,
    controller: function(HelperService, $rootScope, $routeParams, $scope, $http, $timeout, $location) {
        var self = this;
        self.hasPermission = HelperService.hasPermission;
        var tables = [];
        var cols = [
            { data: 'action', class: 'action', searchable: false },
            { data: 'code', name: 'code', searchable: true },
            { data: 'name', name: 'name', searchable: true },
            { data: 'status', name: '' },
        ];
        var dataTable = $('#aggregate-table').DataTable({
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
                url: laravel_routes['getAggregateList'],
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
        $('.title-block').html('<h1 class="title">Aggregate<span class="badge badge-secondary" id="table_info">0</span></h1>');
        $('.page-header-content-left .search-block .dataTables_filter label').append('<button class="btn-clear search_clear">Clear</button>');
        $('.page-header-content-left .search-block .dataTables_filter').addClass('search_filter');

        $('li').removeClass('active');
        $('.master_link').addClass('active').trigger('click');

        $('.page-header-content-right .button-block').html(
            '<a href="#!/part-pkg/aggregate/form" type="button" class="btn btn-primary">' +
            'Add New' +
            '</a>'
        );

        $('.page-header-content-left .button-block').html(
            '<button type="button" class="btn btn-refresh refresh_table"><img src="' + refresh_img_url + '" class="img-responsive btn-refresh-icon"></button>' +
            '</button>'
        );
        $('.page-header-content-left .button-block').addClass('pad-lf-rt');
        $('.refresh_table').on("click", function() {
            $('#aggregate-table').DataTable().ajax.reload();
        });
        $('.search_clear').on("click", function() {
            $('#aggregate-table').DataTable().search('').draw();
        });
        $rootScope.loading = false;
    }
});
app.component('aggregateForm', {
    templateUrl: aggregate_form_template_url,
    controller: function(HelperService, $rootScope, $routeParams, $scope, $http, $location) {
        var id = $routeParams.id;
        var self = this;
        self.hasPermission = HelperService.hasPermission;
        self.angular_routes = angular_routes;
        $http.get(
            laravel_routes['getAggregateFormDetails'], {
                params: {
                    id: typeof($routeParams.id) == 'undefined' ? null : $routeParams.id,
                }
            }
        ).then(function(response) {
            self.action = response.data.action;
            self.aggregate = response.data.aggregate_details;

            if (self.action == 'Edit') {
                if (self.aggregate.deleted_at) {
                    self.switch_value = 'Inactive';
                } else {
                    self.switch_value = 'Active';
                }
            } else {
                self.switch_value = 'Active';
            }
        });

        var form_id = form_ids = '#form';
        var v = jQuery(form_ids).validate({

            errorPlacement: function(error, element) {
                if (element.hasClass("name")) {
                    error.appendTo($('.name_error'));
                } else if (element.hasClass("code")) {
                    error.appendTo($('.code_error'));
                } else {
                    error.insertAfter(element)
                }
            },
            ignore: '',
            rules: {
                'name': {
                    required: true,
                },
                'code': {
                    required: true,
                },
            },
            submitHandler: function(form) {
                let formData = new FormData($(form_id)[0]);
                $('#submit').button('loading');
                $.ajax({
                        url: laravel_routes['saveAggregate'],
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
                            $location.path('/part-pkg/aggregate/list')
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
app.component('aggregateView', {
    templateUrl: aggregate_view_template_url,
    controller: function(HelperService, $rootScope, $routeParams, $scope, $http, $location) {
        var id = $routeParams.id;
        var self = this;
        self.hasPermission = HelperService.hasPermission;
        self.angular_routes = angular_routes;
        $http.get(
            laravel_routes['getAggregateFormDetails'], {
                params: {
                    id: typeof($routeParams.id) == 'undefined' ? null : $routeParams.id,
                }
            }
        ).then(function(response) {
            self.aggregate = response.data.aggregate_details;
        });
    }
});