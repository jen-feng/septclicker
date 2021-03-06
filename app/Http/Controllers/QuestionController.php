<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class QuestionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function create(\App\Quiz $quiz){
        return view('instructorside.quiz.addquestion', compact('quiz'));
    }

    public function store(\App\Quiz $quiz){

        $data = request()->validate([
            'question_text' => 'required',
            'question_ans' => 'required',
            'image' => '',
            'quiz_id' => '',
            'option_a' => '',
            'option_b' => '',
            'option_c' => '',
            'option_d' => '',
            'option_e' => '',
            'testbankadd' => '',
        ]);
        //Takes the quiz we are working on tht was passed into the create function
        //Create a new question object with question()
        //And add the data to the new question for the quiz we're working on
        if (request('image') != null) {
            $imagePath = request('image')->store('uploads', 'public');
        }
        else {$imagePath = null;}

        $quiz->question()->create([
            'question_text' => $data['question_text'],
            'question_ans' => $data['question_ans'],
            'image' => $imagePath,
            'quiz_id' =>  $data['quiz_id'],
            'option_a' => $data['option_a'],
            'option_b' =>  $data['option_b'],
            'option_c' =>  $data['option_c'],
            'option_d' =>  $data['option_d'],
            'option_e' => $data['option_e'],
        ]);

        if (isset($data['testbankadd'])){
            $q  = DB::select('select * from questions where (question_text,question_ans) = (?,?)',array($data['question_text'],$data['question_ans']));
            $iid = auth()->user()->id;
            $qid = $q[0]->id;

            DB::insert('insert into test_bank (instructor_id,question_id) values (?,?)',array($iid,$qid));
        }

        return view('instructorside.quiz.addquestion', compact('quiz'));
    }

    public function edit(\App\Quiz $quiz, \App\Question $question){

        return view('instructorside/quiz/question/edit', compact('quiz', 'question'));
    }



    // public function show(\App\Quiz $quiz){
    //     return view('instructorside.quiz.addquestion', compact('quiz'));
    // }

    public function update(\App\Quiz $quiz, \App\Question $question){
        $data = request()->validate(
            ['question_text' => '',
            'question_ans' => '',
            'image' => '',
            'quiz_id' => '',
            'option_a' => '',
            'option_b' => '',
            'option_c' => '',
            'option_d' => '',
            'option_e' => '',
            'testbankadd' => '',
            ]
        );

        if (request('image') != null) {
            $imagePath = request('image')->store('uploads', 'public');
        }
        else {$imagePath = null;} //second option is driver to store your file.
                                                    //there are diff drivers, s3 for amazon, but we have local storage under public
        $question-> update([
            'question_text' => $data['question_text'],
            'question_ans' => $data['question_ans'],
            'image' => $imagePath,
            'quiz_id' =>  $data['quiz_id'],
            'option_a' => $data['option_a'],
            'option_b' =>  $data['option_b'],
            'option_c' =>  $data['option_c'],
            'option_d' =>  $data['option_d'],
            'option_e' => $data['option_e'],
        ]);

        //Get question id
        $q  = DB::select('select * from questions where (question_text,question_ans) = (?,?)',array($data['question_text'],$data['question_ans']));
        $qid = $q[0]->id;

        //Check if question is in testbank
        $contains = DB::select('select * from test_bank where question_id = ?',array($qid));

        //Question is in test bank
        if (isset($contains)){
            if (! isset($data['testbankadd'])){
                DB::delete('delete from test_bank where question_id = ?', array($qid));
            }
        }
        //Question not in test bank
        else {
            if (isset($data['testbankadd'])){
                DB::insert('insert into test_bank (instructor_id,question_id) values (?,?)', array($iid,$qid));
            }
        }

        //if (isset($data['testbankadd'])){
           // $q  = DB::select('select * from questions where (question_text,question_ans) = (?,?)',array($data['question_text'],$data['question_ans']));
            //$iid = auth()->user()->id;
            //$qid = $q[0]->id;

            //DB::insert('insert into test_bank (instructor_id,question_id) values (?,?)',array($iid,$qid));
        //}

        
        //auth()->user()->quiz()->update($data);

        //dd($question->id);


        return redirect('q/'.$question->quiz_id);

    }
}
