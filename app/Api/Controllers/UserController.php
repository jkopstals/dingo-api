<?php

namespace Api\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;

use App\Models\User;
use Api\Transformers\UserTransformer;
use Api\Requests\UserRequest;

use JWTAuth;
use Log;
use Excel;
use Validator;
//use PHPExcel_Settings; //not needed, sinch ZipArchive was installed

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

    /**
     * Upload new users with excel file
     *
     *
     * @Post("/users/upload")
     * @Versions({"v1"})
     * @Request(contentType="raw")
     * @Parameters({
     *      @Parameter("file", type="file", required=true, description="File sent as payload"),

     * })
     * @Response(200, body={"data": {"success": true,"progress":  {"File uploaded to server","File content found","2 rows valid for import","1 rows imported","1 rows NOT imported"},"rows":  {{"user": {"name": "Miss Lavina D'Amore Jr.","email": "wallace.prosacco@example.com","password": "password"},"success": false,"errors": {"email":  {"The email has already been taken."}}},{"user": {"name": "Mr. Victor Stracke","email": "zackery.wuckert@example.com","password": "password"},"success": true, "errors":  {}}}}})
     *
     */

    // DEV NOTE: PHPExcel needs to have ZipArchive installed
    // e.g. on ubuntu: sudo apt-get install php7.0-zip
    public function upload(Request $request)
    {
        Log::info('Upload called');
        $data = ['success' => false, 'progress' => [], 'rows' => []];
        
        $file = file_get_contents('php://input'); //read file from RAW body
        $temp_name = tempnam(sys_get_temp_dir(), 'dingoApi_');
        file_put_contents($temp_name,$file);
        Log::info('temp_file: '.$temp_name);
        if($temp_name) {
            $data['progress'][] = 'File uploaded to server';
        }
        $results = [];
        //PHPExcel_Settings::setZipClass(PHPExcel_Settings::PCLZIP);
        Excel::load($temp_name, function($reader) use (&$data) {
            $rows_imported = 0;
            $rows_failed = 0;
            $results = $reader->select(array('name', 'email', 'password'))->all()->toArray();
            if(count($results)) {
                $data['progress'][] = 'File content found';
            } else {
                $data['progress'][] = 'File content not found';
            }
            $full_list = [];
            foreach($results as $r) {
                if(array_key_exists('name',$r) && array_key_exists('email',$r) && array_key_exists('password',$r)) {
                    $full_list[] = $r;
                } else { 
                    // check if file has sheets, then import from all sheets
                    foreach($r as $x) {
                        if(array_key_exists('name',$x) && array_key_exists('email',$x) && array_key_exists('password',$x)) {
                            $full_list[] = $x;
                        }
                    }
                }
            }
            if(count($full_list)) {
                $data['progress'][] = count($full_list) .' rows valid for import';
            } else {
                $data['progress'][] = 'No rows valid for import!';
            }
            $validation_rules = User::getRules();
            unset($validation_rules['password_confirmation']);

            foreach($full_list as $user) {
                $validator = Validator::make($user, $validation_rules);
                if($validator->fails()) {
                    $data['rows'][] = ['user' => $user, 'success' => false, 'errors' => $validator->errors()];
                    $rows_failed++;
                } else {
                    $new_user = User::create($user);
                    if($new_user) {
                        $data['success'] = true;
                        $rows_imported++;
                        $data['rows'][] = ['user' => $user, 'success' => true, 'errors' => []];
                    } else {
                        $data['rows'][] = ['user' => $user, 'success' => false, 'errors' => ['unknown' => 'Unknown error']];
                        $rows_failed++;
                    }
                }
            }
            $data['progress'][] = $rows_imported. ' rows imported';
            $data['progress'][] = $rows_failed. ' rows NOT imported';
            
        });
        return $this->response->array(['data' => $data]);
    }
}
