<?php

class HomeController extends Controller {

    public function index() {
        // Página inicial
        $this->view('home/index');
    }
}
