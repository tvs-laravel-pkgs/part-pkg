<?php
Route::group(['namespace' => 'Abs\PartPkg\Api', 'middleware' => ['api', 'auth:api']], function () {
	Route::group(['prefix' => 'api/part-pkg'], function () {
		//Route::post('punch/status', 'PunchController@status');
	});
});