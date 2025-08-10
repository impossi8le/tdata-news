<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use TData\News\Models\News;
use TData\News\Models\User;

/*
|--------------------------------------------------------------------------
| API Routes with session (AddQueuedCookies, StartSession, ShareErrors)
|--------------------------------------------------------------------------
*/
Route::group([
    'prefix'     => 'api',
    'middleware' => [
        AddQueuedCookiesToResponse::class,
        StartSession::class,
        ShareErrorsFromSession::class,
    ],
], function () {

    //
    // АВТОРИЗАЦИЯ
    //

    // POST /api/login
    Route::post('/login', function (Request $request) {
        $v = Validator::make($request->all(), [
            'login'    => 'required|string',
            'password' => 'required|string',
        ]);

        if ($v->fails()) {
            return response()->json([
                'message' => 'validation error',
                'errors'  => $v->errors(),
            ], 422);
        }

        $user = User::where('login', $request->input('login'))->first();
        if (! $user || ! Hash::check($request->input('password'), $user->password)) {
            return response()->json(['message' => 'invalid credentials'], 401);
        }

        // Обновляем сессию и сохраняем user_id
        $request->session()->regenerate();
        session(['user_id' => $user->id]);

        return response()->json([
            'message' => 'ok',
            'user'    => [
                'id'    => $user->id,
                'login' => $user->login,
                'email' => $user->email,
            ],
        ]);
    });

    // GET /api/me — проверка авторизации
    Route::get('/me', function () {
        return response()->json([
            'authorized' => session()->has('user_id'),
            'user_id'    => session('user_id'),
        ]);
    });

    // POST /api/logout
    Route::post('/logout', function (Request $request) {
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return response()->json(['message' => 'ok']);
    });


    //
    // ПУБЛИЧНЫЕ НОВОСТИ
    //

    // GET /api/news — только опубликованные
    Route::get('/news', function () {
        return News::where('is_published', true)
            ->orderByDesc('published_at')
            ->orderByDesc('created_at')
            ->get();
    });

    // GET /api/news — только опубликованные
    Route::get('/anews', function () {
        return News::orderByDesc('published_at')
            ->orderByDesc('created_at')
            ->get();
    });


    // // GET /api/news/{slug}
    // // гостю — только опубликованные, авторизованному — любые
    Route::get('/news/{slug}', function (Request $request, $slug) {
        $query = News::where('slug', $slug);
        if (! session()->has('user_id')) {
            $query->where('is_published', true);
        }

        if (! $news = $query->first()) {
            return response()->json(['message' => 'not found'], 404);
        }

        return response()->json($news);
    });


    //
    // CRUD ДЛЯ АДМИНКИ (только после проверки сессии)
    //
    Route::group(['middleware' => function ($request, $next) {
        if (! session()->has('user_id')) {
            return response()->json(['message' => 'unauthorized'], 401);
        }
        return $next($request);
    }], function () {

        // GET /api/news/admin — все новости
        Route::get('/news/admin', function () {
            return News::orderByDesc('published_at')
                ->orderByDesc('created_at')
                ->get();
        });

        // POST /api/news — создать
        Route::post('/news', function (Request $request) {
            try {
                $data = $request->validate([
                    'title'        => 'required|string|max:255',
                    'slug'         => 'required|string|max:255|unique:tdata_news_news,slug',
                    'content'      => 'required|string',
                    'published_at' => 'nullable|date',
                    'is_published' => 'boolean',
                ]);
            } catch (ValidationException $e) {
                return response()->json([
                    'message' => 'validation error',
                    'errors'  => $e->errors(),
                ], 422);
            }

            $news = News::create($data);
            return response()->json($news, 201);
        });

        // PUT /api/news/{id} — обновить
        Route::put('/news/{id}', function ($id, Request $request) {
            $news = News::findOrFail($id);
            try {
                $data = $request->validate([
                    'title'        => 'sometimes|required|string|max:255',
                    'slug'         => 'sometimes|required|string|max:255|unique:tdata_news_news,slug,' . $news->id,
                    'content'      => 'sometimes|required|string',
                    'published_at' => 'nullable|date',
                    'is_published' => 'boolean',
                ]);
            } catch (ValidationException $e) {
                return response()->json([
                    'message' => 'validation error',
                    'errors'  => $e->errors(),
                ], 422);
            }

            $news->update($data);
            return response()->json($news);
        });

        // DELETE /api/news/{id}
        Route::delete('/news/{id}', function ($id) {
            News::findOrFail($id)->delete();
            return response()->json(['message' => 'deleted']);
        });
    });
});
