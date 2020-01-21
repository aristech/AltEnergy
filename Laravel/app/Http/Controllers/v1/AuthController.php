<?php

namespace App\Http\Controllers\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\User;
use App\Role;
use App\UsersRoles;
use Validator;
use App\Manager;
use App\Http\Resources\UserResource;


class AuthController extends Controller
{
    /**
     * Create user
     *
     * @param  [string] name
     * @param  [string] email
     * @param  [string] password
     * @param  [string] password_confirmation
     * @return [string] message
     */
    public function signup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lastname' => 'required|string',
            'firstname' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|confirmed'
        ]);

        if ($validator->fails()) {
            $failedRules = $validator->errors()->first(); //todo for future: na allaksw
            return response()->json(["message" => $failedRules], 422);
        }

        $user = new User([
            'lastname' => $request->lastname,
            'firstname' => $request->firstname,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'active' => true
        ]);

        $user->save();

        UsersRoles::create(['role_id' => 5, 'user_id' => $user->id]);
        return response()->json(['message' => 'Ο χρήστης ' . $request->lastname . " " . $request->firstname . " καταχωρήθηκε επιτυχώς! Στοιχεία εισόδου χρήστη: Email: " . $user->email . " Password: " . $request->password], 201);
    }

    public function createUser(Request $request)
    {
        // $user_role = $request->user()->role()->first()->id;
        // if ($user_role < 4) {
        //     return response()->json(["message" => "Ο χρήστης με ρόλο " . $request->user()->role()->first()->title . " δεν μπορεί να πραγματοποιήσει την ενέργεια αυτή"], 401);
        // }
        //

        $validator = Validator::make($request->all(), [
            'lastname' => 'required|string',
            'firstname' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|confirmed',
            'role_id' => 'required|integer',
            'active' => 'nullable|boolean',
            'manager_id' => 'nullable|integer',
            'client_id' => 'nullable|integer',
            'telephone' => 'nullable|string',
            'telephone2' => 'nullable|string',
            'mobile' => 'nullable|string',
            'active' => 'required|boolean'
        ]);

        if ($validator->fails()) {
            $failedRules = $validator->errors()->first(); //todo for future: na allaksw
            return response()->json(["message" => $failedRules], 422);
        }

        $role = Role::where('id', $request->role_id)->first();
        if (!$role) {
            return response()->json(['message' => 'Ο ρόλος χρήστη δεν είναι διαθέσιμος'], 404);
        }

        if ($request->telephone == null && $request->telephone2 == null && $request->mobile == null) {
            return response()->json(["message" => "τουλάχιστον ένα τηλέφωνο είναι υποχρεώτικο!"], 422);
        }

        if ($request->role_id == 2) {
            $manager = Manager::where('id', $request->manager_id)->first();
            if (!$manager) {
                return response()->json(["message" => "Παρακαλώ ορίστε αντιστοιχεία λογαριασμού με διαχειριστή!"], 422);
            }
        }

        if ($request->role_id == 2) {
            $user = new User([
                'lastname' => $request->lastname,
                'firstname' => $request->firstname,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'active' => $request->active,
                'telephone' => $request->telephone,
                'telephone2' => $request->telephone2,
                'mobile' => $request->mobile,
                'manager_id' => $request->manager_id
            ]);
        } else {
            $user = new User([
                'lastname' => $request->lastname,
                'firstname' => $request->firstname,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'active' => $request->active,
                'telephone' => $request->telephone,
                'telephone2' => $request->telephone2,
                'mobile' => $request->mobile
            ]);
        }

        $user->save();

        UsersRoles::create(['role_id' => $request->role_id, 'user_id' => $user->id]);
        $role_name = Role::where("id", $request->role_id)->first();

        return response()->json(['message' => 'Ο χρήστης ' . $request->lastname . " " . $request->firstname . " καταχωρήθηκε επιτυχώς με ρόλο " . $role_name->title . "! Στοιχεία εισόδου χρήστη: Email: " . $user->email . " Password: " . $request->password], 201);
    }

    /**
     * Login user and create token
     *
     * @param  [string] email
     * @param  [string] password
     * @param  [boolean] remember_me
     * @return [string] access_token
     * @return [string] token_type
     * @return [string] expires_at
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
            'remember_me' => 'boolean'
        ]);

        if ($validator->fails()) {
            $failedRules = $validator->errors()->first(); //todo for future: na allaksw
            return response()->json(["message" => $failedRules], 422);
        }


        $credentials = request(['email', 'password']);

        $user = User::where('email', $credentials['email'])->first();

        if (!Auth::attempt($credentials) || $user->active == false)
            return response()->json(['message' => 'Λάθος όνομα ή κωδικός!'], 401);
        $user = $request->user();
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;
        if ($request->remember_me)
            $token->expires_at = Carbon::now()->addWeeks(1);
        $token->save();
        return response()->json([
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse(
                $tokenResult->token->expires_at
            )->toDateTimeString(),
            'user' => UserResource::make($user)
        ]);
    }

    /**
     * Logout user (Revoke the token)
     *
     * @return [string] message
     */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json([
            'message' => 'Ο χρήστης αποσυνδέθηκε επιτυχώς!'
        ], 200);
    }

    /**
     * Get the authenticated User
     *
     * @return [json] user object
     */
    public function user(Request $request)
    {
        return response()->json(UserResource::make($request->user()));
    }

    public function allUsers(Request $request)
    {
        // $user_role = $request->user()->role()->first()->id;
        // if ($user_role < 4) {
        //     return response()->json(["message" => "Ο χρήστης με ρόλο " . $request->user()->role()->first()->title . " δεν μπορεί να πραγματοποιήσει την ενέργεια αυτή"], 401);
        // }

        return UserResource::collection(User::all());
    }

    public function editUser(Request $request)
    {
        //return response()->json(["message" => "Στην Demo έκδοση δεν μπορέιτε να κάνετε edit τα στοιχεία των χρηστών!"], 401);
        //#ΤODO -> Change to production

        // $user_role = $request->user()->role()->first()->id;
        // if ($user_role < 4) {
        //     return response()->json(["message" => "Ο χρήστης με ρόλο " . $request->user()->role()->first()->title . " δεν μπορεί να πραγματοποιήσει την ενέργεια αυτή"], 401);
        // }
        //

        $user = User::find($request->id);
        if (!$user) {
            return response()->json(["message" => "Δεν υπάρχει ο χρήστης για να αλλάξουν τα στοιχεία του"], 404);
        }


        $validator = Validator::make($request->all(), [
            'lastname' => 'required|string',
            'firstname' => 'required|string',
            'email' => 'required|string|email|',
            'password' => 'nullable|string',
            'password_confirmation' => 'nullable|string',
            'role_id' => 'required|integer',
            'active' => 'nullable|boolean',
            'manager_id' => 'nullable|integer',
            'client_id' => 'nullable|integer',
            'telephone' => 'nullable|string',
            'telephone2' => 'nullable|string',
            'mobile' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            $failedRules = $validator->errors()->first(); //todo for future: na allaksw
            return response()->json(["message" => $failedRules], 422);
        }


        $mail_exists = User::where('email', $request->id)->where('id', "!=", $request->id)->first();

        if ($mail_exists) {
            return response()->json(["message" => "To email " . $request->email . " είναι σε χρήση από αλλόν χρήστη"], 422);
        }

        // $role = Role::where('id', $request->role_id)->first();
        // if (!$role) {
        //     return response()->json(['message' => 'Ο ρόλος χρήστη δεν είναι διαθέσιμος'], 404);
        // }
        foreach ($request->roles as $role) {
            $checkRole = Role::where('id', $role['id'])->first();
            if (!$checkRole) {
                return response()->json(["message" => "Ενας ή παραπάνω ρολοι δεν είναι καταχωρημένοι στο σύστημα"], 404);
            }
        }

        if ($request->telephone == null && $request->telephone2 == null && $request->mobile == null) {
            return response()->json(["message" => "τουλάχιστον ένα τηλέφωνο είναι υποχρεώτικο!"], 422);
        }

        if (!$request->password && !$request->pasword_confirmation) {
            $user->update([
                "lastname" => $request->lastname,
                "firstname" => $request->firstname,
                "email" => $request->email,
                "active" => $request->active,
                'telephone' => $request->telephone,
                'telephone2' => $request->telephone2,
                'mobile' => $request->mobile
            ]);

            UsersRoles::where('user_id', $request->id)->where('role_id', '!=', 5)->delete();
            foreach ($request->roles as $role) {
                if ($role['checked'] == true) {
                    UsersRoles::create(["user_id" => $request->id, "role_id" => $role['id']]);
                }
            }
            // UsersRoles::where('user_id', $request->id)->first()->update(["role_id" => $request->role_id]);
            // $role_name = Role::where("id", $request->role_id)->first();
            return response()->json(["message" => "Τα στοιχεία του χρήστη με κωδικό " . $request->id . " άλλαξαν επιτυχώς!", UserResource::make($user)], 200);
        }

        if ($request->password != $request->password_confirmation) {
            return response()->json(["message" => "Ο κωδικός και η επαλήθευση του δεν ταιριάζουν!"], 200);
        }

        // UsersRoles::where('user_id', $request->id)->first()->update(["role_id" => $request->role_id]);
        // $role_name = Role::where("id", $request->role_id)->first();
        UsersRoles::where('user_id', $request->id)->where('role_id', '!=', 5)->delete();
        foreach ($request->roles as $role) {
            if ($role['checked'] == true) {
                UsersRoles::create(["user_id" => $request->id, "role_id" => $role['id']]);
            }
        }

        $user->update([
            "lastname" => $request->lastname,
            "firstname" => $request->firstname,
            "email" => $request->email,
            "password" => bcrypt($request->password),
            "active" => $request->active,
            'telephone' => $request->telephone,
            'telephone2' => $request->telephone2,
            'mobile' => $request->mobile
        ]);

        return response()->json(['message' => 'Τα στοιχεία του χρήστη με κωδικό ' . $request->id . ' ενημερώθηκαν επιτυχώς. Ο χρήστης ' . $request->lastname . " " . $request->firstname . " καταχωρήθηκε επιτυχώς με ρόλο " . $role_name->role_title . "!Νέα στοιχεία εισόδου χρήστη: Email: " . $user->email . " Password: " . $request->password, UserResource::make($user)], 201, ['charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
        //return response()->json(["message" => "Τα στοιχεία του χρήστη άλλαξαν επιτυχώς!", UserResource::make($user)],200);
    }

    public function deleteUser(Request $request)
    {
        // $user_role = $request->user()->role()->first()->id;
        // if ($user_role < 4) {
        //     return response()->json(["message" => "Ο χρήστης με ρόλο " . $request->user()->role()->first()->title . " δεν μπορεί να πραγματοποιήσει την ενέργεια αυτή"], 401);
        // }
        //

        $user = User::find($request->id);
        if (!$user) {
            return response()->json(["message" => "Δεν υπάρχει ο χρήστης για να αλλάξουν τα στοιχεία του"], 404);
        }

        $user->delete();

        return response()->json(["message" => "Ο χρήστης με κωδικό " . $request->id . " ολοκληρώθηκε επιτυχώς"], 200);
    }
}
