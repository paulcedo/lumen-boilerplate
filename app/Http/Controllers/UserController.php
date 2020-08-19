<?php

namespace App\Http\Controllers;

use App\Users\Repositories\Interfaces\UserRepositoryInterface;
use App\Users\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    private $userRepo;

    /**
     * Create a new controller instance.
     *
     * @param UserRepositoryInterface $userRepo
     */
    public function __construct(UserRepositoryInterface $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:2|max:50',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|regex:/^[A-Za-z0-9_.@$]+$/',
            'c_password' => 'required|same:password|min:6|regex:/^[A-Za-z0-9_.@$]+$/',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        return $this->userRepo->createUser([
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>password_hash($request->password, PASSWORD_DEFAULT)
        ]);
    }

    public function show($id){
        return $this->userRepo->findUserById($id);
    }

    public function index(){
        return $this->userRepo->listAllUsers();
    }

    public function update($id, Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:2|max:50',
            'email' => ['required','email',Rule::unique("users", 'email')->ignore($id)]
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $user = $this->userRepo->findUserById($id);
        $userRepo = new UserRepository($user);
        return $userRepo->updateUser($request->all());
    }

    public function delete($id){
        $user = $this->userRepo->findUserById($id);
        $userRepo = new UserRepository($user);
        return $userRepo->deleteUser();
    }
}
