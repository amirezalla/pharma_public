<?php

Route::group(['namespace' => 'Botble\Ecommerce\Http\Controllers', 'middleware' => ['web', 'core']], function () {
    Route::group(['prefix' => BaseHelper::getAdminPrefix(), 'middleware' => 'auth'], function () {
        Route::group(['prefix' => 'questionaries', 'as' => 'questionary.'], function () {
            Route::get('', [
                'as' => 'index',
                'uses' => 'QuestionnaireController@index',
            ]);
//            Route::get('getView', [
//                'as' => 'getView',
//                'uses' => 'QuestionnaireController@getView',
////                'permission' => ['customers.index', 'orders.index'],
//            ]);
//            Route::group(['prefix' => 'questionnaire', 'as' => 'ecommerce.questionnaire.'], function () {
//                Route::get('/getView', [QuestionnaireController::class, 'getView']);
//                Route::post('/createQuestionnaire', [QuestionnaireController::class, 'createQuestionnaire']);
//                Route::post('/createQuestions', [QuestionnaireController::class, 'createQuestions']);
//                Route::post('/saveAnswers', [QuestionnaireController::class, 'saveAnswers']);
//                Route::get('/getAnswers', [QuestionnaireController::class, 'getAnswers']);
//
//                Route::get('/viewQuestionnaire', [QuestionnaireController::class, 'viewQuestionnaire']);
//
//
//
//            });

            Route::delete('items/destroy', [
                'as' => 'deletes',
                'uses' => 'TaxController@deletes',
                'permission' => 'tax.destroy',
            ]);
        });
    });
});
