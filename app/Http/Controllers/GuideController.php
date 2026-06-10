<?php

namespace App\Http\Controllers;

use App\Support\SeoMeta;

class GuideController extends Controller
{
    public function index()
    {
        return view('guide.index', [
            'seoMeta' => SeoMeta::guide(),
        ]);
    }
}
