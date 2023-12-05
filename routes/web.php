<?php
    
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
    
    // Route::get('/', function () {
    //     return redirect(route('home', app()->getLocale()));
    // });

    Route::group([
        'middleware' => 'setlocale'
    ], function() {

        Route::get('/', 'HomeController@index')->name('home');
        
        Route::get("/properties/{required_project_id}","ProjectController@properties")->name('properties');
        
        Auth::routes();

        Route::post('/addFavourite',"ProjectController@addFav");
        Route::get("/wishlist","ProjectController@wishlist");
    
        Route::get("/change-lang/{lang}","ProjectController@changeLang");

        Route::group(['prefix' => '/projects', 'as' => 'projects.'], function () {

            //   generator uploaded image to small size
//            Route::get('/generator', 'ProjectController@generatorSmallUrl')->name('generator');

            Route::get('/', 'ProjectController@index')->name('archive');


            Route::get("/layouts","ProjectController@layouts")->name('layouts');

            Route::get('/{slug}', 'ProjectController@showSingle')->name('single');

            Route::post('/products', 'ProjectController@products')->name('products');
            Route::post('/more', 'ProjectController@loadMore')->name('more');

            Route::post('/getbottomimg/{project}', [
                'uses' => 'ProjectController@getBottomImg',
                'as' => 'getbottomimg',
            ]);
        });

        Route::get('/partners',"PartnerController@index")->name('partners');
    
        Route::get("about","PageController@index")->name('about');
    
        Route::get('/contact',"ContactUsController@show")->name('contact');
        Route::get("/contact-us","ContactUsController@contactForm");
    
        Route::get('/request',"RequiredProjectController@showForm")->name('request');
        Route::post('/request/save',"RequiredProjectController@store")->name('request.save');
        Route::post('/request/unsubscribe/{required_project_id}',"RequiredProjectController@unsubscribe")->name('request.unsubscribe');
    
        Route::get('filter', 'AnnouncementController@filter')->name('announcement.filter');

        Route::get('announcement/{id}', 'AnnouncementController@show')->name('announcement.show');
    });


    
    Route::group([
        'prefix' => '{locale}',
        'where' => ['locale' => '[a-zA-Z]{2}'],
        'middleware' => 'setlocale'
    ], function () {
        // Route::get('/', function () {
        //     // return redirect(app()->getLocale());
        //     return redirect(route('home', app()->getLocale()));
        // });
        // 404
        Route::get('/error', function () {
            return view('pages.error404');
        })->name('error');
        
        // Contact
        Route::get('/contact', 'ContactUsController@show')->name('contact');
        Route::post('/contact', 'ContactUsController@send')->name('contact_send');
        
        // Requirement
        Route::get('/requirement', 'RequiredPropertyController@show')->name('requirement');
        Route::post('/requirement/save', 'RequiredPropertyController@store')->name('requirement.save');


        // Home
        // Route::get('/home', 'HomeController@index')->name('home');
        
        // Projects Route Group
        
        // WishList
        // Route::get('/wishlist', 'WishlistController@getWishListItems')->name('wishlist');
        
        // Announcement Routes
        Route::resource('announcement', 'AnnouncementController')->names([
            'index' => 'announcement',
            'create' => 'announcement.create',
            // 'show' => 'announcement.show',
            'store' => 'announcement.store',
            'edit' => 'announcement.edit',
            'update' => 'announcement.update',
            'delete' => 'announcement.delete'
        ])->except(['index', 'show']);
        Route::post('announcement/store_client_side', 'AnnouncementController@storeClientSide')->name('announcement.store_client_side');

        Route::get('announcement/{announcement_type_id?}/{order_by?}/{order_dir?}', 'AnnouncementController@index')->name('announcement');

        // Announcement Filter Routes
        Route::post('get_place_children', 'AnnouncementController@getPlaceChildren')->name('announcement.get_place_children');
        Route::post('get_price', 'AnnouncementController@getPrice')->name('announcement.get_price');
        Route::post('get_area_price', 'AnnouncementController@getAreaPrice')->name('announcement.get_area_price');


        Route::get('announcement_success', 'AnnouncementController@success')->name('announcement.success');
        
        // Announcement Options Routes
        Route::resource('property', 'PropertyTypeController')->names([
            'index' => 'property',
            'create' => 'property.create',
            'store' => 'property.store',
            'show' => 'property.show',
            'edit' => 'property.edit',
            'update' => 'property.update',
            'delete' => 'property.delete'
        ]);
        
        // Realtors Routes
        Route::resource('realtor', 'RealtorController')->names([
            'index' => 'realtor',
            'create' => 'realtor.create',
            'store' => 'realtor.store',
            'show' => 'realtor.show',
            'edit' => 'realtor.edit',
            'update' => 'realtor.update',
            'delete' => 'realtor.delete'
        ]);
        
        // Realtor`s Announcements load more
        Route::post('load_announcements', [
            'uses' => 'RealtorController@loadAnnouncements',
            'as' => 'load_announcements',
        ]);
        
        // Realtors load more
        Route::post('load_realtors', [
            'uses' => 'RealtorController@loadRealtors',
            'as' => 'load_realtors',
        ]);

        // Page Routes
        Route::get('/pages/{slug}', 'PageController@show')->name('page.show');
        
        // Post Routes
        Route::get('/post/{slug}', 'PostController@show')->name('post.show');
        Route::resource('post', 'PostController')->names([
            'index' => 'post',
            'create' => 'post.create',
            'store' => 'post.store',
            'edit' => 'post.edit',
            'update' => 'post.update',
            'delete' => 'post.delete'
        ]);
        // Posts load more
        Route::post('load_posts', [
            'uses' => 'PostController@loadPosts',
            'as' => 'load_posts',
        ]);
        
        // Search route
        Route::get('/search', 'SearchController@show')->name('search');
        Route::get('/search', 'SearchController@getSearchResult')->name('search_result');

    });


    Route::post('add_sum', [
        'uses' => 'AnnouncementController@addClickCount',
        'as' => 'add_sum',
    ]);

    Route::post('add_filter_ann', [
        'uses' => 'AnnouncementController@filterAnnouncements',
        'as' => 'add_filter_ann',
    ]);


    Route::group(['middleware' => ['auth']], function () {
        Route::group(['prefix' => '/admin', 'as' => 'admin.'], function () {
            Route::get("/",'ProjectController@indexAdmin')->name('archive');
            Route::group(['prefix' => '/projects', 'as' => 'projects.'], function () {

                Route::post('/change/image', 'ProjectController@adminChangeImage')->name('change.image');

                Route::get('/', 'ProjectController@indexAdmin')->name('archive');
                Route::get('/create/{project_id}', [
                    'uses' => 'ProjectController@createProjectAdmin',
                    'as' => 'single.default',
                ]);
                Route::get('/{slug}', 'ProjectController@showAdminSingle')->name('singleadmin');


                Route::post('/storelayoutfile', [
                    'uses' => 'ProjectController@storeLayoutFile',
                    'as' => 'layoutfile',
                ]);
                Route::delete('/delete/{project}', [
                    'uses' => 'ProjectController@destroy',
                    'as' => 'destroy',
                ]);
            });
            Route::group(['prefix' => '/files', 'as' => 'files.'], function () {
                Route::delete('/delete/{files}', [
                    'uses' => 'FilesController@destroy',
                    'as' => 'destroy',
                ]);
            });
        });
        Route::get("/logout","ProjectController@logout");

        Route::get('admin/pages/{id}', 'PageController@edit')->name('admin.pagedit');
		Route::post('admin/pages/edit/{id}','PageController@update');

		Route::get('admin/contact',"ContactUsController@edit")->name('contact.edit');
		Route::post('admin/contact/update',"ContactUsController@update")->name('contact.update');

		Route::get('admin/partners',"PartnerController@edit")->name('partners.edit');
		Route::post('admin/partners/update',"PartnerController@update")->name('partners.update');
		Route::delete('admin/partners/delete/{id}',"PartnerController@destroy");
		Route::delete('admin/partners/deletegroup/{id}',"PartnerController@destroyGroup");

		Route::get('admin/requests',"RequiredProjectController@index")->name('requests');

        Route::post('/updateproject', [
            'uses' => 'ProjectController@updateProject',
            'as' => 'single.update',
        ]);
        Route::post('/storeproject', [
            'uses' => 'ProjectController@storeSingleAdmin',
            'as' => 'single.store',
        ]);
    });