<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request){

        $users = User::latest();

        if(!empty($request->get('keyword'))){
            $users = $users->where('name','like','%'.$request->get('keyword').'%');
            $users = $users->orWhere('email','like','%'.$request->get('keyword').'%');
        }

        $users = $users->paginate(10);
        $data['users'] = $users;

        return view('admin.users.index',$data);
    }

    public function create(){

        return view('admin.users.create');
    }

    public function store(Request $request){

        $validator = Validator::make($request->all(),[

            'name' => 'required',
            'password' => 'required|min:5',
            'email' => 'required|email|unique:users',
            'phone' => 'required',

        ]);

        if($validator->passes()){

            $user = new User;
            $user->name = $request->name;
            $user->email = $request->email; 
            $user->phone = $request->phone; 
            $user->status = $request->status; 
            $user->password = Hash::make($request->password); 
            $user->save();

            $message = "User Added Successfully";
            
            session()->flash('success',$message);

            return response()->json([
                'status' =>  true,
                'message' => $message,
            ]);

        }
        else{
            return response()->json([
                'status' =>false,
                'errors' => $validator->errors(),
            ]);
        }
    }

    public function edit(Request $request, $id){

        $user = User::find($id);
        if($user == null){
            session()->flash('error','User not found');
            return redirect()->route('users.index');
        }
        $data['user'] = $user;
        return view('admin.users.edit',$data);
    }

    public function update(Request $request, $id){

        $user = User::find($id);
        if($user == null){
            $message = 'User not found';
            session()->flash('error',$message);
            return response()->json([
                'status' =>  true,
                'message' => $message,
            ]);
        }

        $validator = Validator::make($request->all(),[

            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$id.',id',
            'phone' => 'required',

        ]);

        if($validator->passes()){

            $user->name = $request->name;
            $user->email = $request->email; 
            $user->phone = $request->phone; 
            $user->status = $request->status; 
            if($request->password != ''){
                $user->password = Hash::make($request->password); 
            }
            $user->save();

            $message = "User Update Successfully";
            
            session()->flash('success',$message);

            return response()->json([
                'status' =>  true,
                'message' => $message,
            ]);

        }
        else{
            return response()->json([
                'status' =>false,
                'errors' => $validator->errors(),
            ]);
        }
    }

    public function destroy($id){

        $user = User::find($id);
        if($user == null){
            $message = 'User not found';
            session()->flash('error',$message);
            return response()->json([
                'status' =>  true,
                'message' => $message,
            ]);
        }

        $user->delete();
        $message = 'User Deleted Successfully';
        session()->flash('success',$message);
        return response()->json([
            'status' =>  true,
            'message' => $message,
        ]);
    }
}
