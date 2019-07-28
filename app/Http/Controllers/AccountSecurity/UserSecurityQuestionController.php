<?php

namespace App\Http\Controllers\AccountSecurity;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\SecurityQuestion;
use Session;
use DB;
use Validator;

//helper
use App\Traits\StatusHttp;

class UserSecurityQuestionController extends Controller
{
    use StatusHttp;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $result = SecurityQuestion::all();

        return response()->json([
            'data' => $result ? $result : [],
            'http_code' => $this->getStatusCode200(),
            'status' => __('messages.status_success'),
        ], $this->getStatusCode200())
            ->header(__('messages.header_convo'), Session::getId());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //get the answer array count
        $countAnswers = $request->answers;

        if ($countAnswers <= 0) {
            return response()->json([
                'message' => __('messages.empty_security_questions'),
                'http_code' => $this->getStatusCode404(),
                'status' => __('messages.status_error')
            ]);
        }

        //make validation
        $validator = Validator::make($request->all(), [
            'answers.*.security_question_answer' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors(),
                'http_code' => $this->getStatusCode400(),
                'status' => __('messages.status_error')
            ]);
        }

        $securityQuestions = new SecurityQuestion();

        for($x=0; $x < count($request->answers); $x++) {
            $securityQuestions->storeSecurityQuestionAnswer($request->answers[$x]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
