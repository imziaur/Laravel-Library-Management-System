<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;
use Carbon\Carbon;
use App\IssueBook;

class IssueBookController extends Controller
{
    protected $successStatus = 200;
    protected $rentPerBook = 2;
    protected $maxBook = 5;
    /**
     * Issue book method.
     *
     * @return \Illuminate\Http\Response
     */
    public function issue_book(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'book_id' => 'required', 
            'user_id' => 'required' 
        ]);
        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()]);
        }
        $input = $request->all();
        $book = IssueBook::where([['user_id', $input['user_id']], ['book_id', $input['book_id']]])->whereNull('return_date')->get()->first();
        if($book){
            return response()->json(['error'=>'This book is already issued to the person'], 400); 
        }
        $bookCount = IssueBook::where('user_id', $input['user_id'])->whereNull('return_date')->get()->count();       
        if($bookCount>=$this->maxBook){
            return response()->json(['error'=>'Can not issue a book to this person. Reached max limit.'], 400); 
        }
        $issue = IssueBook::create($input);
        $success['data'] =  $issue; 
        return response()->json(['success'=>$success], $this->successStatus);        
    }

    public function return_book(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'book_id' => 'required', 
            'user_id' => 'required',
            'rent' => 'required'
        ]);
        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()]);
        }
        $input = $request->all();
        $book = IssueBook::where([['user_id', $input['user_id']], ['book_id', $input['book_id']]])->whereNull('return_date')->first();
        if(!$book){
            return response()->json(['error'=>'No record found']);
        }
        
        $data['rent'] = $input['rent'];
        $data['return_date'] = date('Y-m-d');
        $issue = $book->update($data);
        $success['data'] =  $issue; 
        return response()->json(['success'=>$success], $this->successStatus);
    }

    public function rent_to_pay(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'book_id' => 'required', 
            'user_id' => 'required'
        ]);
        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()]);
        }
        $input = $request->all();
        $book = IssueBook::where([['user_id', $input['user_id']], ['book_id', $input['book_id']]])->whereNull('return_date')->first();
        if(!$book){
            return response()->json(['error'=>'No record found']);
        }
        $issue_date = Carbon::parse($book->issue_date);
        $days = $issue_date->diffInDays(Carbon::now());
        if($days<1){
            $rent = $this->rentPerBook;
        } else {
            $rent = $days*$this->rentPerBook;
        }
        $success['rent'] = $rent; 
        return response()->json(['success'=>$success], $this->successStatus);
    }

    public function issue_books(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'date' => 'required'
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()]);
        }
        try
        {
            $input = $request->all();
            $books = IssueBook::where('issue_date', '=', $input['date'])->whereNull('return_date')->with('book', 'user')->get();
            $success['data'] =  $books; 
            return response()->json(['success'=>$success], $this->successStatus);
        } catch (\Exception $e) {
            return response()->json(['error'=>$e]);
        }
        
    }

    public function return_books(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'date' => 'required'
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()]);
        }
        try
        {
            $input = $request->all();
            $books = IssueBook::where('return_date', '=', $input['date'])->with('book', 'user')->get();
            $success['data'] =  $books; 
            return response()->json(['success'=>$success], $this->successStatus);
        } catch (\Exception $e) {
            return response()->json(['error'=>$e]);
        }
        
    }

    public function total_rent(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'date' => 'required'
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()]);
        }
        try
        {
            $input = $request->all();
            $rent = IssueBook::where('return_date', '=', $input['date'])->sum('rent');
            $success['data'] =  $rent; 
            return response()->json(['success'=>$success], $this->successStatus);
        } catch (\Exception $e) {
            return response()->json(['error'=>$e]);
        }
        
    }

}
