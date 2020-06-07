<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator,
    Redirect,
    Response;
Use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Session;
use App\CentralSetting;

class AuthController extends Controller {

    public function index() {
        return view('login.login');
    }

    public function registration() {
        return view('login.registration');
    }

    /**
     * Login
     * @param Request $request
     * @return view login
     */
    public function postLogin(Request $request) {

        request()->validate([
            'email' => 'required',
            'password' => 'required|min:2',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {

            $comment = "Please, define the signature to generate report.";
            $settingsPdfReport = false;

            if (Auth::user()->getSettings(CentralSetting::IN_PROCESS)) {
                $comment = "Please, re - define the signature to generate report";
                $settingsPdfReport = false;
            }
            if (Auth::user()->getSettings(CentralSetting::E_SIGNATURE)) {
                $settingsPdfReport = Auth::user()->getSettings(CentralSetting::E_SIGNATURE);
                $comment = "";
            }
            if (Auth::user()->getSettings(CentralSetting::RE_E_SIGNATURE)) {
                $settingsPdfReport = Auth::user()->getSettings(CentralSetting::RE_E_SIGNATURE);
                $comment = CentralSetting::CONTACT;
            }

            $signatute_status = DB::Table('users')->select('signature')->where('id', Auth::user()->id)->first();


            if ($signatute_status->signature) {

                $controller = new ReportController;
                $signature = $controller->getLastSignatureArray(Auth::user()->id);

                $signature = $signature[0];
            } else {
                $signature = 0;
            }

            return view('login.dashboard', [
                'signature' => $signature,
                'settingsPdfReport' => $settingsPdfReport,
                'comment' => $comment,
            ]);
        }

        return Redirect::to("login")->withSuccess('Oppes! You have entered invalid credentials');
    }

    /**
     * Registration
     * @param Request $request
     * @return view dashboard
     */
    public function postRegistration(Request $request) {
        request()->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);


        $data = $request->all();

        $this->create($data);

        return Redirect::to("dashboard")->withSuccess('Great! You have Successfully loggedin');
    }

    /**
     * @return view login/dashboard
     */
    public function dashboard() {

        if (Auth::check()) {
            return view('login.dashboard', [
            ]);
        }
        return Redirect::to("login")->withSuccess('Successful registration');
    }

    /**
     * Create User
     * @param array $data
     * @return User
     */
    public function create(array $data) {
        return User::create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => Hash::make($data['password'])
        ]);
    }

    /**
     * Logout
     * @return login view
     */
    public function logout() {
        Session::flush();
        Auth::logout();
        return Redirect('login');
    }

}
