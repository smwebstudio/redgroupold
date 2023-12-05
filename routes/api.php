<?php
    
    use Illuminate\Http\Request;
    
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
    
    // Route::middleware('auth:api')->get('/user', function (Request $request) {
    //     return $request->user();
    // });
    
    Route::group([
            'prefix' => '{locale}',
            'where' => ['locale' => '[a-zA-Z]{2}'],
            'middleware' => 'setlocale'
        ], function () {
         //   Route::post('/announcement/store', 'AnnouncementController@store');
            Route::get('/announcement/{id}', 'AnnouncementController@show');
            // Route::get('/example', function(){
            //     return response()->json(['data' => 'rrr']);
            // });
        });
   
