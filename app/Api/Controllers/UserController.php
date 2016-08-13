<?php

namespace Api\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;

use App\Models\User;
use Api\Transformers\UserTransformer;
use Api\Requests\UserRequest;

use JWTAuth;

/**
* User resource representation.
*   
* @Resource("Users")
**/
class UserController extends Controller
{
    /**
     * Verify user credentials and return a token
     *
     * @Post("/auth")
     * @Versions({"v1"})
     * @Request(contentType="application/x-www-form-urlencoded")
     * @Parameters({
     *      @Parameter("email", description="Email", required=true),
     *      @Parameter("password", description="Password", required=true),
     * })
     * @Response(200, body={"token": "NEW_TOKEN"})
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
     * @Get("/validateToken")
     * @Versions({"v1"})
     * @Parameters({
     *      @Parameter("token", description="User authentication token", required=true)
     * })
     * @Response(204)
     *
     * @return \Illuminate\Http\Response
     */
    public function validateToken() 
    {
        return $this->response->noContent();
    }

    /**
     * Show current user
     *
     * Get a JSON representation of current user (jwt auth)
     *
     * @Get("/users/me")
     * @Versions({"v1"})
     * @Parameters({
     *      @Parameter("token", description="User authentication token", required=true)
     * })
     * @Response(200, body={"data":
     *  {"id":1,"name":"Janis Kopstals","email":"jk@jk.jk","created_at":"2016-08-10 23:35:52","updated_at":"2016-08-10 23:35:52"}
     * })
     *
     * @return \Illuminate\Http\Response
     */
    public function me()
    {
        $user = JWTAuth::parseToken()->authenticate();
        return $this->response->item($user, new UserTransformer);
    }


    /**
     * List all users (paginated)
     *
     * Get a JSON representation of all the users
     *
     * @Get("/users/{?page,limit}")
     * @Versions({"v1"})
     * @Parameters({
     *      @Parameter("token", description="User authentication token", required=true),
     *      @Parameter("page", description="The page of results to view.", default=1),
     *      @Parameter("limit", description="The amount of results per page.", default=10)
     * })
     * @Response(200, body={
     * "data":
     *  {
     *   {"id":1,"name":"Janis Kopstals","email":"jk@jk.jk","created_at":"2016-08-10 23:35:52","updated_at":"2016-08-10 23:35:52"}
     *  },
     * "meta":{
     *  "pagination":{
     *   "total":51,"count":10,"per_page":10,"current_page":1,"total_pages":6,
     *   "links":{"next":"http:\/\/localhost:8000\/api\/users?page=2"}
     *   }
     *  }
     * })
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::paginate(10);

        return $this->response->paginator($users, new UserTransformer);
    }

    /**
     * List user registration rules
     *
     * Get the validation rules applied to register a new user
     *
     * @Get("/users/rules")
     * @Versions({"v1"})
     * @Response(200,body={"data":{
     *  "name":"string|required|min:2",
     *  "email":"email|required|unique",
     *  "password":"string|min:8",
     *  "password_confirmation":"required|same:password"
     * }})
     *
     * @return \Illuminate\Http\Response
     */
    public function rules()
    {
        return $this->response->array(['data' => User::getRules()]);
    }

    /**
     * Register a new user
     *
     * @Post("/users")
     * @Versions({"v1"})
     * @Request(contentType="application/x-www-form-urlencoded")
     * @Parameters({
     *      @Parameter("name", description="Name of user", required=true),
     *      @Parameter("email", description="Email", required=true),
     *      @Parameter("password", description="Password", required=true),
     *      @Parameter("password_confirmation", description="Password confirmation", required=true)
     * })
     * @Response(201, body={"data":
     *  {"id":1,"name":"Janis Kopstals","email":"jk@jk.jk","created_at":"2016-08-10 23:35:52","updated_at":"2016-08-10 23:35:52"}
     * })
     * @Response(422, body={"message":
     *  "422 Unprocessable Entity",
     *  "errors":{"email":{"The email has already been taken."}},
     *  "status_code":422
     * })
     *
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserRequest $request)
    {
        return $this->response->item(User::create($request->only($request->fillable())), new UserTransformer)->setStatusCode(201);
    }

    /**
     * Show specific user (by id)
     *
     * Get a JSON representation of current user (jwt auth)
     *
     * @Get("/users/{id}")
     * @Versions({"v1"})
     * @Parameters({
     *      @Parameter("id", type="integer", required=true, description="(URI) User ID"),
     *      @Parameter("token", description="User authentication token", required=true),
     * })
     * @Response(200, body={"data":{"id":1,"name":"Janis Kopstals","email":"jk@jk.jk","created_at":"2016-08-10 23:35:52","updated_at":"2016-08-10 23:35:52"}})
     *
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
     * Update user
     *
     * Update user fields, return updated resource.
     *
     * @Post("/users/{id}")
     * @Versions({"v1"})
     * @Request(contentType="application/x-www-form-urlencoded")
     * @Parameters({
     *      @Parameter("id", type="integer", required=true, description="(URI) User ID"),
     *      @Parameter("token", description="User authentication token", required=true),
     *      @Parameter("name", description="Name of user", required=true),
     *      @Parameter("email", description="Email", required=true),
     *      @Parameter("password", description="Password", required=true),
     *      @Parameter("password_confirmation", description="Password confirmation", required=true)
     * })
     * @Response(201, body={"data":{"id":1,"name":"Janis Kopstals","email":"jk@jk.jk","created_at":"2016-08-10 23:35:52","updated_at":"2016-08-10 23:35:52"}})
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UserRequest $request, $id)
    {
        $user =  User::findOrFail($id);
        $user->update($request->only($request->fillable()));
        return $this->response->item($user, new UserTransformer);
    }

    /**
     * Delete user
     * 
     * Remove the specified user from storage.
     * 
     * @Delete("/users/{id}")
     * @Versions({"v1"})
     * @Parameters({
     *      @Parameter("id", type="integer", required=true, description="(URI) User ID"),
     *      @Parameter("token", description="User authentication token", required=true),
     * })
     * @Response(200)
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user =  User::findOrFail($id);
        if($user->delete()) {
            return $this->response->noContent();
        } else {
            return $this->response->errorInternal('Unknown');
        }
    }
    
    public function upload(Request $request)
    {
        $file = 'Not found';
        if ($request->hasFile('file')) {
            if ($request->file('file')->isValid()) {
                $file = 'Is valid';
                //$file = $request->file('file'); //read file from form data
            } else {
                $file = 'Is not valid';
            }
        }
        
        //$file = file_get_contents('php://input'); //read file from RAW body
        return $file;
    }
}
