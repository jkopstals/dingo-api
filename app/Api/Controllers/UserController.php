<?php

namespace Api\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use API;

use App\Models\User;
use Api\Transformers\UserTransformer;

/**
 * Class for handling user specific endpoints
 */
class UserController extends Controller
{
    /**
     * Verify user credentials and return a token
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function authenticate(Request $request) 
    {
        $credentials = $request->only('email', 'password');

        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                return $this->response->error('invalid_credentials', 401);
            }
        } catch (JWTException $e) {
            return $this->response->error('could_not_create_token', 500);
        }

        return $this->response->array(compact('token'));
    }

    /**
     * Validate token
     *
     * @return \Illuminate\Http\Response
     */
    public function validateToken() 
    {
        // Route already authenticated this token, so return success
        //return API::response()->array(['status' => 'success'])->statusCode(200);

        return $this->response->noContent();
    }

    /**
     * Return current user info
     *
     * @return \Illuminate\Http\Response
     */
    public function me()
    {
        $user = JWTAuth::parseToken()->authenticate();
        return $this->response->item($user, new UserTransformer);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::paginate(10);

        return $this->response->paginator($users, new UserTransformer);
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user =  User::findOrFail($id);

        return $this->response->item($user, new UserTransformer);
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
