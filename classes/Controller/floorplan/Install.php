<?php defined('SYSPATH') OR die('No direct script access.');

class Controller_floorplan_Install extends Controller_Template
{
    public $template = 'template';
    
    public function before()
    {
        parent::before();
        
        $this->is_admin = Auth::instance()->logged_in('admin');
        View::bind_global('is_admin', $this->is_admin);
        
       /*  if (!$this->is_admin) {
            Session::instance()->set('message', 'Доступ запрещен');
            Session::instance()->set('message_type', 'danger');
            $this->redirect('floorplan');
        } */
    }

    public function action_index()
    {
        $model = Model::factory('Floorplan_Installm');
        $result = $model->checkDatabase();
        
        $content = View::factory('floorplan/database_check', array(
            'result' => $result,
            'is_admin' => $this->is_admin,
        ));
        
        $this->template->content = $content;
    }

    public function action_install()
    {
        $model = Model::factory('Floorplan_Installm');
        $result = $model->installDatabase();
        
        if ($result['success']) {
            Session::instance()->set('message', 'База данных успешно установлена');
            Session::instance()->set('message_type', 'success');
        } else {
            Session::instance()->set('message', 'Ошибка при установке базы данных');
            Session::instance()->set('message_type', 'danger');
        }
        
        $this->redirect('floorplan/install');
    }

    public function action_uninstall()
    {
        if ($this->request->method() != HTTP_Request::POST) {
            $this->redirect('floorplan/install');
        }
        
        $model = Model::factory('Floorplan_Installm');
        $result = $model->uninstallDatabase();
        
        if ($result['success']) {
            Session::instance()->set('message', 'База данных успешно удалена');
            Session::instance()->set('message_type', 'success');
        } else {
            Session::instance()->set('message', 'Ошибка при удалении базы данных');
            Session::instance()->set('message_type', 'danger');
        }
        
        $this->redirect('floorplan/install');
    }

    public function action_upgrade()
    {
        $model = Model::factory('Floorplan_Installm');
        $result = $model->upgradeDatabase();
        
        if ($result['success']) {
            Session::instance()->set('message', 'База данных успешно обновлена');
            Session::instance()->set('message_type', 'success');
        } else {
            Session::instance()->set('message', 'Ошибка при обновлении базы данных');
            Session::instance()->set('message_type', 'danger');
        }
        
        $this->redirect('floorplan/install');
    }
}
