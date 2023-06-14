<?php

use App\Models\Task;
use Illuminate\Support\Facades\Route;
use App\Http\Resources\API\User\SimpleUserResource;

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

Route::get('/', function () {
    return redirect('/tasks');
});

Route::get('/tasks', function () {
    $signedInUser = new SimpleUserResource(auth()->user());
    $statuses = Task::STATUSES;
    $priorities = Task::PRIORITIES;

    return view('tasks', compact('signedInUser', 'statuses', 'priorities'));
})->middleware(['auth', 'verified'])->name('tasks');

require __DIR__ . '/auth.php';
