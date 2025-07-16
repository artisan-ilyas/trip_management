<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function about()
    {
        return view('about');
    }    

    public function trips()
    {
        return view('trips');
    }  

    public function blog()
    {
        return view('blog');
    }    
    
    public function contact()
    {
        return view('contact');
    }


}
