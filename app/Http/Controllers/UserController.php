<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['index', 'show', 'votes', 'search']]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show($username)
    {
        $user = User::where('username', $username)
            ->with(['myFollow'])
            ->withCount(['following', 'followers', 'votes', 'likes', 'dislikes'])
            ->firstOrFail();

        return response()->json($user);
    }

    public function search(Request $request)
    {
        $query = $request->get('query');
        $limit = $request->get('limit');

        if (!$query || $query === '') {
            return response()->json(['message' => 'You havent provided a search query'], 400);
        }

        $result = User::where('username', 'LIKE', '%' . $query . '%')
            ->orWhere('name', 'LIKE', '%' . $query . '%')
            ->withCount(['followers'])
            ->take($limit)
            ->get();

        return response()->json($result);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {

        if ($user->id === Auth::user()->id) {
            $request->validate([
                'name' => 'string',
                'biography' => 'string|max:150',
                'username' => 'string|min:5|max:32|unique:users',
                'email' => 'string|email|unique:users',
            ]);

            foreach ($request->all() as $key => $value) {
                if (Schema::hasColumn($user->getTable(), $key)) {
                    $user[$key] = $value;
                }
            }

            $user->save();

            return response()->json($user);
        }

        return response()->json(['message' => 'Unauthorized'], 401);
    }

    public function changePassword(Request $request, User $user)
    {
        if ($user->id === Auth::id()) {
            $request->validate([
                'current_password' => 'required|',
                'new_password' => 'required|confirmed',
            ]);

            if (Hash::check($request->current_password, $user->password)) {
                $user->password = $request->new_password;
                $user->save();
                return response()->json(['message' => 'Successfully changed password']);
            }
            return response()->json(['message' => 'Incorrect password'], 400);
        }

        return response()->json(['message' => 'Unauthorized'], 401);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, User $user)
    {
        $forceDelete = $request->input('force_delete', false);

        if ($user->id === Auth::user()->id) {
            if ($forceDelete === true) {
                $user->forceDelete();
                return response()->json(['message' => 'Successfully deleted user', 'force_delete' => true]);
            } else {
                $user->delete();
                return response()->json(['message' => 'Successfully deleted user', 'force_delete' => false]);
            }
        }

        return response()->json(['message' => 'Unauthorized'], 401);
    }

    public function updateAvatar(Request $request, User $user)
    {
        $authUser = Auth::user();
        if ($user->id === $authUser->id) {
            $path = Storage::putFile('public/avatars', $request->file('avatar'));
            $user->avatar = Storage::url($path);
            $user->save();
            return response()->json($user);
        }
        return response()->json(['message' => 'Unauthorized', 'debug' => [$user, $authUser],
        ], 401);
    }

    public function removeAvatar(Request $request, User $user)
    {
        if (isset($user)) {
            if ($user->id === Auth::user()->id) {
                $user->avatar = null;
                $user->save();
                return response()->json($user);
            }
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return response()->json(['message' => 'User not found'], 404);
    }

    public function notifications(User $user)
    {
        if ($user->id === Auth::id()) {
            return response()->json(Auth::user()->notifications);
        }
        return response()->json(['message' => 'Unauthorized'], 401);
    }
}