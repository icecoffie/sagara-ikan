<?php
if (!defined('SAGARA_APP')) { http_response_code(403); exit('Forbidden'); }

class DashboardController extends Controller
{
    public function index(): void
    {
        require_login();
        $this->view('dashboard/index', [], 'Dashboard');
    }
}
