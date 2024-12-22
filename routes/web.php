<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\CardController;
use App\Http\Controllers\ItemController;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;

use App\Http\Controllers\Auth\ViewNewController;

use App\Http\Controllers\StaticPageController;

use App\Http\Controllers\UserController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SystemManagerController;

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
Route::controller(StaticPageController::class)->group(function () {
    Route::get('/about', 'showAboutPage')->name('about');
    Route::get('/contacts', 'showContactsPage')->name('contacts');
});

// Users
Route::controller(UserController::class)->group(function () {
    Route::get('/users', 'list')->name('users.list');
    Route::get('/users/categories', 'listCategories');
    Route::get('/users/notifications', 'showNotifications')->name('user.notifications');
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
    Route::delete('/api/users/{id}/follow', 'unfollow');
    Route::post('api/users/{id}/report', 'report');
    Route::put('api/users/notifications/{notification_id}', 'viewNotification');
    Route::delete('api/users/notifications/{notification_id}', 'deleteNotification');
    Route::put('api/users/notifications', 'viewAllNotifications');
    Route::delete('api/users/notifications', 'deleteAllNotifications');
    Route::delete('/account/delete', [UserController::class, 'deleteAccount'])->middleware('auth');
});

// Posts
Route::controller(PostController::class)->group(function () {
    Route::get('/posts', 'list')->name('posts');
    Route::get('/posts/create', [PostController::class, 'showPostCreatorForm'])->name('posts.create');
    Route::get('/posts/favorites', 'favorites')->name('posts.favorites');
    Route::get('/posts/category/{category_id}', 'listByCategory')->name('posts.category');
    Route::get('/posts/{post_id}', 'show')->name('posts.show');
    Route::get('/posts/{post_id}/edit', 'showPostEditorForm')->name('posts.edit');
    Route::get('/posts/{post_id}/report', 'showReportForm')->name('posts.report');
    // API
    Route::post('/api/posts', [PostController::class, 'create'])->name('posts.store');
    Route::put('/api/posts/{post_id}', 'update')->name('posts.update');
    Route::delete('/api/posts/{post_id}', 'delete')->name('posts.delete');
    Route::post('/api/posts/{post_id}/vote', 'vote')->name('posts.vote');
    Route::put('/api/posts/{post_id}/vote', 'editVote')->name('posts.vote.edit');
    Route::delete('/api/posts/{post_id}/vote', 'removeVote')->name('posts.vote.remove');
    Route::post('/api/posts/{post_id}/favorites', 'addToFavorites')->name('posts.favorites.add');
    Route::delete('/api/posts/{post_id}/favorites', 'removeFromFavorites')->name('posts.favorites.remove');
    Route::post('api/posts/{post_id}/report', 'report');
    Route::delete('/comments/{comment}', 'CommentController@delete')->name('comments.delete');
    // System Manager
    Route::delete('/sys/posts/{post_id}', 'forceDelete');
});

// Comments
Route::controller(CommentController::class)->group(function () {
    Route::get('/posts/{post_id}/comments', 'list')->name('comments.list');
    Route::get('/comments/{comment_id}/edit', 'showCommentEditorForm')->name('comments.edit');
    Route::get('/comments/{comment_id}/report', 'showReportForm')->name('comments.report');
    // API
    Route::post('/api/posts/{post_id}/comments', 'create')->name('comments.create');
    Route::put('/api/comments/{comment_id}', 'update')->name('comments.update');
    Route::delete('/api/comments/{comment_id}', 'delete')->name('comments.delete');
    Route::post('/api/comments/{comment_id}/reply', 'reply')->name('comments.reply');
    Route::post('/api/comments/{comment_id}/vote', 'vote')->name('comments.vote');
    Route::put('/api/comments/{comment_id}/vote', 'editVote')->name('comments.editVote');
    Route::delete('/api/comments/{comment_id}/vote', 'removeVote')->name('comments.removeVote');
    Route::post('api/comments/{comment_id}/report', 'report');
});

//System Manager
 Route::controller(SystemManagerController::class)->group(function () {
    Route::put('/sys/users/{id}/block', 'blockUser')->name('system.users.block');
    Route::put('/sys/users/{id}/unblock', 'unblockUser')->name('system.users.unblock');
    Route::get('/sys/reports', 'listReports')->name('system.reports.list');
    Route::put('/sys/reports/{report_id}/resolve', 'resolveReport')->name('system.reports.resolve');
    Route::post('/sys/categories', 'addCategory')->name('system.categories.add');
    Route::put('/sys/categories/{category_id}', 'updateCategory')->name('system.categories.update');
    Route::put('/sys/categories/{category_id}', 'deleteCategory')->name('system.categories.delete');
});

// Search
 Route::controller(SearchController::class)->group(function () {
    Route::get('/search/posts', 'searchPosts')->name('search.posts');
    //Route::get('/search/comments', 'searchComments')->name('search.comments');
    Route::get('/search/users', 'searchUsers')->name('search.users');
    //Route::get('/search/categories', 'searchCategories')->name('search.categories');
});