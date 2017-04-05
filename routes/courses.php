<?php

$app->group('/courses', function () use ($app) {
    $app->get('', 'App\Controllers\CourseController:index')->setName('courses');

    $app->get('/category/{id}', 'App\Controllers\CourseController:category')->setName('courses-category');

    $app->get('/{id:[0-9]+}', 'App\Controllers\CourseController:datail')->setName('course');

    $app->get('/action/{id:[0-9]+}', 'App\Controllers\CourseController:action')->setName('course-action');

    $app->group('', function () use ($app) {
        $app->get('/all', 'App\Controllers\CourseController:all')->setName('all-courses');

        $app->get('/new', 'App\Controllers\CourseController:form')->setName('new-course');
        $app->post('/new', 'App\Controllers\CourseController:formPost');

        $app->get('/edit/{id:[0-9]+}', 'App\Controllers\CourseController:form')->setName('edit-course');
        $app->post('/edit/{id:[0-9]+}', 'App\Controllers\CourseController:formPost');

        $app->get('/delete/{id:[0-9]+}', 'App\Controllers\CourseController:delete')->setName('delete-course');
        $app->post('/delete/{id:[0-9]+}', 'App\Controllers\CourseController:deletePost');
    })->add('App\Middlewares\Authorization\AdminMiddleware');
})->add('App\Middlewares\Authorization\UserMiddleware');
