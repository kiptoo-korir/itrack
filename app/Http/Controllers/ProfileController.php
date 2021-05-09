<?php

namespace App\Http\Controllers;

use App\Models\AccessToken;
use App\Models\Platform;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class ProfileController extends Controller
{
    public function profile()
    {
        $data['user_data'] = Auth::user();
        $data['user_data']->first_letter = substr($data['user_data']->name, 0, 1);
        $data['platforms'] = Platform::all();
        $client = env('GITHUB_CLIENT_ID');
        $data['request'] = "https://github.com/login/oauth/authorize?client_id={$client}&scope=repo%20notifications%20user";

        return view('profile')->with($data);
    }

    public function profile_photo(Request $request)
    {
        $request->validate([
            'profile_photo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:512',
        ]);

        $random = Str::random(6);
        $imageName = $random.time().'.'.$request->profile_photo->extension();
        $user = Auth::user();

        if ($user->photo) {
            unlink(storage_path('app/public/images/'.$user->photo));
        }

        $request->profile_photo->StoreAs('public/images', $imageName);
        $user->photo = $imageName;
        if ($user->save()) {
            return response()
                ->json(['success' => 'Profile image uploaded successfully.', 'img' => url('storage/images/'.$user->photo)], 200)
            ;
        }
    }

    public function change_password(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
            'new_password' => 'required|confirmed|min:6|max:18|string',
        ]);

        $user = Auth::user();
        if (Hash::check($request->password, $user->password)) {
            $user->password = bcrypt($request->new_password);
            if ($user->save()) {
                return response()->json(['success' => 'Your new password has been reset successfully.'], 200);
            }
        } else {
            return response()->json(['error' => 'Please ensure that you have provided the correct current password.'], 400);
        }
    }

    public function add_token(Request $request)
    {
        $request->validate([
            'platform' => 'required|integer',
            'access_token' => 'required|string',
        ]);

        $user_id = Auth::id();
        $token_record = [
            'owner' => $user_id,
            'platform' => $request->platform,
            'access_token' => Crypt::encrypt($request->access_token),
        ];

        if ($new = AccessToken::create($token_record)) {
            AccessToken::where(['platform' => $request->platform, 'owner' => $user_id])
                ->whereNotIn('id', [$new->id])
                ->delete()
            ;

            return response()->json(['success' => 'Access token has been added successfully.'], 200);
        }

        return response()->json(['error' => 'An error seems to have occurred. Please try to add your token again.'], 400);
    }

    public function recent_activity()
    {
        $user_id = Auth::id();
        $activities = DB::table('activity_log')
            ->select('log_name', 'created_at')
            ->where(['causer_type' => 'App\Models\User', 'causer_id' => $user_id])
            ->orderByDesc('created_at')
            ->get()
        ;

        return DataTables::of($activities)
            ->addColumn('created_at', function ($data) {
                return date('D, d M Y g:i A', strtotime($data->created_at));
            })
            ->addColumn('log_name', function ($data) {
                return strtoupper($data->log_name);
            })
            ->addColumn('order_date', function ($data) {
                return strtotime($data->created_at);
            })
            ->rawColumns(['created_at', 'log_name', 'order_date'])
            ->make(true)
        ;
    }

    public function tokens_list()
    {
        $user_id = Auth::id();
        $tokens = AccessToken::withTrashed()
            ->leftJoin('platforms as p', 'access_tokens.platform', '=', 'p.id')
            ->select(['access_tokens.created_at', 'access_tokens.id', 'p.name', 'access_tokens.deleted_at'])
            ->where('owner', $user_id)->orderByDesc('access_tokens.created_at')->get();

        return DataTables::of($tokens)
            ->addColumn('created_at', function ($data) {
                return date('D, d M Y g:i A', strtotime($data->created_at));
            })
            ->addColumn('action', function ($data) {
                return '<button type = "button" name = "remove" data-toggle = "modal" data-target = "#removeModalChildren" data-id = "'.$data->id.'" class="delete btn btn-danger btn-sm" >Delete</button > ';
            })
            ->addColumn('order_date', function ($data) {
                return strtotime($data->created_at);
            })
            ->rawColumns(['action', 'created_at', 'order_date'])
            ->make(true)
        ;
    }

    public function callback(Request $request)
    {
        $code = $_GET['code'];
        $response = Http::withHeaders([
            'Accept' => 'application/json',
        ])->post('https://github.com/login/oauth/access_token', [
            'client_id' => env('GITHUB_CLIENT_ID'),
            'client_secret' => env('GITHUB_SECRET'),
            'code' => $code,
        ]);

        $body = json_decode($response->body());
        $access_token = $body->access_token;
        $token_type = $body->token_type;
        $scope = $body->scope;

        $user_id = Auth::id();
        $token_record = [
            'owner' => $user_id,
            'platform' => 1,
            'access_token' => Crypt::encrypt($access_token),
            'scope' => $scope,
            'type' => $token_type,
            'verified' => true,
        ];

        if ($new = AccessToken::create($token_record)) {
            AccessToken::where(['platform' => 1, 'owner' => $user_id])
                ->whereNotIn('id', [$new->id])
                ->delete()
            ;

            return redirect()->route('profile');
        }
    }

    public function remove_token(Request $request)
    {
        $request->validate([
            'token_id' => 'required|uuid',
        ]);

        $token_id = $request->token_id;

        if (AccessToken::where('id', $token_id)->delete()) {
            return response()->json(['success' => 'Access token has been removed successfully.'], 200);
        }

        return response()->json(['error' => 'An error seems to have occurred. Please try to remove this token again.'], 400);
    }

    // public function storeSecret(Request $request)
    // {
    //     $request->user()->fill([
    //         'token' => Crypt::encryptString($request->token),
    //     ])->save();
    // }
    // try {
    //     $decrypted = Crypt::decryptString($encryptedValue);
    // } catch (DecryptException $e) {
    //     //
    // }
}
