<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MenusController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\StocksController;
use App\Http\Controllers\PeriodsController;
use App\Http\Controllers\ReasonsController;
use App\Http\Controllers\AccountsController;
use App\Http\Controllers\PaymentsController;
use App\Http\Controllers\PlantelsController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\CashBoxesController;
use App\Http\Controllers\MovementsController;
use App\Http\Controllers\ObsEntriesController;
use App\Http\Controllers\OrderSalesController;
use App\Http\Controllers\StPaymentsController;
use App\Http\Controllers\LnCashBoxesController;
use App\Http\Controllers\StCashBoxesController;
use App\Http\Controllers\TypeMovementsController;
use App\Http\Controllers\PaymentMethodsController;
use App\Http\Controllers\OrderSalesLinesController;
use App\Http\Controllers\OrderDevolutionsController;
use App\Http\Controllers\Auth\AutenticationsController;
use App\Http\Controllers\OrderDevolutionLinesController;

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
    Route::get('/editPerfil/{id}', 'editPerfil')->name('editPerfil')->middleware('can:users.editPerfil');
    Route::post('/assignRolesToAUser', 'assignRolesToAUser');//->middleware('can:users.assignRolesToAUser');
    Route::post('/assignPlantelsToAUser', 'assignPlantelsToAUser')->middleware('can:users.assignPlantelsToAUser');
    Route::get('/show/{id}', 'show')->name('show')->middleware('can:users.show');
    Route::post('/store', 'store')->name('store')->middleware('can:users.store');
    Route::post('/edit/{id}', 'update')->name('update')->middleware('can:users.update');
    Route::post('/editPerfil/{id}', 'updatePerfil')->name('updatePerfil')->middleware('can:users.editPerfil');
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
    Route::get('/findItem/{id}', 'findItem')->name('findItem');//->middleware('can:menus.show');
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
    Route::get('/findById', 'findById')->name('findById');
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
    Route::get('consultaExistencias', 'consultaExistencias')->name('consultaExistencias');
    Route::get('', 'index')->name('index')->middleware('can:movements.index');
    Route::get('/create', 'create')->name('create')->middleware('can:movements.create');
    Route::get('/edit/{id}', 'edit')->name('edit')->middleware('can:movements.edit');
    Route::get('/show/{id}', 'show')->name('show')->middleware('can:movements.show');
    Route::get('/verEntradas', 'verEntradas')->name('verEntradas');
    Route::get('/verEntradasSalidasF', 'verEntradasSalidasF')->name('verEntradasSalidasF')->middleware('can:movements.verEntradasSalidasF');
    Route::post('/verEntradasSalidasR', 'verEntradasSalidasR')->name('verEntradasSalidasR')->middleware('can:movements.verEntradasSalidasF');;
    Route::post('/store', 'store')->name('store')->middleware('can:movements.create');
    Route::post('/edit/{id}', 'update')->name('update')->middleware('can:movements.update');
    Route::delete('/delete/{id}', 'destroy')->name('destroy')->middleware('can:movements.destroy');
    Route::get('/cortePlantel', 'cortePlantel')->name('cortePlantel')->middleware('can:movements.cortePlantel');
    Route::post('/cortePlantelR', 'cortePlantelR')->name('cortePlantelR')->middleware('can:movements.cortePlantel');
    Route::get('/corteGeneral', 'corteGeneral')->name('corteGeneral')->middleware('can:movements.corteGeneral');
    Route::post('/corteGeneralR', 'corteGeneralR')->name('corteGeneralR')->middleware('can:movements.corteGeneral');
    Route::get('/corteToeic', 'corteToeic')->name('corteToeic')->middleware('can:movements.corteToeic');
    Route::post('/corteToeicR', 'corteToeicR')->name('corteToeicR')->middleware('can:movements.corteToeic');
});

Route::prefix('/stPayments')
->middleware('auth')
->name('stPayments.')
->controller(StPaymentsController::class)
->group(function () {
    Route::get('', 'index')->name('index')->middleware('can:stPayments.index');
    Route::get('/create', 'create')->name('create')->middleware('can:stPayments.create');
    Route::get('/edit/{id}', 'edit')->name('edit')->middleware('can:stPayments.edit');
    Route::get('/show/{id}', 'show')->name('show')->middleware('can:stPayments.show');
    Route::post('/store', 'store')->name('store')->middleware('can:stPayments.create');
    Route::post('/edit/{id}', 'update')->name('update')->middleware('can:stPayments.update');
    Route::delete('/delete/{id}', 'destroy')->name('destroy')->middleware('can:stPayments.destroy');
});

Route::prefix('/stCashBoxes')
->middleware('auth')
->name('stCashBoxes.')
->controller(StCashBoxesController::class)
->group(function () {
    Route::get('', 'index')->name('index')->middleware('can:stCashBoxes.index');
    Route::get('/create', 'create')->name('create')->middleware('can:stCashBoxes.create');
    Route::get('/edit/{id}', 'edit')->name('edit')->middleware('can:stCashBoxes.edit');
    Route::get('/show/{id}', 'show')->name('show')->middleware('can:stCashBoxes.show');
    Route::post('/store', 'store')->name('store')->middleware('can:stCashBoxes.create');
    Route::post('/edit/{id}', 'update')->name('update')->middleware('can:stCashBoxes.update');
    Route::delete('/delete/{id}', 'destroy')->name('destroy')->middleware('can:stCashBoxes.destroy');
});

Route::prefix('/paymentMethods')
->middleware('auth')
->name('paymentMethods.')
->controller(PaymentMethodsController::class)
->group(function () {
    Route::get('', 'index')->name('index')->middleware('can:paymentMethods.index');
    Route::get('/create', 'create')->name('create')->middleware('can:paymentMethods.create');
    Route::get('/edit/{id}', 'edit')->name('edit')->middleware('can:paymentMethods.edit');
    Route::get('/show/{id}', 'show')->name('show')->middleware('can:paymentMethods.show');
    Route::get('/consultaPorcentajeDescuento', 'consultaPorcentajeDescuento')->name('consultaPorcentajeDescuento');
    Route::post('/store', 'store')->name('store')->middleware('can:paymentMethods.create');
    Route::post('/edit/{id}', 'update')->name('update')->middleware('can:paymentMethods.update');
    Route::delete('/delete/{id}', 'destroy')->name('destroy')->middleware('can:paymentMethods.destroy');
});

Route::prefix('/cashBoxes')
->middleware('auth')
->name('cashBoxes.')
->controller(CashBoxesController::class)
->group(function () {
    Route::get('', 'index')->name('index')->middleware('can:cashBoxes.index');
    Route::get('/create', 'create')->name('create')->middleware('can:cashBoxes.create');
    Route::get('/edit/{id}', 'edit')->name('edit')->middleware('can:cashBoxes.edit');
    Route::get('/show/{id}', 'show')->name('show')->middleware('can:cashBoxes.show');
    Route::get('/cancelCashBox/{id}', 'cancelCashBox')->name('cancelCashBox')->middleware('can:cashBoxes.cancelCashBox');
    Route::get('/ticket/{id}', 'ticket')->name('ticket')->middleware('can:cashBoxes.ticket');
    Route::get('/rptCajasApartadasF', 'rptCajasApartadasF')->name('rptCajasApartadasF')->middleware('can:cashBoxes.rptCajasApartadas');
    Route::post('/rptCajasApartadasR', 'rptCajasApartadasR')->name('rptCajasApartadasR')->middleware('can:cashBoxes.rptCajasApartadas');
    Route::post('/store', 'store')->name('store')->middleware('can:cashBoxes.create');
    Route::post('/update', 'update')->name('update')->middleware('can:cashBoxes.update');
    Route::delete('/delete/{id}', 'destroy')->name('destroy')->middleware('can:cashBoxes.destroy');
});

Route::prefix('/lnCashBoxes')
->middleware('auth')
->name('lnCashBoxes.')
->controller(LnCashBoxesController::class)
->group(function () {
    Route::get('', 'index')->name('index')->middleware('can:lnCashBoxes.index');
    Route::get('/create', 'create')->name('create')->middleware('can:lnCashBoxes.create');
    Route::get('/edit/{id}', 'edit')->name('edit')->middleware('can:lnCashBoxes.edit');
    Route::get('/show/{id}', 'show')->name('show')->middleware('can:lnCashBoxes.show');
    Route::post('/store', 'store')->name('store')->middleware('can:lnCashBoxes.create');
    Route::post('/update', 'update')->name('update')->middleware('can:lnCashBoxes.update');
    Route::delete('/delete', 'destroy')->name('destroy')->middleware('can:lnCashBoxes.destroy');
});

Route::prefix('/payments')
->middleware('auth')
->name('payments.')
->controller(PaymentsController::class)
->group(function () {
    Route::get('', 'index')->name('index')->middleware('can:payments.index');
    Route::get('/create', 'create')->name('create')->middleware('can:payments.create');
    Route::get('/edit/{id}', 'edit')->name('edit')->middleware('can:payments.edit');
    Route::get('/show/{id}', 'show')->name('show')->middleware('can:payments.show');
    Route::post('/store', 'store')->name('store')->middleware('can:payments.create');
    Route::post('/update', 'update')->name('update')->middleware('can:payments.update');
    Route::delete('/delete', 'destroy')->name('destroy')->middleware('can:payments.destroy');
});

Route::prefix('/orderDevolutions')
->middleware('auth')
->name('orderDevolutions.')
->controller(OrderDevolutionsController::class)
->group(function () {
    Route::get('', 'index')->name('index')->middleware('can:orderDevolutions.index');
    Route::get('/create/{id?}', 'create')->name('create')->middleware('can:orderDevolutions.create');
    Route::get('/edit/{id}', 'edit')->name('edit')->middleware('can:orderDevolutions.edit');
    Route::get('/show/{id}', 'show')->name('show')->middleware('can:orderDevolutions.show');
    Route::get('/registrarDevolucion/{id}', 'registrarDevolucion')->name('registrarDevolucion')->middleware('can:orderDevolutions.registrarDevolucion');
    Route::post('/store', 'store')->name('store')->middleware('can:orderDevolutions.create');
    Route::post('/edit/{id}', 'update')->name('update')->middleware('can:orderDevolutions.update');
    Route::delete('/delete/{id}', 'destroy')->name('destroy')->middleware('can:orderDevolutions.destroy');
});

Route::prefix('/orderDevolutionLines')
->middleware('auth')
->name('orderDevolutionLines.')
->controller(OrderDevolutionLinesController::class)
->group(function () {
    Route::get('', 'index')->name('index')->middleware('can:orderDevolutionLines.index');
    Route::get('/create', 'create')->name('create')->middleware('can:orderDevolutionLines.create');
    Route::get('/edit/{id}', 'edit')->name('edit')->middleware('can:orderDevolutionLines.edit');
    Route::get('/show/{id}', 'show')->name('show')->middleware('can:orderDevolutionLines.show');
    Route::get('/registrarDevolucion/{id}', 'registrarDevolucion')->name('registrarDevolucion')->middleware('can:orderDevolutionLines.registrarDevolucion');
    Route::post('/store', 'store')->name('store')->middleware('can:orderDevolutionLines.create');
    Route::post('/storeLines', 'storeLines')->name('storeLines')->middleware('can:orderDevolutionLines.create');
    Route::post('/edit/{id}', 'update')->name('update')->middleware('can:orderDevolutionLines.update');
    Route::delete('/delete', 'destroy')->name('destroy')->middleware('can:orderDevolutionLines.destroy');
});

Route::prefix('/obsEntries')
->middleware('auth')
->name('obsEntries.')
->controller(ObsEntriesController::class)
->group(function () {
    Route::get('', 'index')->name('index')->middleware('can:obsEntries.index');
    Route::get('/create', 'create')->name('create')->middleware('can:obsEntries.create');
    Route::get('/edit/{id}', 'edit')->name('edit')->middleware('can:obsEntries.edit');
    Route::get('/show/{id}', 'show')->name('show')->middleware('can:obsEntries.show');
    Route::get('/verObservaciones', 'verObservaciones')->name('verObservaciones')->middleware('can:obsEntries.show');
    Route::post('/store', 'store')->name('store')->middleware('can:obsEntries.create');
    Route::post('/edit/{id}', 'update')->name('update')->middleware('can:obsEntries.update');
    Route::delete('/delete/{id}', 'destroy')->name('destroy')->middleware('can:obsEntries.destroy');
});

Route::prefix('/accounts')
->middleware('auth')
->name('accounts.')
->controller(AccountsController::class)
->group(function () {
    Route::get('', 'index')->name('index')->middleware('can:accounts.index');
    Route::get('/create', 'create')->name('create')->middleware('can:accounts.create');
    Route::get('/edit/{id}', 'edit')->name('edit')->middleware('can:accounts.edit');
    Route::get('/show/{id}', 'show')->name('show')->middleware('can:accounts.show');
    Route::post('/store', 'store')->name('store')->middleware('can:accounts.create');
    Route::post('/edit/{id}', 'update')->name('update')->middleware('can:accounts.update');
    Route::delete('/delete/{id}', 'destroy')->name('destroy')->middleware('can:accounts.destroy');
});

Route::prefix('/outputs')
->middleware('auth')
->name('outputs.')
->controller(OutputsController::class)
->group(function () {
    Route::get('', 'index')->name('index')->middleware('can:outputs.index');
    Route::get('/create', 'create')->name('create')->middleware('can:outputs.create');
    Route::get('/edit/{id}', 'edit')->name('edit')->middleware('can:outputs.edit');
    Route::get('/show/{id}', 'show')->name('show')->middleware('can:outputs.show');
    Route::post('/store', 'store')->name('store')->middleware('can:outputs.create');
    Route::post('/edit/{id}', 'update')->name('update')->middleware('can:outputs.update');
    Route::delete('/delete/{id}', 'destroy')->name('destroy')->middleware('can:outputs.destroy');
});
