app.component('priceDiscountList', {
    templateUrl: price_discount_list_template_url,
    controller: function($http, $location, HelperService, $scope, $routeParams, $rootScope, $element, $mdSelect) {
        $scope.loading = true;
        $('#search_price_discount').focus();
        var self = this;
        $('li').removeClass('active');
        $('.master_link').addClass('active').trigger('click');
        self.hasPermission = HelperService.hasPermission;

        var table_scroll;
        table_scroll = $('.page-main-content.list-page-content').height() - 37;
        var dataTable = $('#price_discounts_list').DataTable({
            "dom": cndn_dom_structure,
            "language": {
                // "search": "",
                // "searchPlaceholder": "Search",
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
                    $('#search_price_discount').val(state_save_val.search.search);
                }
                return JSON.parse(localStorage.getItem('CDataTables_' + settings.sInstance));
            },
            serverSide: true,
            paging: true,
            stateSave: true,
            scrollY: table_scroll + "px",
            scrollCollapse: true,
            ajax: {
                url: laravel_routes['getPriceDiscountList'],
                type: "GET",
                dataType: "json",
                data: function(d) {
                    d.region_id = $("#region_filter_id").val();
                    d.discount_grp_id = $("#discount_grp_filter_id").val();
                    d.status_list_filter_id = $("#status_list_filter_id").val();
                },
            },

            columns: [
                { data: 'action', class: 'action', name: 'action', searchable: false },
                { data: 'region', name: 'regions.name' },
                { data: 'discount_group', name: 'discount_groups.name' },
                { data: 'purchase_discount', name: 'price_discounts.purchase_discount' },
                { data: 'customer_discount', name: 'price_discounts.customer_discount' },
                { data: 'effective_from', name: 'price_discounts.effective_from' },
                { data: 'status', name: '' },

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
            $('#search_price_discount').val('');
            $('#price_discounts_list').DataTable().search('').draw();
        }
        $('.refresh_table').on("click", function() {
            $('#price_discounts_list').DataTable().ajax.reload();
        });

        var dataTables = $('#price_discounts_list').dataTable();
        $("#search_price_discount").keyup(function() {
            dataTables.fnFilter(this.value);
        });

        //DELETE
        $scope.deletePriceDiscount = function($id) {
            $('#price_discount_id').val($id);
        }
        $scope.deleteConfirm = function() {
            $id = $('#price_discount_id').val();
            $http.get(
                laravel_routes['deletePriceDiscount'], {
                    params: {
                        id: $id,
                    }
                }
            ).then(function(response) {
                if (response.data.success) {
                    custom_noty('success', 'Price Discount Deleted Successfully');
                    $('#price_discounts_list').DataTable().ajax.reload(function(json) {});
                    $location.path('/part-pkg/price-discount/list');
                }
            });
        }

        // FOR FILTER
        $http.get(
            laravel_routes['getPriceDiscountFilterData']
        ).then(function(response) {
            self.region_lists = response.data.region_list;
            self.discount_grp_lists = response.data.discount_grp_list;
            self.status_lists = response.data.status_list;
        });
        $element.find('input').on('keydown', function(ev) {
            ev.stopPropagation();
        });
        $scope.clearSearchTerm = function() {
            $scope.searchTerm = '';
            $scope.searchTerm1 = '';
            $scope.searchTerm2 = '';
            $scope.searchTerm3 = '';
        };
        $scope.clearSearchDiscount = function() {
            $scope.searchDiscount = '';
        };
        /* Modal Md Select Hide */
        $('.modal').bind('click', function(event) {
            if ($('.md-select-menu-container').hasClass('md-active')) {
                $mdSelect.hide();
            }
        });
        $('#code').on('keyup', function() {
            // dataTables.fnFilter();
        });
        $('#name').on('keyup', function() {
            // dataTables.fnFilter();
        });
        $scope.onSelectedStatus = function(id) {
            $('#status_list_filter_id').val(id);
        }
        $scope.onSelectedRegion = function(id) {
            $('#region_filter_id').val(id);
        }
        $scope.onSelectedDiscountGrp = function(id) {
            $('#discount_grp_filter_id').val(id);
        }

        $scope.reset_filter = function() {
            $("#status_list_filter_id").val('');
            $("#region_filter_id").val('');
            $('#discount_grp_filter_id').val('');
            self.region_filter_id = null;
            self.discount_grp_filter_id = null;
            self.status = null;
            $('#discount-group-filter-modal').modal('hide');
            dataTables.fnFilter();
        }
        $scope.apply_filter = function() {
            dataTables.fnFilter();
        }

        $rootScope.loading = false;
    }
});

app.component('priceDiscountForm', {
    templateUrl: price_discount_form_template_url,
    controller: function(HelperService, $rootScope, $routeParams, $scope, $http, $location, $element) {
        var id = $routeParams.id;
        var self = this;
        var type = self.type = $routeParams.type;
        self.hasPermission = HelperService.hasPermission;
        self.angular_routes = angular_routes;
        $http.get(
            laravel_routes['getPriceDiscountFormDetails'], {
                params: {
                    id: typeof($routeParams.id) == 'undefined' ? null : $routeParams.id,
                    type: typeof($routeParams.type) == 'undefined' ? null : $routeParams.type,
                }
            }
        ).then(function(response) {
            self.action = response.data.action;
            self.region_lists = response.data.region_list;
            self.discount_grp_lists = response.data.discount_grp_list;
            self.price_discount = response.data.price_discount;
            if (self.action == 'Edit' || self.action == 'View') {
                if (self.price_discount.deleted_at) {
                    self.switch_value = 'Inactive';
                } else {
                    self.switch_value = 'Active';
                }
            } else {
                self.switch_value = 'Active';
            }
        });

        $scope.searchRegion;
        $scope.clearSearchRegion = function() {
            $scope.searchRegion = '';
        };
        $scope.searchDiscountGrp;
        $scope.clearSearchDiscountGrp = function() {
            $scope.searchDiscountGrp = '';
        };
        // The md-select directive eats keydown events for some quick select
        // logic. Since we have a search input here, we don't need that logic.
        $element.find('input').on('keydown', function(ev) {
            ev.stopPropagation();
        });
        setTimeout(function() {
            $('.show-as').select2();
            $('.modal-select').select2();
            $('.multi-select').multiselect({
                enableClickableOptGroups: true,
                enableCollapsibleOptGroups: true,
                enableFiltering: true,
                enableCaseInsensitiveFiltering: true,
                includeSelectAllOption: true
            });
        }, 300);

        var form_id = form_ids = '#form';
        var v = jQuery(form_ids).validate({

            errorPlacement: function(error, element) {
                if (element.hasClass("region_id")) {
                    error.appendTo($('.region_id_error'));
                } else if (element.hasClass("discount_group_id")) {
                    error.appendTo($('.discount_group_id_error'));
                } else if (element.hasClass("purchase_discount")) {
                    error.appendTo($('.purchase_discount_error'));
                } else if (element.hasClass("approved_discount")) {
                    error.appendTo($('.approved_discount_error'));
                } else if (element.hasClass("customer_discount")) {
                    error.appendTo($('.customer_discount_error'));
                } else {
                    error.insertAfter(element)
                }
            },
            ignore: '',
            rules: {
                'region_id': {
                    required: true,
                },
                'discount_group_id': {
                    required: true,
                },
                'purchase_discount': {
                    required: true,
                },
                'approved_discount': {
                    required: true,
                },
                'customer_discount': {
                    required: true,
                },
                'effective_from': {
                    required: true,
                },
            },
            submitHandler: function(form) {
                let formData = new FormData($(form_id)[0]);
                $('#submit').button('loading');
                $.ajax({
                        url: laravel_routes['savePriceDiscount'],
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
                            $location.path('/part-pkg/price-discount/list')
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