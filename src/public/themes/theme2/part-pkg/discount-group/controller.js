app.component('discountGroupList', {
    templateUrl: discount_group_list_template_url,
    controller: function($http, $location, HelperService, $scope, $routeParams, $rootScope, $element, $mdSelect) {
        $scope.loading = true;
        $('#search_discount_group').focus();
        var self = this;
        $('li').removeClass('active');
        $('.master_link').addClass('active').trigger('click');
        self.hasPermission = HelperService.hasPermission;

        var table_scroll;
        table_scroll = $('.page-main-content.list-page-content').height() - 37;
        var dataTable = $('#discount_groups_list').DataTable({
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
                    $('#search_part').val(state_save_val.search.search);
                }
                return JSON.parse(localStorage.getItem('CDataTables_' + settings.sInstance));
            },
            serverSide: true,
            paging: true,
            stateSave: true,
            scrollY: table_scroll + "px",
            scrollCollapse: true,
            ajax: {
                url: laravel_routes['getDiscountGroupList'],
                type: "GET",
                dataType: "json",
                data: function(d) {
                    d.type_list_filter_id = $("#type_list_filter_id").val();
                    d.status_list_filter_id = $("#status_list_filter_id").val();
                },
            },

            columns: [
                { data: 'action', class: 'action', name: 'action', searchable: false },
                { data: 'type', name: 'configs.name' },
                { data: 'code', name: 'discount_groups.code' },
                { data: 'name', name: 'discount_groups.name' },
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
            $('#search_discount_group').val('');
            $('#discount_groups_list').DataTable().search('').draw();
        }
        $('.refresh_table').on("click", function() {
            $('#discount_groups_list').DataTable().ajax.reload();
        });

        var dataTables = $('#discount_groups_list').dataTable();
        $("#search_discount_group").keyup(function() {
            dataTables.fnFilter(this.value);
        });

        //DELETE
        $scope.deleteDiscountGroup = function($id) {
            $('#discount_group_id').val($id);
        }
        $scope.deleteConfirm = function() {
            $id = $('#discount_group_id').val();
            $http.get(
                laravel_routes['deleteDiscountGroup'], {
                    params: {
                        id: $id,
                    }
                }
            ).then(function(response) {
                if (response.data.success) {
                    custom_noty('success', 'Discount Group Deleted Successfully');
                    $('#discount_groups_list').DataTable().ajax.reload(function(json) {});
                    $location.path('/part-pkg/discount-group/list');
                }
            });
        }

        // FOR FILTER
        $http.get(
            laravel_routes['getDiscountGrpFilterData']
        ).then(function(response) {
            self.type_lists = response.data.type_list;
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
        $scope.onSelectedType = function(id) {
            $('#type_list_filter_id').val(id);
        }

        $scope.reset_filter = function() {
            $("#status_list_filter_id").val('');
            $('#type_list_filter_id').val('');
            self.type_list_id = null;
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

app.component('discountGroupForm', {
    templateUrl: discount_group_form_template_url,
    controller: function(HelperService, $rootScope, $routeParams, $scope, $http, $location, $element) {
        var id = $routeParams.id;
        var self = this;
        var type = self.type = $routeParams.type;
        self.hasPermission = HelperService.hasPermission;
        self.angular_routes = angular_routes;
        $http.get(
            laravel_routes['getDiscountGroupFormDetails'], {
                params: {
                    id: typeof($routeParams.id) == 'undefined' ? null : $routeParams.id,
                    type: typeof($routeParams.type) == 'undefined' ? null : $routeParams.type,
                }
            }
        ).then(function(response) {
            self.action = response.data.action;
            self.discount_group = response.data.discount_group;
            self.type_list = response.data.type_list;
            if (self.action == 'Edit' || self.action == 'View') {
                if (self.discount_group.deleted_at) {
                    self.switch_value = 'Inactive';
                } else {
                    self.switch_value = 'Active';
                }
            } else {
                self.switch_value = 'Active';
            }
        });

        $scope.searchType;
        $scope.clearSearchType = function() {
            $scope.searchType = '';
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
                if (element.hasClass("type_id")) {
                    error.appendTo($('.type_id_error'));
                } else if (element.hasClass("code")) {
                    error.appendTo($('.code_error'));
                } else if (element.hasClass("name")) {
                    error.appendTo($('.name_error'));
                } else {
                    error.insertAfter(element)
                }
            },
            ignore: '',
            rules: {
                'type_id': {
                    required: true,
                },
                'code': {
                    required: true,
                },
                'name': {
                    required: true,
                },
            },
            submitHandler: function(form) {
                let formData = new FormData($(form_id)[0]);
                $('#submit').button('loading');
                $.ajax({
                        url: laravel_routes['saveDiscountGroup'],
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
                            $location.path('/part-pkg/discount-group/list')
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