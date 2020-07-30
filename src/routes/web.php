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
		

});