<?php

namespace Admin;

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
        View::display('user/index.twig', $this->data);
    }

    /**
     * display the signup page
     */
    public function doSignup()
    {
        try{
            Sentry::createUser(array(
                'email'       => 'admin@admin.com',
                'password'    => 'password',
                'first_name'  => 'Website',
                'last_name'   => 'Administrator',
                'activated'   => 1,
                'permissions' => array(
                    'admin'     => 1
                )
            ));
        }catch(\Exception $e){
            App::flash('message', $e->getMessage());
        }

    }

}