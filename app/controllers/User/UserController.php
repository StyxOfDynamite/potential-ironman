<?php

namespace User;

use \App;
use \View;
use \Input;
use \Sentry;
use \Response;

class UserController extends BaseController
{

    /**
     * display the user dashboard
     */
    public function index()
    {
        View::display('user/index.twig', $this->data);
    }

    /**
     * display the signup form
     */
    public function signup()
    {
        View::display('user/signup.twig', $this->data);
    }

    /**
     * display the user homepage
     */
    public function home()
    {
        View::display('user/home.twig', $this->data);
    }

    /**
     * display the login form
     */
    public function login()
    {
        if(Sentry::check()){
            Response::redirect($this->siteUrl('home'));
        }else{
            $this->data['redirect'] = (Input::get('redirect')) ? base64_decode(Input::get('redirect')) : '';
            View::display('admin/login.twig', $this->data);
        }
    }

    /**
     * Process the login
     */
    public function doLogin()
    {
        $remember = Input::post('remember', false);
        $email    = Input::post('email');
        $redirect = Input::post('redirect');
        $redirect = ($redirect) ? $redirect : 'home';

        try{
            $credential = array(
                'email'     => $email,
                'password'  => Input::post('password')
            );

            // Try to authenticate the user
            $user = Sentry::authenticate($credential, false);

            if($remember){
                Sentry::loginAndRemember($user);
            }else{
                Sentry::login($user, false);
            }

            Response::redirect($this->siteUrl($redirect));
        }catch(\Exception $e){
            App::flash('message', $e->getMessage());
            App::flash('email', $email);
            App::flash('redirect', $redirect);
            App::flash('remember', $remember);

            Response::redirect($this->siteUrl('login'));
        }
    }

    /**
     * Logout the user
     */
    public function logout()
    {
        Sentry::logout();

        Response::redirect($this->siteUrl('login'));
    }

    /**
     * display the signup page
     */
    public function doSignup()
    {
        $email = Input::post('email');
        $password = Input::post('password');
        $firstName = Input::post('firstName');
        $lastName = Input::post('lastName');
        $redirect = Input::post('redirect');
        $redirect = ($redirect) ? $redirect : 'home';

        try{
            Sentry::createUser(array(
                'email'       => $email,
                'password'    => $password,
                'first_name'  => $firstName,
                'last_name'   => $lastName,
                'activated'   => 1,
                'permissions' => array(
                    'admin'     => 0
                )
            ));
        }catch(\Exception $e){
            App::flash('message', $e->getMessage());
            App::flash('email', $email);

            Response::redirect($this->siteUrl('signup'));
        }

        Response::redirect($this->siteUrl($redirect));

    }

}