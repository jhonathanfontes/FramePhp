<?php

namespace App\Controllers\Site;

use Core\Controller\BaseController;

class HomeController extends BaseController
{
    public function index()
    {
        return $this->render('pages/home');
    }

    public function about()
    {
        return $this->render('site/home/about');
    }

    public function contact()
    {
        return $this->render('site/home/contact');
    }

    public function dashboard()
    {
        return $this->render('site/home/dashboard');
    }

    public function profile()
    {
        return $this->render('site/home/profile');
    }
}
