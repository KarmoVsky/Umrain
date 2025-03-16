<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DatabaseController;
use App\Http\Controllers\MyFatoorahController;
use App\Http\Controllers\LocationController;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/clear/{clear?}', function ($clear=NULL) {

    if($clear=='cache'){Artisan::call('cache:clear');}
    elseif($clear=='view'){Artisan::call('view:clear');}
    elseif($clear=='config'){Artisan::call('config:clear');}
    elseif($clear=='route'){Artisan::call('route:clear');}
    elseif($clear=='dump-autoload'){shell_exec('composer dump-autoload');}
    else{
      /* Artisan::call('cache:clear');
      Artisan::call('config:clear');
      Artisan::call('route:clear');
      Artisan::call('view:clear'); */
      Artisan::call('optimize:clear');
    }
      return redirect('/'.app()->getLocale().'#success')->with('success', 'OK, Clear view&cache&config');
});

Route::middleware('auth', 'verified')->controller(LocationController::class)->group(function (){
    Route::get('/countries', 'getCountries');
    Route::get('/states/{country_id}', 'getStates');
    Route::get('/cities/{country_code}/{state_code}', 'getCities');
});


Route::get('/payment-report', [MyFatoorahController::class, 'report'])->name('payment.report');


Route::get('/intro', 'LandingpageController@index');
Route::get('/', 'HomeController@index');
Route::get('/home', 'HomeController@index')->name('home');
Route::post('/install/check-db', 'HomeController@checkConnectDatabase');

// Social Login
Route::get('social-login/{provider}', 'Auth\LoginController@socialLogin');
Route::get('social-callback/{provider}', 'Auth\LoginController@socialCallBack');

// Logs
Route::get(config('admin.admin_route_prefix') . '/logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index')->middleware(['auth', 'dashboard', 'system_log_view'])->name('admin.logs');

Route::get('/install', 'InstallerController@redirectToRequirement')->name('LaravelInstaller::welcome');
Route::get('/install/environment', 'InstallerController@redirectToWizard')->name('LaravelInstaller::environment');
Route::fallback([\Modules\Core\Controllers\FallbackController::class, 'FallBack']);

// Hide page update default
Route::get('/update', 'InstallerController@redirectToHome');
Route::get('/update/overview', 'InstallerController@redirectToHome');
Route::get('/update/database', 'InstallerController@redirectToHome');

