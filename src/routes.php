<?php
namespace Magegate;

\Route::group(array('prefix'=>'/api/magegate'), function()
{
    /**
     * EventController
     */
    \Route::post('events', 'Magegate\EventController@create');
    \Route::get('events', 'Magegate\EventController@index');
    \Route::get('events/{model}/{id}', 'Magegate\EventController@show')
        ->where(array(
            'model' => '[A-Za-z_]+/[A-Za-z_]+',
            'id' => '[0-9]+',
        ));

    /**
     * ImportCatalogCategoryController
     */
    \Route::get('imports/{host}/catalog/category', 'Magegate\ImportCatalogCategoryController@index');
    \Route::get('imports/{host}/catalog/category/{id}', 'Magegate\ImportCatalogCategoryController@show');

});

