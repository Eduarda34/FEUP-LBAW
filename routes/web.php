<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\CardController;
use App\Http\Controllers\ItemController;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;

use App\Http\Controllers\Auth\ViewNewController;

use App\Http\Controllers\UserController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\CategoryController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Home
Route::redirect('/', '/login');

// Cards
Route::controller(CardController::class)->group(function () {
    Route::get('/cards', 'list')->name('cards');
    Route::get('/cards/{id}', 'show');
});

// API
Route::controller(CardController::class)->group(function () {
    Route::put('/api/cards', 'create');
    Route::delete('/api/cards/{card_id}', 'delete');
});

Route::controller(ItemController::class)->group(function () {
    Route::put('/api/cards/{card_id}', 'create');
    Route::post('/api/item/{id}', 'update');
    Route::delete('/api/item/{id}', 'delete');
});


// Authentication
Route::controller(LoginController::class)->group(function () {
    Route::get('/login', 'showLoginForm')->name('login');
    Route::post('/login', 'authenticate');
    Route::get('/logout', 'logout')->name('logout');
});

Route::controller(RegisterController::class)->group(function () {
    Route::get('/register', 'showRegistrationForm')->name('register');
    Route::post('/register', 'register');
});

// Users
Route::controller(UserController::class)->group(function () {
    Route::get('/users/{id}', 'show')->name('user.profile');
    Route::delete('/api/users/{id}', 'delete');
    Route::get('/users/{id}/edit', 'showProfileEditorForm');
    Route::put('/api/users/{id}/edit', 'update');
    Route::get('/users/{id}/followers', 'showFollowers');
    Route::get('/users/{id}/following', 'showFollowing');
    Route::post('/api/users/{id}/follow', 'follow');
    Route::delete('/api/users/{id}/unfollow', 'unfollow');
    Route::get('/users/{id}/report', 'showReportForm');
    Route::post('api/users/{id}/report', 'report');
    Route::get('/users/notifications', 'showNotifications');
});

// Categories
/* Route::controller(CategoryController::class)->group(function () {
    Route::get('/users/categories', 'list');
    Route::post('/api/users/categories/{category_id}', 'follow');
    Route::delete('/api/users/categories/{category_id}', 'unfollow');
}); */

// Posts
Route::controller(PostController::class)->group(function () {
    Route::get('/posts', 'list')->name('posts');
    Route::get('/posts/{post_id}', 'show');
    Route::post('/api/posts', 'create')->name('posts.create');
    Route::get('/posts/{post_id}/edit', 'showPostEditorForm')->name('posts.edit');
    Route::put('/api/posts/{post_id}', 'update')->name('posts.update');
    Route::delete('/api/posts/{post_id}', 'delete')->name('posts.delete');
    Route::get('/posts/category/{category_id}', 'listByCategory')->name('posts.category');
    Route::post('/api/posts/{post_id}/vote', 'vote')->name('posts.vote');
    Route::put('/api/posts/{post_id}/vote', 'editVote')->name('posts.vote.edit');
    Route::delete('/api/posts/{post_id}/vote', 'removeVote')->name('posts.vote.remove');
    Route::get('/posts/favorites', 'favorites')->name('posts.favorites');
    Route::post('/api/posts/{post_id}/favorites', 'addToFavorites')->name('posts.favorites.add');
    Route::delete('/api/posts/{post_id}/favorites', 'removeFromFavorites')->name('posts.favorites.remove');
});

// Comments
/* Route::controller(CommentController::class)->group(function () {
    Route::post('/api/posts/{post_id}/comments', 'add')->name('comments.add');
    Route::get('/posts/{post_id}/comments', 'list')->name('comments.list');
    Route::put('/comments/{comment_id}', 'showCommentEditorForm')->name('comments.edit');
    Route::put('/api/comments/{comment_id}', 'update')->name('comments.update');
    Route::delete('/api/comments/{comment_id}', 'delete')->name('comments.delete');
    Route::post('/api/comments/{comment_id}/reply', 'reply')->name('comments.reply');
    Route::post('/api/comments/{comment_id}/vote', 'vote')->name('comments.vote');
    Route::put('/api/comments/{comment_id}/vote', 'editVote')->name('comments.editVote');
    Route::delete('/api/comments/{comment_id}/vote', 'removeVote')->name('comments.removeVote');
});
 */