<?php

Class HomeController extends BaseController
{

    public function welcome()
    {
        $this->data['title'] = 'Invoyz';
        $this->loadCss('landing-page.css');

        App::render('welcome.twig', $this->data);
    }
}