<?php

Route::group(['namespace' => 'Abs\PartPkg','middleware' => ['web', 'auth'], 'prefix' => 'part-pkg'], function () {

	
		//Part
		Route::get('/part/get-list', 'PartController@getPartList')->name('getPartList');
		Route::get('/part/get-form-data', 'PartController@getPartFormData')->name('getPartFormData');
		Route::post('/part/save', 'PartController@savePart')->name('savePart');
		Route::get('/part/delete', 'PartController@deletePart')->name('deletePart');
		Route::get('/part/get-filter-data', 'PartController@getPartFilterData')->name('getPartFilterData');
		//CREATED BY KARTHICK T ON 15-07-2020
		Route::POST('/part/get-sub-aggregate-data', 'PartController@getItemSubAggregateByAggregate')->name('getItemSubAggregateByAggregate');
		Route::POST('/part/get-hsn-code-data', 'PartController@getHsnCode')->name('getHsnCode');
		Route::POST('/part/get-vehicle-model-data', 'PartController@getVehicleModelByMake')->name('getVehicleModelByMake');
		Route::POST('/part/get-alternate-part-data', 'PartController@getNewPartDetail')->name('getNewPartDetail');
		Route::get('/part/get-new-part-data', 'PartController@addNewParts')->name('addNewParts');
		//CREATED BY KARTHICK T ON 15-07-2020
		//CREATED BY KARTHICK T ON 30-07-2020
		Route::POST('/part/delete-rack', 'PartController@deletePartRack')->name('deletePartRack');
		//CREATED BY KARTHICK T ON 30-07-2020
		//ADD BY KARTHICK T ON 11-08-2020
		Route::POST('/part/get-rack-data', 'PartController@getRackBasedOnType')->name('getRackBasedOnType');
		//ADD BY KARTHICK T ON 11-08-2020
		
		//By karthick t on 12-08-2020
		//AGGREGATE
		Route::get('/aggregate-list/get', 'AggregateController@getAggregateList')->name('getAggregateList');
		Route::get('/aggregate-details/get', 'AggregateController@getAggregateFormDetails')->name('getAggregateFormDetails');
		Route::POST('/aggregate-details/save', 'AggregateController@saveAggregate')->name('saveAggregate');
		//SUB AGGREGATE
		Route::get('/sub-aggregate-list/get', 'SubAggregateController@getSubAggregateList')->name('getSubAggregateList');
		Route::get('/sub-aggregate-details/get', 'SubAggregateController@getSubAggregateFormDetails')->name('getSubAggregateFormDetails');
		Route::POST('/sub-aggregate-details/save', 'SubAggregateController@saveSubAggregate')->name('saveSubAggregate');
		//Brand
		Route::get('/brand-list/get', 'BrandController@getBrandList')->name('getBrandList');
		Route::get('/brand-details/get', 'BrandController@getBrandFormDetails')->name('getBrandFormDetails');
		Route::POST('/brand-details/save', 'BrandController@saveBrand')->name('saveBrand');
		//Variant
		Route::get('/variant-list/get', 'VariantController@getVariantList')->name('getVariantList');
		Route::get('/variant-details/get', 'VariantController@getVariantFormDetails')->name('getVariantFormDetails');
		Route::POST('/variant-details/save', 'VariantController@saveVariant')->name('saveVariant');
		//Component
		Route::get('/component-list/get', 'ComponentController@getComponentList')->name('getComponentList');
		Route::get('/component-details/get', 'ComponentController@getComponentFormDetails')->name('getComponentFormDetails');
		Route::POST('/component-details/save', 'ComponentController@saveComponent')->name('saveComponent');
		//Rack 
		Route::get('/rack-filter-details/get', 'RackController@getRackFilterDetails')->name('getRackFilterDetails');
		Route::get('/rack-list/get', 'RackController@getRackList')->name('getRackList');
		Route::get('/rack-details/get', 'RackController@getRackFormDetails')->name('getRackFormDetails');
		Route::POST('/rack-details/save', 'RackController@saveRack')->name('saveRack');
		//By karthick t on 12-08-2020

		//Added by karthick t on 17-08-2020
		//For discount group
		Route::get('/discount-group-filter-details/get', 'DiscountGroupController@getFilterData')->name('getDiscountGrpFilterData');
		Route::get('/discount-group-details/list', 'DiscountGroupController@getList')->name('getDiscountGroupList');
		Route::get('/discount-group-form-details/get', 'DiscountGroupController@getFormData')->name('getDiscountGroupFormDetails');
		Route::POST('/discount-group-form-details/save', 'DiscountGroupController@saveData')->name('saveDiscountGroup');
		Route::get('/discount-group-form-details/delete', 'DiscountGroupController@deleteData')->name('deleteDiscountGroup');

		//For price discount
		Route::get('/price-discount-filter-details/get', 'PriceDiscountController@getFilterData')->name('getPriceDiscountFilterData');
		Route::get('/price-discount-details/list', 'PriceDiscountController@getList')->name('getPriceDiscountList');
		Route::get('/price-discount-form-details/get', 'PriceDiscountController@getFormData')->name('getPriceDiscountFormDetails');
		Route::POST('/price-discount-form-details/save', 'PriceDiscountController@saveData')->name('savePriceDiscount');
		Route::get('/price-discount-form-details/delete', 'PriceDiscountController@deleteData')->name('deletePriceDiscount');
		//Added by karthick t on 17-08-2020
    //Part categories
    Route::post('/part/category/update', 'PartController@updatePartCategoryDetail')->name('updatePartCategoryDetail');

		//Part Price Details by Rajarajan S on 20-09-2021
		Route::get('/part-price-detail/get-list', 'PartPriceDetailController@getPartPriceDetailList')->name('getPartPriceDetailList');
		Route::get('/part-price-detail/get-form-data', 'PartPriceDetailController@getPartPriceDetailFormData')->name('getPartPriceDetailFormData');
		Route::post('/part-price-detail/save', 'PartPriceDetailController@savePartPriceDetail')->name('savePartPriceDetail');
		Route::POST('/part-price-detail/get-part-code-data', 'PartPriceDetailController@getPart')->name('getPart');
		//Part Price Details by Rajarajan S on 20-09-2021


});