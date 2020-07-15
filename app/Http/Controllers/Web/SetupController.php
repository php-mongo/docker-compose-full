<?php

namespace App\Http\Controllers\Web;

/**
 * Base Controllers
 */
use App\Http\Controllers\Controller;

/**
 * Requests
 */
use Illuminate\Http\Request;
use App\Http\Requests\StoreControlUser;

/**
 * Models
 */
use App\Models\User;

/**
 * Facades
 */
//use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

/**
 * Class SetupController
 * @package App\Http\Controllers\Web
 */
class SetupController extends Controller
{
    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/#/admin';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Loads the initial setup laylout
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getSetup()
    {
        return view('public.setup');
    }

    /**
     * Saves the Control User
     *
     * @param StoreControlUser $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function saveSetup(StoreControlUser $request)
    {
        $data = $request->validated();

        // new user
        $user               = new User();
        $user->name         = $data['name'];
        $user->user         = $data['user'];
        $user->email        = $data['email'];
        $user->password     = Hash::make($data['password']);
        $user->control_user = 1;
        $user->admin_user   = 1;
        $user->active       = 1;

        $user->save();

        return redirect('/admin');
    }
}
