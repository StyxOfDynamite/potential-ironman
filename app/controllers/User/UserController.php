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
     * display the signup page
     */
    public function doSignup()
    {
        $email = Input::post('email');
        $password = Input::post('password');
        $firstName = Input::post('firstName');
        $lastName = Input::post('lastName');

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

    }

}