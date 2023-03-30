<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('jwt.verify');
Route::post('/register', [UserController::class, 'register'])->middleware('jwt.verify');
Route::post('/projects', [ProjectController::class, 'createProject'])->middleware('jwt.verify');
Route::get('/projects', [ProjectController::class, 'getAllProjects'])->middleware('jwt.verify');
Route::get('/projects/{project}', [ProjectController::class, 'getProject'])->middleware('jwt.verify');
Route::patch('/projects/{project}', [ProjectController::class, 'modifyProject'])->middleware('jwt.verify');
Route::patch('/projects/{project}/{status}', [ProjectController::class, 'modifyProjectStatus'])->middleware('jwt.verify');
Route::get('/projects/{project}/tasks', [TaskController::class, 'getAllTasks'])->middleware('jwt.verify');
Route::post('/projects/{project}/tasks', [TaskController::class, 'createTask'])->middleware('jwt.verify');
Route::get('/projects/{project}/tasks/{task}', [TaskController::class, 'getTask'])->middleware('jwt.verify');
Route::patch('/projects/{project}/tasks/{task}', [TaskController::class, 'modifyTask'])->middleware('jwt.verify');
Route::patch('/projects/{project}/tasks/{task}/{status}', [TaskController::class, 'modifyTaskStatus'])->middleware('jwt.verify');
