<?php
//Route::get('/', ['as' => 'admin.dashboard', function () {
//	$content = view('catalog::dashboard');
//	return AdminSection::view($content, 'Панель управления');
//}]);

//Route::get('/manager', ['as' => 'admin.manager', function () {
//	$content = view('catalog::manager');
//	return AdminSection::view($content, 'Панель управления');
//}]);
	// $router->get('/', ['as' => 'admin.dashboard', function (\SleepingOwl\Admin\Contracts\Template\TemplateInterface $template) {
	//     return $template->view('catalog::manager', ['11']);
	// }]);
Route::post('api/checkMakeCountersCommandProcess', function(){
	$data = Cache::store('file')->get('catalog_command_makecounters_data', []);
	$data['complete'] = !Cache::store('file')->get('catalog_command_makecounters_work');
	return response()->json($data);
});

Route::post('api/dispatchMakeCountersCommand', function(){
	$worked = Cache::store('file')->get('catalog_command_makecounters_work', false);
	if(!$worked){
      	Cache::store('file')->put('catalog_command_makecounters_work', 1, 60);
		$worked = Cache::store('file')->get('catalog_command_makecounters_work');
		//Artisan::call('queue:listen');
      	//Artisan::call('catalog:makecounters');
		dispatch(new \Softlab\Catalog\Jobs\MakeCounters);
		return response()->json(true);
	}
	return response()->json(false);	
});

