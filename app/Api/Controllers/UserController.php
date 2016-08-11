<?php

namespace Api\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use API;

use App\Models\User;
use Api\Transformers\UserTransformer;

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
     * @Request("email=X&password=Y", contentType="application/x-www-form-urlencoded")
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
     * @Response(200, body={"data":{"id":1,"name":"Janis Kopstals","email":"jk@jk.jk","created_at":"2016-08-10 23:35:52","updated_at":"2016-08-10 23:35:52"}})
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
     *      @Parameter("page", description="The page of results to view.", default=1),
     *      @Parameter("limit", description="The amount of results per page.", default=10)
     * })
     * @Response(200, body={"data":{{"id":1,"name":"Janis Kopstals","email":"jk@jk.jk","created_at":"2016-08-10 23:35:52","updated_at":"2016-08-10 23:35:52"}},"meta":{"pagination":{"total":51,"count":10,"per_page":10,"current_page":1,"total_pages":6,"links":{"next":"http:\/\/localhost:8000\/api\/users?page=2"}}}})
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
     * @Response(200,body={"data":{"name":"text|required|min:2","email":"email|required|unique","password":"text|min:8"}})
     *
     * @return \Illuminate\Http\Response
     */
    public function rules()
    {
        return $this->response->array(['data' => User::getRules()]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @Post("/users")
     * @Versions({"v1"})
     * @Request(contentType="application/x-www-form-urlencoded")
     * @Parameters({
     *      @Parameter("name", description="Name of user", required=true),
     *      @Parameter("email", description="Email", required=true),
     *      @Parameter("password", description="Password", required=true)
     * })
     * @Response(201, body={"data":{"id":1,"name":"Janis Kopstals","email":"jk@jk.jk","created_at":"2016-08-10 23:35:52","updated_at":"2016-08-10 23:35:52"}})
     *
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserRequest $request)
    {
        return $this->response->item(User::create($request->only($request->acceptableParams())), new UserTransformer)->setStatusCode(201);
    }

    /**
     * Show specific user (by id)
     *
     * Get a JSON representation of current user (jwt auth)
     *
     * @Get("/users/{id}")
     * @Versions({"v1"})
     * @Parameters({
     *       @Parameter("id", type="integer", required=true, description="User ID")
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
     * @Post("/auth")
     * @Versions({"v1"})
     * @Request("email=X&password=Y", contentType="application/x-www-form-urlencoded")
     * @Response(200, body={"token": "NEW_TOKEN"})
     *
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
