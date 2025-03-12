<?php

namespace App\Http\Controllers\Requerimientos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PersonalController extends Controller
{
    public function index(){
        return view('requerimientos.personal.index');
    }
}
