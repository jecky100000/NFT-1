<?php

namespace app\admin\controller\apps;

use app\admin\controller\Controller;

class Test extends Controller
{
    public function index(){
        return $this->fetch("index");
    }
}