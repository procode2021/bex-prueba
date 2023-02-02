<?php


namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;
/**
* @OA\Info(title="API Prueba de BEX", version="1.0")
*
* @OA\Server(url="http://localhost:8000")
*/
class AuthController extends Controller
{

      /**
        * @OA\Post(
        * path="/api/v1/register",
        * operationId="Register",
        * tags={"Register"},
        * summary="User Register",
        * description="User Register here",
        *     @OA\RequestBody(
        *         @OA\JsonContent(),
        *         @OA\MediaType(
        *            mediaType="multipart/form-data",
        *            @OA\Schema(
        *               type="object",
        *               required={"name","email", "password"},
        *               @OA\Property(property="name", type="text"),
        *               @OA\Property(property="email", type="text"),
        *               @OA\Property(property="password", type="password")
        *            ),
        *        ),
        *    ),
        *      @OA\Response(
        *          response=200,
        *          description="Register Successfully",
        *          @OA\JsonContent()
        *       ),
        *      @OA\Response(response=400, description="Bad request"),
        * )
        */
  public function register(Request $request) {
        //validación de los datos
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed'
        ]);    
        //alta del usuario
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();
      
        return response($user, Response::HTTP_CREATED);
    }
      /** @OA\Post(
        * path="/api/v1/login",
        * operationId="Login",
        * tags={"Login"},
        * summary="User Login",
        * description="User Login here",
        *     @OA\RequestBody(
        *         @OA\JsonContent(),
        *         @OA\MediaType(
        *            mediaType="multipart/form-data",
        *            @OA\Schema(
        *               type="object",
        *               required={"email", "password"},
        *               @OA\Property(property="email", type="text"),
        *               @OA\Property(property="password", type="password")
        *            ),
        *        ),
        *    ),
       
        *      @OA\Response(
        *          response=200,
        *          description="userProfile OK",
        *          @OA\JsonContent()
        *       ),
        *      @OA\Response(response=400, description="Credenciales inválidas"),
        *      @OA\Response(response=404, description="Resource Not Found"),
        * )
        */
   
    public function login(Request $request) {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required']
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('token')->plainTextToken;
            $cookie = cookie('cookie_token', $token, 60 * 24);
            return response(["token"=>$token], Response::HTTP_OK)->withoutCookie($cookie);
        } else {
            return response(["message"=> "Credenciales inválidas"],Response::HTTP_UNAUTHORIZED);
        }        
    }
    /**
    * @OA\Get(
    *     path="/api/v1/user/me",
    *     summary="Mostrar usuarios",
    *     tags={"User"},
    *     description="Se coloca el token en header con el nombre de token.",
    *     security={{"apiAuth":{}}},
    *     operationId="user",
        * @OA\SecurityScheme(
        *    securityScheme="bearerAuth",
        *    in="header",
        *    name="bearerAuth",
        *    type="http",
        *    scheme="bearer",
        *    bearerFormat="JWT",
        * ),
    *     @OA\Response(
    *         response=200,
    *         description="Mostrar todos los usuarios."
    *     ),
    *     @OA\Response(
    *         response="default",
    *         description="Ha ocurrido un error."
    *     )
    * )
    */
    public function userProfile(Request $request) {
        return response()->json([
            "message" => "userProfile OK",
            "userData" => auth()->user()
        ], Response::HTTP_OK);
    }


    /**
     * @OA\Put(
     *     path="/api/v1/update{id}",
     *     tags={"Update"},
     *     summary="User Update",
     *     operationId="Update",
     *     @OA\Parameter(name="id", description="id, eg; 1", required=true, in="path", @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="User update"),
     *     @OA\Response(response=400, description="Bad request"),
     *     @OA\Response(response=404, description="Resource Not Found"),
     * )
     */
    public function userUpdate(Request $request, $id){
        $user = User::find($id);
        $user->password =$request->password;
        $user->email = $request->email;
        $user->name = $request->name;
        $user->save();
        return response($user, Response::HTTP_CREATED);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/remove{id}",
     *     tags={"Delete"},
     *     summary="User delete",
     *     operationId="Delete",
     *     @OA\Parameter(name="id", description="id, eg; 1", required=true, in="path", @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="User Delete"),
     *     @OA\Response(response=400, description="Bad request"),
     *     @OA\Response(response=404, description="Resource Not Found"),
     * )
     */
    public function userRemove(Request $request, $id){
        $user = User::destroy($id);
        return response($user, Response::HTTP_CREATED);
    }
    
    public function logout() {
        $cookie = Cookie::forget('cookie_token');
        return response(["message"=>"Cierre de sesión OK"], Response::HTTP_OK)->withCookie($cookie);
    }


   
    /**
    * @OA\Get(
    *     path="/api/v1/users",
    *     summary="Mostrar usuarios",
    *     tags={"User List"},
    *     operationId="Users",
    *     @OA\Response(
    *         response=200,
    *         description="Mostrar todos los usuarios."
    *     ),
    *     @OA\Response(
    *         response="default",
    *         description="Ha ocurrido un error."
    *     )
    * )
    */

    public function allUsers() {
       $users = User::all();
       return response()->json([
        "users" => $users
       ]);
    }
}