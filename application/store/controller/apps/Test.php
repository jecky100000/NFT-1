<?php

namespace app\store\controller\apps;

use app\store\controller\Controller;

class Test extends Controller
{
    public function index()
    {
        return $this->fetch("index");
    }
}