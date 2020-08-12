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

});