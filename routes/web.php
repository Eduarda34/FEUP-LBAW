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
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SearchController;

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

// Static Pages
/* Route::controller(StaticPageController::class)->group(function () {
    Route::get('/about', 'showAboutPage').>name('about');
}); */

// Users
Route::controller(UserController::class)->group(function () {
    Route::get('/users', 'list');
    Route::get('/users/categories', 'listCategories');
    Route::get('/users/notifications', 'showNotifications');
    Route::get('/users/{id}', 'show')->name('user.profile');
    Route::get('/users/{id}/edit', 'showProfileEditorForm');
    Route::get('/users/{id}/followers', 'showFollowers');
    Route::get('/users/{id}/following', 'showFollowing');
    Route::get('/users/{id}/report', 'showReportForm');
    // API
    Route::post('/api/users/categories/{category_id}', 'followCategory');
    Route::delete('/api/users/categories/{category_id}', 'unfollowCategory');
    Route::delete('/api/users/{id}', 'delete');
    Route::put('/api/users/{id}/edit', 'update');
    Route::post('/api/users/{id}/follow', 'follow');
    Route::delete('/api/users/{id}/unfollow', 'unfollow');
    Route::post('api/users/{id}/report', 'report');
});

// Posts
Route::controller(PostController::class)->group(function () {
    Route::get('/posts', 'list')->name('posts');
    Route::get('/posts/create', 'showPostCreatorForm');
    Route::get('/posts/favorites', 'favorites')->name('posts.favorites');
    Route::get('/posts/category/{category_id}', 'listByCategory')->name('posts.category');
    Route::get('/posts/{post_id}', 'show')->name('posts.show');
    Route::get('/posts/{post_id}/edit', 'showPostEditorForm')->name('posts.edit');
    // API
    Route::post('/api/posts', 'create')->name('posts.create');
    Route::put('/api/posts/{post_id}', 'update')->name('posts.update');
    Route::delete('/api/posts/{post_id}', 'delete')->name('posts.delete');
    Route::post('/api/posts/{post_id}/vote', 'vote')->name('posts.vote');
    Route::put('/api/posts/{post_id}/vote', 'editVote')->name('posts.vote.edit');
    Route::delete('/api/posts/{post_id}/vote', 'removeVote')->name('posts.vote.remove');
    Route::post('/api/posts/{post_id}/favorites', 'addToFavorites')->name('posts.favorites.add');
    Route::delete('/api/posts/{post_id}/favorites', 'removeFromFavorites')->name('posts.favorites.remove');
    // System Manager
    Route::delete('/sys/posts/{post_id}', 'forceDelete');
});

// Comments
Route::controller(CommentController::class)->group(function () {
    Route::get('/posts/{post_id}/comments', 'list')->name('comments.list');
    Route::put('/comments/{comment_id}', 'showCommentEditorForm')->name('comments.edit');
    // API
    Route::post('/api/posts/{post_id}/comments', 'create')->name('comments.create');
    Route::put('/api/comments/{comment_id}', 'update')->name('comments.update');
    Route::delete('/api/comments/{comment_id}', 'delete')->name('comments.delete');
    Route::post('/api/comments/{comment_id}/reply', 'reply')->name('comments.reply');
    Route::post('/api/comments/{comment_id}/vote', 'vote')->name('comments.vote');
    Route::put('/api/comments/{comment_id}/vote', 'editVote')->name('comments.editVote');
    Route::delete('/api/comments/{comment_id}/vote', 'removeVote')->name('comments.removeVote');
});

//System Manager
/*  Route::controller(AdminController::class)->group(function () {
    Route::put('/sys/users/{id}/block', 'blockUser')->name('admin.users.block');
    Route::put('/sys/users/{id}/unblock', 'unblockUser')->name('admin.users.unblock');
    Route::get('/sys/reports', 'listReports')->name('admin.reports.list');
    Route::put('/sys/reports/{report_id}/resolve', 'resolveReport')->name('admin.reports.resolve');
    Route::post('/sys/categories', 'addCategory')->name('admin.categories.add');
    Route::put('/sys/categories/{category_id}', 'updateCategory')->name('admin.categories.update');
}); */

// Search
 Route::controller(SearchController::class)->group(function () {
    Route::get('/search/posts', 'searchPosts')->name('search.posts');
    //Route::get('/search/comments', 'searchComments')->name('search.comments');
    Route::get('/search/users', 'searchUsers')->name('search.users');
    //Route::get('/search/categories', 'searchCategories')->name('search.categories');
});