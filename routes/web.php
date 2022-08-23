<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MenusController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\StocksController;
use App\Http\Controllers\PeriodsController;
use App\Http\Controllers\ReasonsController;
use App\Http\Controllers\PlantelsController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\MovementsController;
use App\Http\Controllers\OrderSalesController;
use App\Http\Controllers\TypeMovementsController;
use App\Http\Controllers\OrderSalesLinesController;
use App\Http\Controllers\Auth\AutenticationsController;

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

Route::controller(AutenticationsController::class)
->group(function () {
    Route::get('/login', 'login')->name('login');
    Route::post('/authenticate', 'authenticate')->name('authenticate');
    Route::get('/logout', 'logout')->name('logout');
});

Route::get('/', function () {
    return Inertia::render('Welcome');
})->middleware('auth')->name('welcome');

Route::get('/prb', function () {
    return Inertia::render('Prb');
});

Route::get('/bienvenido', function () {
    return Inertia::render('Bienvenido');
});
/*
Route::get('/users', function () {
    return Inertia::render('UsersIndex');
});*/

Route::prefix('/users')
->middleware('auth')
->name('users.')
->controller(UsersController::class)
->group(function () {
    Route::get('', 'index')->name('index');//->middleware('can:users.index');
    Route::get('/create', 'create')->name('create')->middleware('can:users.create');
    Route::get('/edit/{id}', 'edit')->name('edit')->middleware('can:users.edit');
    Route::post('/assignRolesToAUser', 'assignRolesToAUser');//->middleware('can:users.assignRolesToAUser');
    Route::post('/assignPlantelsToAUser', 'assignPlantelsToAUser')->middleware('can:users.assignPlantelsToAUser');
    Route::get('/show/{id}', 'show')->name('show')->middleware('can:users.show');
    Route::post('/store', 'store')->name('store')->middleware('can:users.store');
    Route::post('/edit/{id}', 'update')->name('update')->middleware('can:users.update');
    Route::delete('/delete/{id}', 'destroy')->name('destroy')->middleware('can:roles.destroy');
});

Route::prefix('/roles')
->middleware('auth')
->name('roles.')
->controller(RolesController::class)
->group(function () {
    Route::get('', 'index')->name('index');//->middleware('can:roles.index');
    Route::get('/create', 'create')->name('create');//->middleware('can:roles.create');
    Route::get('/edit/{id}', 'edit')->name('edit');//->middleware('can:roles.edit');
    Route::get('/show/{id}', 'show')->name('show');//->middleware('can:roles.show');
    Route::post('/store', 'store')->name('store');//->middleware('can:roles.create');
    Route::post('/edit/{id}', 'update')->name('update');//->middleware('can:roles.update');
    Route::post('/assignPermissionsToARole', 'assignPermissionsToARole')->name('assignPermissionsToARole');//->middleware('can:roles.assignPermissionsToARole');
    Route::delete('/delete/{id}', 'destroy')->name('destroy');//->middleware('can:roles.destroy');
});

Route::prefix('/menus')
->middleware('auth')
->name('menus.')
->controller(MenusController::class)
->group(function () {
    Route::get('', 'index')->name('index')->middleware('can:menus.index');
    Route::get('/create', 'create')->name('create')->middleware('can:menus.create');
    Route::get('/edit/{id}', 'edit')->name('edit')->middleware('can:menus.edit');
    Route::get('/show/{id}', 'show')->name('show')->middleware('can:menus.show');
    Route::post('/store', 'store')->name('store')->middleware('can:menus.create');
    Route::post('/edit/{id}', 'update')->name('update')->middleware('can:menus.update');
    Route::delete('/delete/{id}', 'destroy')->name('destroy')->middleware('can:menus.destroy');
});

Route::prefix('/typeMovements')
->middleware('auth')
->name('typeMovements.')
->controller(TypeMovementsController::class)
->group(function () {
    Route::get('', 'index')->name('index')->middleware('can:typeMovements.index');
    Route::get('/create', 'create')->name('create')->middleware('can:typeMovements.create');
    Route::get('/edit/{id}', 'edit')->name('edit')->middleware('can:typeMovements.edit');
    Route::get('/show/{id}', 'show')->name('show')->middleware('can:typeMovements.show');
    Route::post('/store', 'store')->name('store')->middleware('can:typeMovements.create');
    Route::post('/edit/{id}', 'update')->name('update')->middleware('can:typeMovements.update');
    Route::delete('/delete/{id}', 'destroy')->name('destroy')->middleware('can:typeMovements.destroy');
});

Route::prefix('/reasons')
->middleware('auth')
->name('reasons.')
->controller(ReasonsController::class)
->group(function () {
    Route::get('', 'index')->name('index')->middleware('can:reasons.index');
    Route::get('/create', 'create')->name('create')->middleware('can:reasons.create');
    Route::get('/edit/{id}', 'edit')->name('edit')->middleware('can:reasons.edit');
    Route::get('/show/{id}', 'show')->name('show')->middleware('can:reasons.show');
    Route::post('/store', 'store')->name('store')->middleware('can:reasons.create');
    Route::post('/edit/{id}', 'update')->name('update')->middleware('can:reasons.update');
    Route::delete('/delete/{id}', 'destroy')->name('destroy')->middleware('can:reasons.destroy');
});

Route::prefix('/periods')
->middleware('auth')
->name('periods.')
->controller(PeriodsController::class)
->group(function () {
    Route::get('', 'index')->name('index')->middleware('can:periods.index');
    Route::get('/create', 'create')->name('create')->middleware('can:periods.create');
    Route::get('/edit/{id}', 'edit')->name('edit')->middleware('can:periods.edit');
    Route::get('/show/{id}', 'show')->name('show')->middleware('can:periods.show');
    Route::post('/store', 'store')->name('store')->middleware('can:periods.create');
    Route::post('/edit/{id}', 'update')->name('update')->middleware('can:periods.update');
    Route::delete('/delete/{id}', 'destroy')->name('destroy')->middleware('can:periods.destroy');
});

Route::prefix('/plantels')
->middleware('auth')
->name('plantels.')
->controller(PlantelsController::class)
->group(function () {
    Route::get('', 'index')->name('index')->middleware('can:plantels.index');
    Route::get('/create', 'create')->name('create')->middleware('can:plantels.create');
    Route::get('/edit/{id}', 'edit')->name('edit')->middleware('can:plantels.edit');
    Route::get('/show/{id}', 'show')->name('show')->middleware('can:plantels.show');
    Route::post('/store', 'store')->name('store')->middleware('can:plantels.create');
    Route::post('/edit/{id}', 'update')->name('update')->middleware('can:plantels.update');
    Route::delete('/delete/{id}', 'destroy')->name('destroy')->middleware('can:plantels.destroy');
});

Route::prefix('/products')
->middleware('auth')
->name('products.')
->controller(ProductsController::class)
->group(function () {
    Route::get('', 'index')->name('index')->middleware('can:products.index');
    Route::get('/create', 'create')->name('create')->middleware('can:products.create');
    Route::get('/edit/{id}', 'edit')->name('edit')->middleware('can:products.edit');
    Route::get('/show/{id}', 'show')->name('show')->middleware('can:products.show');
    Route::post('/store', 'store')->name('store')->middleware('can:products.create');
    Route::post('/edit/{id}', 'update')->name('update')->middleware('can:products.update');
    Route::delete('/delete/{id}', 'destroy')->name('destroy')->middleware('can:products.destroy');
});

Route::prefix('/stocks')
->middleware('auth')
->name('stocks.')
->controller(StocksController::class)
->group(function () {
    Route::get('', 'index')->name('index')->middleware('can:stocks.index');
    Route::get('/create', 'create')->name('create')->middleware('can:stocks.create');
    Route::get('/edit/{id}', 'edit')->name('edit')->middleware('can:stocks.edit');
    Route::get('/show/{id}', 'show')->name('show')->middleware('can:stocks.show');
    Route::post('/store', 'store')->name('store')->middleware('can:stocks.create');
    Route::post('/edit/{id}', 'update')->name('update')->middleware('can:stocks.update');
    Route::delete('/delete/{id}', 'destroy')->name('destroy')->middleware('can:stocks.destroy');
});

Route::prefix('/orderSales')
->middleware('auth')
->name('orderSales.')
->controller(OrderSalesController::class)
->group(function () {
    Route::get('', 'index')->name('index')->middleware('can:orderSales.index');
    Route::get('/create', 'create')->name('create')->middleware('can:orderSales.create');
    Route::get('/edit/{id}', 'edit')->name('edit')->middleware('can:orderSales.edit');
    Route::get('/show/{id}', 'show')->name('show')->middleware('can:orderSales.show');
    Route::get('/print/{id}', 'print')->name('print')->middleware('can:orderSales.edit');
    Route::post('/store', 'store')->name('store')->middleware('can:orderSales.create');
    Route::post('/edit/{id}', 'update')->name('update')->middleware('can:orderSales.update');
    Route::delete('/delete/{id}', 'destroy')->name('destroy')->middleware('can:orderSales.destroy');
});

Route::prefix('/orderSalesLines')
->middleware('auth')
->name('orderSalesLines.')
->controller(OrderSalesLinesController::class)
->group(function () {
    Route::get('', 'index')->name('index')->middleware('can:orderSalesLines.index');
    Route::get('/create', 'create')->name('create')->middleware('can:orderSalesLines.create');
    Route::get('/edit/{id}', 'edit')->name('edit')->middleware('can:orderSalesLines.edit');
    Route::get('/show/{id}', 'show')->name('show')->middleware('can:orderSalesLines.show');
    Route::get('/receiveOCPlantel/{id}', 'receiveOCPlantel')->name('receiveOCPlantel')->middleware('can:orderSalesLines.receiveOCPlantel');
    Route::post('/store', 'store')->name('store')->middleware('can:orderSalesLines.create');
    Route::post('/edit/{id}', 'update')->name('update')->middleware('can:orderSalesLines.update');
    Route::delete('/delete/{id}', 'destroy')->name('destroy')->middleware('can:orderSalesLines.destroy');
});

Route::prefix('/movements')
->middleware('auth')
->name('movements.')
->controller(MovementsController::class)
->group(function () {
    Route::get('', 'index')->name('index')->middleware('can:movementss.index');
    Route::get('/create', 'create')->name('create')->middleware('can:movementss.create');
    Route::get('/edit/{id}', 'edit')->name('edit')->middleware('can:movementss.edit');
    Route::get('/show/{id}', 'show')->name('show')->middleware('can:movementss.show');
    Route::post('/store', 'store')->name('store')->middleware('can:movementss.create');
    Route::post('/edit/{id}', 'update')->name('update')->middleware('can:movementss.update');
    Route::delete('/delete/{id}', 'destroy')->name('destroy')->middleware('can:movementss.destroy');
});