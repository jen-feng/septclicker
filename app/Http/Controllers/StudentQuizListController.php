<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StudentQuizListController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index($user)
    {
        $user = auth()->user();
        //$this->authorize('view', $user->profile);
        //dd($user->profile);
            // TODO: Whoever's doing quiz creation, there's no actual field that says whether or not it's enabled
            return view('studentside/quiz/quizlist', ['quizzes' => \App\Quiz::get()]);
    }

}
