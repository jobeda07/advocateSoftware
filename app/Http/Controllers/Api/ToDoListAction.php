<?php

namespace App\Http\Controllers\Api;

use App\Models\ToDoList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ToDoListRequest;
use App\Http\Resources\ToDoListResource;

class ToDoListAction extends Controller
{
    public function index(){
        try {
            $todo_list = ToDoList::where('created_by', Auth::user()->id)->orderBy('id','desc')->paginate(50);
            return response()->json(['todo_list_data' => ToDoListResource::collection($todo_list) ,'status'=>200]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Something went wrong: ' . $e->getMessage() ,
                'status'=>500
            ]);
        }
    }
    public function store(ToDoListRequest $request)
    {
        DB::beginTransaction();
        try {
            $listData = ToDoList::create([
                'title' => $request->title,
                'deadline' => $request->deadline,
                'note' => $request->note,
                'created_by' => Auth::user()->id
            ]);


            DB::commit();

            return response()->json([
                'todo-list-data' => new ToDoListResource($listData),
                'message' => 'Data created successfully',
            ], 201);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'error' => 'Something went wrong: ' . $e->getMessage() ,
                'status' => 500,
            ]);
        }
    }


    public function update(ToDoListRequest $request, $id){

        DB::beginTransaction();
        try{
            $listData = ToDoList::where('created_by', Auth::user()->id)->where('id', $id)->first();
            if(!isset($listData)){
                return response()->json([
                    'status'=>false,
                    'message' =>'Data not found',
                ], 404);
            }

            $listData->title = $request->title;
            $listData->deadline = $request->deadline;
            $listData->note = $request->note;
            $listData ->save();
            DB::commit();
            return response([
                'todo-list-data'=> new ToDoListResource($listData),
                'message' => 'Data Updated successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'error' => 'Something went wrong: ' . $e->getMessage() ,
                'status'=>500
            ]);
        }
    }

    public function delete($id){
        DB::beginTransaction();
        try{
            $todoList = ToDoList::where('created_by', Auth::user()->id)->where('id', $id)->first();
            if (!isset($todoList)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Data Not Found',
                ], 404);
            }
            if($todoList){
                $todoList->delete();
            }

            DB::commit();
            return response([
                'message' => ' Data Deleted successfully'
            ]);
        }catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'error' => 'Something went wrong: ' . $e->getMessage() ,
                'status'=>500
            ]);
        }
    }

    public function show($id){
        try {
            $todoList = ToDoList::where('created_by', Auth::user()->id)->where('id', $id)->first();
            if (!isset($todoList)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Data Not Found',
                ], 404);
            }
            if ($todoList){
                $show_todo = new ToDoListResource($todoList);
            }

            return response()->json([
                'show_todo_list' =>$show_todo,
                'status'=>200
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Something went wrong: ' . $e->getMessage() ,
                'status'=>500
            ]);
        }
    }
}
