app.component('partList', {
    templateUrl: part_list_template_url,
    controller: function($http, $location, HelperService, $scope, $routeParams, $rootScope, $element, $mdSelect) {
        $scope.loading = true;
        $('#search_part').focus();
        var self = this;
        $('li').removeClass('active');
        $('.master_link').addClass('active').trigger('click');
        self.hasPermission = HelperService.hasPermission;
        if (!self.hasPermission('parts')) {
            window.location = "#!/page-permission-denied";
            return false;
        }
        self.add_permission = self.hasPermission('add-part');
        var table_scroll;
        table_scroll = $('.page-main-content.list-page-content').height() - 37;
        var dataTable = $('#parts_list').DataTable({
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
                url: laravel_routes['getPartList'],
                type: "GET",
                dataType: "json",
                data: function(d) {
                    d.code = $("#code").val();
                    d.name = $("#name").val();
                    d.uom_filter_id = $("#uom_filter_id").val();
                    // alert($("#uom_filter_id").val());
                    d.tax_code_filter_id = $("#tax_code_filter_id").val();
                    d.status = $("#status").val();
                },
            },

            columns: [
                { data: 'action', class: 'action', name: 'action', searchable: false },
                { data: 'code', name: 'parts.code' },
                { data: 'name', name: 'parts.name' },
                { data: 'uom', name: 'uoms.code' },
                { data: 'tax_code', name: 'tax_codes.code' },
                { data: 'mrp', name: 'parts.mrp' },
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
            $('#search_part').val('');
            $('#parts_list').DataTable().search('').draw();
        }
        $('.refresh_table').on("click", function() {
            $('#parts_list').DataTable().ajax.reload();
        });

        var dataTables = $('#parts_list').dataTable();
        $("#search_part").keyup(function() {
            dataTables.fnFilter(this.value);
        });

        //DELETE
        $scope.deletePart = function($id) {
            $('#part_id').val($id);
        }
        $scope.deleteConfirm = function() {
            $id = $('#part_id').val();
            $http.get(
                laravel_routes['deletePart'], {
                    params: {
                        id: $id,
                    }
                }
            ).then(function(response) {
                if (response.data.success) {
                    custom_noty('success', 'Part Deleted Successfully');
                    $('#parts_list').DataTable().ajax.reload(function(json) {});
                    $location.path('/part-pkg/part/list');
                }
            });
        }

        // FOR FILTER
        $http.get(
            laravel_routes['getPartFilterData']
        ).then(function(response) {
            // console.log(response);
            self.extras = response.data.extras;
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
            $('#status').val(id);
            //dataTables.fnFilter();
        }
        $scope.onSelectedUom = function(id) {
            $('#uom_filter_id').val(id);
            //dataTables.fnFilter();
        }
        $scope.onSelectedTaxCode = function(id) {
            $('#tax_code_filter_id').val(id);
            //dataTables.fnFilter();
        }

        $scope.reset_filter = function() {
            $("#code").val('');
            $("#name").val('');
            $("#status").val('');
            $('#uom_filter_id').val('');
            $('#tax_code_filter_id').val('');
            $('#part-filter-modal').modal('hide');
            dataTables.fnFilter();
        }
        $scope.apply_filter = function() {
            dataTables.fnFilter();
        }

        $rootScope.loading = false;
    }
});

//------------------------------------------------------------------------------------------------------------------------
//------------------------------------------------------------------------------------------------------------------------

app.component('partForm', {
    templateUrl: part_form_template_url,
    controller: function($http, $location, HelperService, $scope, $routeParams, $rootScope, $element) {
        var self = this;
        self.hasPermission = HelperService.hasPermission;
        if (!self.hasPermission('add-part') || !self.hasPermission('edit-part')) {
            window.location = "#!/page-permission-denied";
            return false;
        }
        self.angular_routes = angular_routes;
        //UPDATED BY KARTHICK T ON 15-07-2020
        $http.get(
            laravel_routes['getPartFormData'], {
                params: {
                    id: typeof($routeParams.id) == 'undefined' ? null : $routeParams.id,
                }
            }
        ).then(function(response) {
            //UPDATED BY KARTHICK T ON 30-07-2020
            self.part_aggregate_list = response.data.aggregate_list;
            self.part_sub_aggregate_list = response.data.sub_aggregate_list;
            //UPDATED BY KARTHICK T ON 30-07-2020
            self.components_list = response.data.components_list;
            self.vehicle_make_list = response.data.vehicle_make_list;
            self.vehicle_model_list = response.data.vehicle_model_list;
            self.uom_list = response.data.extras.uom_list;
            self.part = response.data.part;
            self.alt_parts = response.data.alt_parts;
            self.upsell_parts = response.data.upsell_parts;
            self.action = response.data.action;
            self.alt_parts_ids = response.data.alt_parts_ids;
            self.upsell_parts_ids = response.data.upsell_parts_ids;
            self.vehicle_mappings = response.data.vehicle_mappings;
            self.years_list = response.data.year_list;
            self.fuel_type_list = response.data.fuel_type_list;
            self.vehicle_type_list = response.data.vehicle_type_list;
            self.rack_parts = response.data.rack_parts;
            self.variant_list = response.data.variant_list;
            self.brand_list = response.data.brand_list;
            self.component_list = response.data.component_list;
            self.rack_list = response.data.rack_list;
            self.rack_type_list = response.data.rack_type_list;
            self.count_attachments = response.data.count_attachments;
            if (self.count_attachments > 0) {
                self.attachments = response.data.attachments;
                self.view = response.data.view;
            }

            //For Discount Group by karthick t on 16-09-2020
            self.discount_group_list = response.data.discount_group_list;
            //For Category List By Parthiban V on 23-06-2021
            self.category_list = response.data.category_list;
            $('#alternate_part_ids').val(self.alt_parts_ids.join());
            $('#upsell_part_ids').val(self.upsell_parts_ids.join());

            if (response.data.action == "Edit") {
                $scope.getSubAggregateBasedonCategory(self.part.aggregate_id);
                self.item_category_list = response.data.item_category_list;
                self.item_category_list_count = response.data.item_category_list_count;
                if (response.data.from_category==null){
                    self.from_category=response.data.part.category_id;
                }else{
                    self.from_category=response.data.from_category;
                }

            }

            $rootScope.loading = false;
            if (self.action == 'Edit') {
                if (self.part.deleted_at) {
                    self.switch_value = 'Inactive';
                } else {
                    self.switch_value = 'Active';
                }
                if (self.part.tcs_status==0) {
                    self.tcs_status = 'No';
                }else{
                    self.tcs_status = 'Yes';
                }
                //Tax Satus and retail price Save By Parthiban V on 02-08-2021
                if (self.part.tax_status==1) {
                    self.tax_status = 'Yes';
                }else{
                    self.tax_status = 'No';
                }
            } else {
                self.switch_value = 'Active';
                self.tcs_status = 'No';
                self.tax_status = 'Yes'; //Tax Satus and retail price Save By Parthiban V on 02-08-2021
            }
        });

        /* Image Uploadify Funtion */
        $('.image_uploadify').imageuploadify();

        $('.item_available_date').datepicker({
            dateFormat: 'dd-mm-yy',
            changeMonth: true,
            todayHighlight: true,
            autoclose: true,
        });

        $("input:text:visible:first").focus();

        $scope.getSubAggregateBasedonCategory = function(part_category_id) {
            if (part_category_id) {
                $.ajax({
                        url: laravel_routes['getItemSubAggregateByAggregate'],
                        method: "POST",
                        data: { part_category_id: part_category_id },
                    })
                    .done(function(res) {
                        self.part_sub_aggregate_list = [];
                        self.part_sub_aggregate_list = res.part_sub_categories_list;
                        $scope.$apply()
                    })
                    .fail(function(xhr) {
                        console.log(xhr);
                    });
            }
        }

        $scope.getVehicleModelBasedonMake = function(key, vehicle_make_id) {
            $.ajax({
                    url: laravel_routes['getVehicleModelByMake'],
                    method: "POST",
                    data: { vehicle_make_id: vehicle_make_id },
                })
                .done(function(res) {
                    self.vehicle_mappings[key].model_list = [];
                    $(res['vehicle_model_list']).each(function(i, v) {
                        self.vehicle_mappings[key].model_list.push({
                            id: v['id'],
                            name: v['name'],
                        });
                    });
                    $scope.$apply()

                })
                .fail(function(xhr) {
                    console.log(xhr);
                });
        }

        self.addNewVehicleMapping = function() {
            self.vehicle_mappings.push({
                vehicle_category_id: '',
                vehicle_make_id: '',
                vehicle_model_id: '',
                vehicle_year_id: '',
                make_list: [],
                model_list: [],
                years_list: self.years_list,
            });
        }

        $scope.deleteVehicleModelconfirm = function(index, part_mapping_id) {
            $('#delete_part_mapping_id').val(part_mapping_id);
            $('#delete_vehicle_mapping_index').val(index);
        }

        $scope.deleteVehicleCategory = function() {
            var index = $('#delete_vehicle_mapping_index').val();
            var part_mapping_id = $('#delete_part_mapping_id').val();
            if (part_mapping_id) {
                $.ajax({
                        url: delete_part_mapping + '/' + part_mapping_id,
                        method: "GET",
                    })
                    .done(function(res) {
                        console.log(res);
                    })
                    .fail(function(xhr) {
                        console.log(xhr);
                    });
            }
            self.vehicle_mappings.splice(index, 1);
        }

        //GET HSN CODE LIST
        self.searcHsnCode = function(query) {
            if (query) {
                return new Promise(function(resolve, reject) {
                    $http
                        .post(
                            laravel_routes['getHsnCode'], {
                                key: query,
                            }
                        )
                        .then(function(response) {
                            resolve(response.data.tax_code_list);
                        });
                    //reject(response);
                });
            } else {
                return [];
            }
        }

        /* Pane Next Button */
        $('.btn-nxt').on("click", function() {
            $('.editDetails-tabs li.active').next().children('a').trigger("click");
        });
        $('.btn-prev').on("click", function() {
            $('.editDetails-tabs li.active').prev().children('a').trigger("click");
        });

        //Colors Click
        $('.colors').on('click', function() {
            var color = $(this).attr('data-original-title');
            $('.color').val(color);
        });

        //Alternate Search
        $scope.altSearchClose = function() {
            $('#alt_part_search').hide();
            $('#alt_search_close').hide();
        }
        //Search Alternate Parts
        self.searchAlternateParts = function(query) {
            if (query) {
                var alternate_part_ids = $("#alternate_part_ids").val();
                var id = $("#id").val();
                return new Promise(function(resolve, reject) {
                    $http
                        .post(
                            laravel_routes['getNewPartDetail'], {
                                key: query,
                                part_ids: alternate_part_ids,
                                id: id,
                            }
                        )
                        .then(function(response) {
                            resolve(response.data.new_parts_list);
                        });
                    //reject(response);
                });
            } else {
                return [];
            }
        }

        //Add Alternate Parts
        $(document).on('click', '#btn_alt_add', function() {
            var add_part_id = $(this).attr('value');
            $.ajax({
                url: laravel_routes['addNewParts'],
                type: 'get',
                data: { 'add_part_id': add_part_id },
                success: function(response) {
                    var alt_response_parts = response.new_parts;
                    self.alt_parts_ids.push(alt_response_parts.id);
                    $('#alternate_part_ids').val(self.alt_parts_ids.join());

                    self.alt_parts.push({
                        id: alt_response_parts.id,
                        code: alt_response_parts.code,
                        name: alt_response_parts.name,
                        mrp: alt_response_parts.mrp,
                        cost_price: alt_response_parts.cost_price,
                        list_price: alt_response_parts.list_price,
                    });
                    $('#alt_part_search').hide();
                    $('#alt_search_close').hide();
                    $scope.$apply()

                }
            });
            self.alternate_part.code = null;
            return false;
        });

        //Upsell Search
        $scope.upsellSearchClose = function() {
            $('#upsell_part_search').hide();
            $('#upsell_search_close').hide();
        }
        //Search Upsell List
        self.searchUpsellParts = function(query) {
            if (query) {
                var upsell_part_ids = $("#upsell_part_ids").val();
                var id = $("#id").val();
                return new Promise(function(resolve, reject) {
                    $http
                        .post(
                            laravel_routes['getNewPartDetail'], {
                                key: query,
                                part_ids: upsell_part_ids,
                                id: id,
                            }
                        )
                        .then(function(response) {
                            resolve(response.data.new_parts_list);
                        });
                    //reject(response);
                });
            } else {
                return [];
            }
        }
        //Add Upsell Parts
        $(document).on('click', '#btn_upsell_add', function() {
            var add_part_id = $(this).val();
            $.ajax({
                url: laravel_routes['addNewParts'],
                type: 'get',
                data: { 'add_part_id': add_part_id },
                success: function(response) {
                    var upsell_response_parts = response.new_parts;
                    self.upsell_parts_ids.push(upsell_response_parts.id);
                    $('#upsell_part_ids').val(self.upsell_parts_ids.join());

                    self.upsell_parts.push({
                        id: upsell_response_parts.id,
                        code: upsell_response_parts.code,
                        name: upsell_response_parts.name,
                        mrp: upsell_response_parts.mrp,
                        cost_price: upsell_response_parts.cost_price,
                        list_price: upsell_response_parts.list_price,
                    });
                    $('#upsell_part_search').hide();
                    $('#upsell_search_close').hide();
                    $scope.$apply()

                }
            });
            self.upsell_part.code = null;
            return false;
        });

        //Remove Alternate Parts
        self.removeAlternateParts = function(index, alt_part_id) {
            var alt_part_index = self.alt_parts_ids.indexOf(alt_part_id);
            if (alt_part_index > -1) {
                self.alt_parts_ids.splice(alt_part_index, 1);
            }
            $('#alternate_part_ids').val(self.alt_parts_ids.join());
            self.alt_parts.splice(index, 1);
        }

        //Remove Upsell Parts
        self.removeUpsellParts = function(index, upsell_part_id) {
            var upsell_part_index = self.upsell_parts_ids.indexOf(upsell_part_id);
            if (upsell_part_index > -1) {
                self.upsell_parts_ids.splice(upsell_part_index, 1);
            }
            $('#upsell_part_ids').val(self.upsell_parts_ids.join());

            self.upsell_parts.splice(index, 1);
        }

        //ADDED BY KARTHICK T ON 30-07-2020
        self.addNewRack = function() {
            self.rack_parts.push({
                name: '',
                quantity: '',
            });
        }

        $scope.deleteRackModelconfirm = function(index, part_rack_id) {
            $('#delete_rack_mapping_id').val(part_rack_id);
            $('#delete_rack_mapping_index').val(index);
            console.log('part_rack_id  : ' + part_rack_id);
        }

        $scope.deleteRack = function() {
            var index = $('#delete_rack_mapping_index').val();
            var rack_mapping_id = $('#delete_rack_mapping_id').val();
            if (rack_mapping_id) {
                $http({
                    url: laravel_routes['deletePartRack'],
                    method: "POST",
                    params: { 'rack_id': rack_mapping_id }
                }).then(function(response) {

                });
            }
            self.rack_parts.splice(index, 1);
        }
        //ADDED BY KARTHICK T ON 30-07-2020

        jQuery.validator.addMethod("decimal", function(value, element) {
            return this.optional(element) || /^\d{0,10}(\.\d{0,2})?$/i.test(value);
        }, "You must include two decimal places");
        //Form Submit
        var form_id = '#part_form';
        var v = jQuery(form_id).validate({
            invalidHandler: function(event, validator) {
                custom_noty('error', 'You have errors, Please check all tabs');
            },
            ignore: "",
            rules: {
                code: {
                    required: true,
                    maxlength: 50,
                },
                name: {
                    required: true,
                    maxlength: 255,
                },
                min_qty: {
                    number: true,
                    maxlength: 11,
                    min: 0,
                },
                max_qty: {
                    number: true,
                    maxlength: 11,
                    min: 0,
                },
                height: {
                    number: true,
                    min: 0,
                    maxlength: 11,
                },
                width: {
                    number: true,
                    min: 0,
                    maxlength: 11,
                },
                weight: {
                    number: true,
                    min: 0,
                    maxlength: 11,
                },
                part_available_date: {},
                local_lang_name: {
                    maxlength: 100,
                },
                package_qty: {
                    number: true,
                    maxlength: 11,
                    min: 0,
                },
                tax_code_id: {
                    required: true,
                },
                mrp: {
                    required: true,
                    maxlength: 12,
                    number: true,
                    decimal: true,
                },
                cost_price: {
                    maxlength: 12,
                    number: true,
                    decimal: true,
                },
                list_price: {
                    maxlength: 12,
                    number: true,
                    decimal: true,
                },
                display_order: {
                    min: 0,
                    number: true,
                },

            },
            submitHandler: function(form) {
                let formData = new FormData($(form_id)[0]);
                $('.submit').button('loading');
                $.ajax({
                        url: laravel_routes['savePart'],
                        method: "POST",
                        data: formData,
                        processData: false,
                        contentType: false,
                    })
                    .done(function(res) {
                        if (res.success == true) {
                            custom_noty('success', res.message);
                            $location.path('/part-pkg/part/list');
                            $scope.$apply();
                        } else {
                            if (!res.success == true) {
                                $('.submit').button('reset');
                                showErrorNoty(res);
                            } else {
                                $('.submit').button('reset');
                                $location.path('/part-pkg/part/list');
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
        //UPDATED BY KARTHICK T ON 15-07-2020
        //ADD BY KARTHICK T ON 11-08-2020
        $scope.getRackBasedOnType = function(key, type_id) {
            $.ajax({
                    url: laravel_routes['getRackBasedOnType'],
                    method: "POST",
                    data: { type_id: type_id },
                })
                .done(function(res) {
                    self.rack_parts[key].rack_list = [];
                    $(res['rack_list']).each(function(i, v) {
                        self.rack_parts[key].rack_list.push({
                            id: v['id'],
                            name: v['name'],
                        });
                    });
                    $scope.$apply()
                })
                .fail(function(xhr) {
                    console.log(xhr);
                });
        }
        //ADD BY KARTHICK T ON 11-08-2020
        $scope.transferDetail = function(part_id,from_category_id,to_category_id,remarks) {
            $('#submit-transfer').button('loading');
            var skip = false;
            if (from_category_id == 'undefined') {
                skip = true;
                custom_noty('error', 'Please Select From Category');
                $('#submit-transfer').button('reset');
            }
            if (to_category_id == 'undefined') {
                skip = true;
                custom_noty('error', 'Please Select To Category');
                $('#submit-transfer').button('reset');
            }
            if (skip == false) {
                var form_data = new FormData();
                form_data.append('part_id', part_id);
                form_data.append('from_category_id', from_category_id);
                form_data.append('to_category_id', to_category_id);
                form_data.append('remarks', remarks);

                $.ajax({
                    url:  laravel_routes['updatePartCategoryDetail'],
                    method: "POST",
                    data: form_data,
                    processData: false,
                    contentType: false,
                })
                    .done(function(response) {

                        if (!response.success) {
                            custom_noty('error', response.errors);
                            $('#submit-transfer').button('reset');
                        } else {
                            custom_noty('success', response.message);
                            self.item_category_list = response.item_category_list;
                            self.item_category_list_count = response.item_category_list_count;
                            self.from_category=response.from_category;
                            self.to_category.id = '';
                            self.remarks = '';
                            $('#submit-transfer').button('reset');
                            $scope.$apply();

                        }
                    })
                    .fail(function(xhr) {
                        custom_noty('error', 'Something went wrong at server.');
                        $('#upload').button('reset');
                    })
            }
        }

    }
});
//------------------------------------------------------------------------------------------------------------------------
//------------------------------------------------------------------------------------------------------------------------