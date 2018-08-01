<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MicropostFavoriteController extends Controller
{
    public function store(Request $request, $id)
    {
        \Auth::micropost()->favorite($id);
        return redirect()->back();
    }

    public function destroy($id)
    {
        \Auth::micropost()->unfavorite($id);
        return redirect()->back();
    }
}
