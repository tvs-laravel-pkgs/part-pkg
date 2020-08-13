app.component('rackList', {
    templateUrl: rack_list_template_url,
    controller: function(HelperService, $rootScope, $routeParams, $scope, $http, $timeout, $location) {
        var self = this;
        self.hasPermission = HelperService.hasPermission;
        var tables = [];
        $http.get(
            laravel_routes['getRackFilterDetails']
        ).then(function(response) {
            self.outlet_list = response.data.outlet_list;
            var d = new Date();
            var month = d.getMonth() + 1;
            var day = d.getDate();
            self.current_date = d.getFullYear() + '/' +
                (('' + month).length < 2 ? '0' : '') + month + '/' +
                (('' + day).length < 2 ? '0' : '') + day;
            $('#date_range').val('');
            $timeout(function() {
                var cols = [
                    { data: 'action', class: 'action', searchable: false },
                    { data: 'type', name: 'type', searchable: true },
                    { data: 'name', name: 'name', searchable: true },
                    { data: 'status', name: '' },
                ];
                var dataTable = $('#rack-table').DataTable({
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
                        url: laravel_routes['getRackList'],
                        data: function(d) {
                            d.today = $('#today').val();
                            d.date_range = $('#date_range').val();
                            d.outlet = $('.outlet_id').val();
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
                $('.title-block').html('<h1 class="title">Rack<span class="badge badge-secondary" id="table_info">0</span></h1>');
                $('.page-header-content-left .search-block .dataTables_filter label').append('<button class="btn-clear search_clear">Clear</button>');
                $('.page-header-content-left .search-block .dataTables_filter').addClass('search_filter');

                $('li').removeClass('active');
                $('.master_link').addClass('active').trigger('click');

                $('.page-header-content-right .button-block').html(
                    '<a href="#!/part-pkg/rack/form" type="button" class="btn btn-primary">' +
                    'Add New' +
                    '</a>'
                );

                $('.page-header-content-left .button-block').html(
                    '<button type="button" class="btn btn-refresh refresh_table"><img src="' + refresh_img_url + '" class="img-responsive btn-refresh-icon"></button>' +
                    '</button>'
                );
                $('.page-header-content-left .button-block').html(
                    '<button class="btn btn-bordered" data-toggle="modal" data-target="#filter">' +
                    '<i class="icon ion-md-funnel"></i>Filter' +
                    '</button>'
                );
                $('.page-header-content-left .button-block').addClass('pad-lf-rt');
                $('.refresh_table').on("click", function() {
                    $('#rack-table').DataTable().ajax.reload();
                });
                $('.search_clear').on("click", function() {
                    $('#rack-table').DataTable().search('').draw();
                });
                $('#date_range').daterangepicker({
                    locale: {
                        format: 'DD/MM/YYYY',
                    },
                    "opens": "center",
                    autoclose: true,
                });

                $('#today').change(function() {
                    $('#all_date').prop('checked', false);
                    $('#date_range').val('');
                    $('#today').val(self.current_date);
                });

                $('#date_range').change(function() {
                    $('#today').prop('checked', false);
                    $('#all_date').prop('checked', false);
                    $('#today').val('');

                });

                $('#filter').find("#date_range").change(function() {
                    $(this).next("#date_range").addClass('red');
                });

                $('#filter').find("input").change(function() {
                    $(this).addClass('red');
                });

                $scope.onChangeOutlet = function(id) {
                    $('.outlet_id').val(id);
                }

                $scope.onClickAll = function() {
                    $("#today").prop('checked', false);
                    $('#today').val('');
                    $('#date_range').val('');
                }

                $scope.onApplyFilter = function() {
                    $("#filter").modal('hide');
                    dataTable.draw();
                }

                $scope.onResetFilter = function() {
                    $("#all_date").prop('checked', false);
                    $("#today").prop('checked', false);
                    $('#today').val('');
                    $('#all_date').val('');
                    $('#date_range').val('');
                    $('.outlet_id').val('');
                    self.outlet_id = null;
                    dataTable.draw();
                    $("#filter").modal('hide');
                }
                $rootScope.loading = false;
            }, 600);
        });
    }
});
app.component('rackForm', {
    templateUrl: rack_form_template_url,
    controller: function(HelperService, $rootScope, $routeParams, $scope, $http, $location) {
        var id = $routeParams.id;
        var self = this;
        self.hasPermission = HelperService.hasPermission;
        self.angular_routes = angular_routes;
        $http.get(
            laravel_routes['getRackFormDetails'], {
                params: {
                    id: typeof($routeParams.id) == 'undefined' ? null : $routeParams.id,
                }
            }
        ).then(function(response) {
            self.action = response.data.action;
            self.rack = response.data.rack_details;
            self.type_list = response.data.type_list;
            if (self.action == 'Edit') {
                if (self.rack.deleted_at) {
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
                } else if (element.hasClass("type_id")) {
                    error.appendTo($('.type_id_error'));
                } else {
                    error.insertAfter(element)
                }
            },
            ignore: '',
            rules: {
                'name': {
                    required: true,
                },
                'type_id ': {
                    required: true,
                },
            },
            submitHandler: function(form) {
                let formData = new FormData($(form_id)[0]);
                $('#submit').button('loading');
                $.ajax({
                        url: laravel_routes['saveRack'],
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
                            $location.path('/part-pkg/rack/list')
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
app.component('rackView', {
    templateUrl: rack_view_template_url,
    controller: function(HelperService, $rootScope, $routeParams, $scope, $http, $location) {
        var id = $routeParams.id;
        var self = this;
        self.hasPermission = HelperService.hasPermission;
        self.angular_routes = angular_routes;
        $http.get(
            laravel_routes['getRackFormDetails'], {
                params: {
                    id: typeof($routeParams.id) == 'undefined' ? null : $routeParams.id,
                }
            }
        ).then(function(response) {
            self.rack = response.data.rack_details;
        });
    }
});