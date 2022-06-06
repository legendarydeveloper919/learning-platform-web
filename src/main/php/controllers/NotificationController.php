<?php
namespace controllers;


use config\Controller;
use repositories\pdo\MySqlPDODatabase;
use domain\Student;
use dao\NotificationsDAO;


/**
 * Responsible for handling ajax requests for notifications.
 */
class NotificationController extends Controller
{
    //-------------------------------------------------------------------------
    //        Methods
    //-------------------------------------------------------------------------
    /**
     * @Override
     */
    public function index ()
    {
        $this->redirectToRoot();
    }
    
    
    //-------------------------------------------------------------------------
    //        Ajax
    //-------------------------------------------------------------------------
    /**
     * Marks a notification as read.
     *
     * @param       int $_POST['id_notification'] Notification id
     *
     * @apiNote     Must be called using POST request method
     */
    public function read()
    {
        // Checks if it is an ajax request
        if ($this->getHttpRequestMethod() != 'POST') {
            $this->redirectToRoot();
        }
            
        if (empty($_POST['id_notification'])) {
            return;
        }
        
        $dbConnection = new MySqlPDODatabase();
        
        $notificationsDao = new NotificationsDAO($dbConnection, Student::getLoggedIn($dbConnection)->getId());
        
        $notificationsDao->markAsRead((int) $_POST['id_notification']);
    }
    
    /**
     * Marks a notification as unread.
     *
     * @param       int $_POST['id_notification'] Notification id
     *
     * @apiNote     Must be called using POST request method
     */
    public function unread()
    {
        // Checks if it is an ajax request
        if ($this->getHttpRequestMethod() != 'POST') {
            $this->redirectToRoot();
        }
            
        if (empty($_POST['id_notification'])) {
            return;
        }
        
        $dbConnection = new MySqlPDODatabase();
        
        $notificationsDao = new NotificationsDAO($dbConnection, Student::getLoggedIn($dbConnection)->getId());
        
        $notificationsDao->markAsUnread((int)$_POST['id_notification']);
    }
    
    /**
     * Deletes a module from a course.
     *
     * @param       int $_POST['id_notification'] Notification id to be deleted
     *
     * @apiNote     Must be called using POST request method
     */
    public function delete()
    {
        // Checks if it is an ajax request
        if ($this->getHttpRequestMethod() != 'POST') {
            $this->redirectToRoot();
        }
            
        if (empty($_POST['id_notification'])) { 
            return; 
        }
        
        $dbConnection = new MySqlPDODatabase();
        
        $notificationsDao = new NotificationsDAO($dbConnection, Student::getLoggedIn($dbConnection)->getId());
        
        $notificationsDao->delete((int)$_POST['id_notification']);
    }
}