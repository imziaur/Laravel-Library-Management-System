<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('issue/book', 'IssueBookController@issue_book');
Route::post('return/book', 'IssueBookController@return_book');
Route::post('issue/book/list', 'IssueBookController@issue_books');
Route::post('return/book/list', 'IssueBookController@return_books');
Route::post('total/rent', 'IssueBookController@total_rent');
Route::post('book/charge', 'IssueBookController@rent_to_pay');

