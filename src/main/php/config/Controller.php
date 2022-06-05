<?php
namespace config;


/**
 * Class responsible for opening views.
 */
abstract class Controller
{
    //-------------------------------------------------------------------------
    //        Attributes
    //-------------------------------------------------------------------------
    private const VIEWS_PATH = "src/main/php/views";


    //-------------------------------------------------------------------------
    //        Methods
    //-------------------------------------------------------------------------
    /**
     * Main method of the controller. It will be responsible for calling a view.
     */
    public abstract function index();
    
    /**
     * Shows a view.
     *
     * @param       string view_name View name
     * @param       array view_data [optional] View's parameters
     */
    public function load_view($view_name, $view_data = array())
    {
        extract($view_data);
        
        require Controller::VIEWS_PATH."/".$view_name.".php";
    }

    /**
     * Shows a view inside a template.
     *
     * @param       string view_name View name
     * @param       array view_data [optional] View's parameters
     * @param       bool $logged [optional] True if user is logged; false 
     * otherwise
     */
    public function load_template($view_name, $view_data = array(), $logged = true)
    {
        extract($view_data);
        
        if ($logged) {
            require Controller::VIEWS_PATH."/"."template/html_logged.php";
        }
        else {
            require Controller::VIEWS_PATH."/"."template/html_no_logged.php";
        }
    }
}
