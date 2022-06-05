<?php
namespace controllers;


use config\Controller;
use repositories\pdo\MySqlPDODatabase;
use domain\Student;
use dao\NotificationsDAO;


/**
 * It will be responsible for site's page not found behavior.
 * 
 * @author		William Niemiec &lt; williamniemiec@hotmail.com &gt;
 * @version		1.0.0
 * @since		1.0.0
 */
class NotFoundController extends Controller 
{
    //-------------------------------------------------------------------------
    //        Methods
    //-------------------------------------------------------------------------
	/**
	 * @Override
	 */
	public function index()
	{
	    $dbConnection = new MySqlPDODatabase();
	    
	    $header = array(
	        'title' => 'Page not found - Learning platform',
            'description' => "Page not found",
	        'robots' => 'noindex'
	    );
	    
	    $viewArgs = array(
	        'header' => $header
	    );
	    
	    $student = Student::getLoggedIn($dbConnection);
	    
	    if (empty($student)) {
	        $this->load_template('error/404', $viewArgs, false);
	    }
        else {
            $notificationsDAO = new NotificationsDAO($dbConnection, $student->getId());
    	        
            $viewArgs['username'] = $student->getName();
            $viewArgs['notifications'] = array(
                'notifications' => $notificationsDAO->getNotifications(10),
                'total_unread' => $notificationsDAO->countUnreadNotification()
            );
            
            $this->load_template('error/404', $viewArgs, true);
        }
	}
}
