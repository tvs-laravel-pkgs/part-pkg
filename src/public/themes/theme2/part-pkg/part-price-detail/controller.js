app.component('partPriceDetailList', {
    templateUrl: part_price_detail_template_url,
    controller: function($http, $location, HelperService, $scope, $rootScope, $element) {
        $scope.loading = true;
        $('#search_part_price_detail').focus();
        var self = this;
        $('li').removeClass('active');
        $('.master_link').addClass('active').trigger('click');
        self.hasPermission = HelperService.hasPermission;
        var table_scroll;
        table_scroll = $('.page-main-content.list-page-content').height() - 37;
        var d = new Date();
            var month = d.getMonth() + 1;
            var day = d.getDate();
            self.current_date = d.getFullYear() + '/' +
                (('' + month).length < 2 ? '0' : '') + month + '/' +
                (('' + day).length < 2 ? '0' : '') + day;
            $('#date_range').val('');
            $('#today').val(self.current_date);
        var dataTable = $('#part_price_detail_list').DataTable({
            "dom": cndn_dom_structure,
            "language": {
                "lengthMenu": "Rows _MENU_",
                "paginate": {
                    "next": '<i class="icon ion-ios-arrow-forward"></i>',
                    "previous": '<i class="icon ion-ios-arrow-back"></i>'
                },
            },
            pageLength: 10,
            processing: true,
            stateSaveCallback: function(settings, data) {
                localStorage.setItem('CDataTables_' + settings.sInstance, JSON.stringify(data));
            },
            stateLoadCallback: function(settings) {
                var state_save_val = JSON.parse(localStorage.getItem('CDataTables_' + settings.sInstance));
                if (state_save_val) {
                    $('#search_part_price_detail').val(state_save_val.search.search);
                }
                return JSON.parse(localStorage.getItem('CDataTables_' + settings.sInstance));
            },
            searching: true,
            serverSide: true,
            paging: true,
            stateSave: true,
            scrollY: table_scroll + "px",
            scrollCollapse: true,
            ajax: {
                url: laravel_routes['getPartPriceDetailList'],
                data: function(d) {
                    d.today = $('#today').val();
                    d.date_range = $('#date_range').val();
                },
            },

            columns: [
                { data: 'action', class: 'action', name: 'action', searchable: false },
                { data: 'code', name: 'parts.code', searchable: true},
                { data: 'name', name: 'parts.name',searchable: true},
                { data: 'regular_price', name: 'regular_price' ,searchable: true},
                { data: 'retail_price', name: 'retail_price',searchable: true },
                { data: 'effective_from', name: 'effective_from',searchable: false },
                { data: 'effective_to', name: 'effective_to', searchable: false},

            ],
            "infoCallback": function(settings, start, end, max, total, pre) {
                $('#table_infos').html(total)
                $('.foot_info').html('Showing ' + start + ' to ' + end + ' of ' + max + ' entries')
            },
            rowCallback: function(row, data) {
                $(row).addClass('highlight-row');
            }
        });
        $('.dataTables_length select').select2();

        $scope.clear_search = function() {
            $('#search_part_price_detail').val('');
            $('#part_price_detail_list').DataTable().search('').draw();
        }
        $('.refresh_table').on("click", function() {
            $('#part_price_detail_list').DataTable().ajax.reload();
        });

        var dataTables = $('#part_price_detail_list').dataTable();
        $("#search_part_price_detail").keyup(function() {
            dataTables.fnFilter(this.value);
        });

        $element.find('input').on('keydown', function(ev) {
            ev.stopPropagation();
        });
        $('#date_range').daterangepicker({
            locale: {
                format: 'DD/MM/YYYY',
            },
            "opens": "center",
            autoclose: true,
        });
        $('#today').change(function () {
            $('#all_date').prop('checked', false);
            $('#date_range').val('');
            $('#today').val(self.current_date);
        });

        $('#date_range').change(function () {
            $('#today').prop('checked', false);
            $('#all_date').prop('checked', false);
            $('#today').val('');

        });
        $scope.onClickAll = function () {
            $("#today").prop('checked', false);
            $('#today').val('');
            $('#date_range').val('');
        }

        $scope.onApplyFilter = function() {
            $("#filter").modal('hide');
            dataTables.fnFilter();
        }
        $scope.onResetFilter = function() {
            $("#all_date").prop('checked', false);
            $("#today").prop('checked', false);
            $('#today').val('');
            $('#all_date').val('');
            $('#date_range').val('');
            $("#filter").modal('hide');
            dataTables.fnFilter();
        }

        $rootScope.loading = false;
    }
});
//------------------------------------------------------------------------------------------------------------------------
//------------------------------------------------------------------------------------------------------------------------

app.component('partPriceDetailForm', {
    templateUrl: part_price_detail_form_template_url,
    controller: function($http, $location, HelperService, $scope, $routeParams, $rootScope, $element) {
        var self = this;
        self.hasPermission = HelperService.hasPermission;
        
        $http.get(
            laravel_routes['getPartPriceDetailFormData'], {
                params: {
                    id: typeof($routeParams.id) == 'undefined' ? null : $routeParams.id,
                }
            }
        ).then(function(response) {
            self.part_price_detail = response.data.part_price_detail;
            self.action = response.data.action;
            console.log(self.action);
        });
        self.searchPartCode = function(query) {
            if (query) {
                return new Promise(function(resolve, reject) {
                    $http
                        .post(
                            laravel_routes['getPart'], {
                                key: query,
                            }
                        )
                        .then(function(response) {
                            resolve(response.data.part_details);
                        });
                });
            } else {
                return [];
            }
        }
    
        jQuery.validator.addMethod("decimal", function(value, element) {
            return this.optional(element) || /^\d{0,10}(\.\d{0,2})?$/i.test(value);
        }, "You must include two decimal places");
        //Form Submit
        var form_id = '#form-part-price-detail';
        var v = jQuery(form_id).validate({
            invalidHandler: function(event, validator) {
                custom_noty('error', 'You have errors, Please check all tabs');
            },
            ignore: "",
            rules: {
                part_id: {
                    required: true,
                },
                regular_price: {
                    required: true,
                    maxlength: 12,
                    number: true,
                    decimal: true,
                },
                retail_price: {
                    required: true,
                    maxlength: 12,
                    number: true,
                    decimal: true,
                },
            },
            submitHandler: function(form) {
                let formData = new FormData($(form_id)[0]);
                $('.submit').button('loading');
                $.ajax({
                        url: laravel_routes['savePartPriceDetail'],
                        method: "POST",
                        data: formData,
                        processData: false,
                        contentType: false,
                    })
                    .done(function(res) {
                        if (res.success == true) {
                            custom_noty('success', res.message);
                            $location.path('/part-pkg/part-price-detail/list');
                            $scope.$apply();
                        } else {
                            if (!res.success == true) {
                                $('.submit').button('reset');
                                showErrorNoty(res);
                            } else {
                                $('.submit').button('reset');
                                $location.path('/part-pkg/part-price-detail/list');
                                $scope.$apply();
                            }
                        }
                    })
                    .fail(function(xhr) {
                        $('.submit').button('reset');
                        custom_noty('error', 'Something went wrong at server');
                    });

            },
        });
    }
});
//------------------------------------------------------------------------------------------------------------------------
//------------------------------------------------------------------------------------------------------------------------