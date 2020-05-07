<?php

Route::group(['namespace' => 'Abs\PartPkg','middleware' => ['web', 'auth'], 'prefix' => 'part-pkg'], function () {

	
		//Part
		Route::get('/part/get-list', 'PartController@getPartList')->name('getPartList');
		Route::get('/part/get-form-data', 'PartController@getPartFormData')->name('getPartFormData');
		Route::post('/part/save', 'PartController@savePart')->name('savePart');
		Route::get('/part/delete', 'PartController@deletePart')->name('deletePart');
		Route::get('/part/get-filter-data', 'PartController@getPartFilterData')->name('getPartFilterData');

		

});