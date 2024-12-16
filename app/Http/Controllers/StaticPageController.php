<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StaticPageController extends Controller
{
    public function showAboutPage()
    {
        return view('pages.about');
    }

    public function showContactsPage()
    {
        return view('pages.contacts');
    }
}
