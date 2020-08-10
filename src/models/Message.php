<?php
declare (strict_types=1);

namespace models;

use DateTime;


/**
 * Responsible for representing messages of a support topic or a class.
 *
 * @author		William Niemiec &lt; williamniemiec@hotmail.com &gt;
 * @version		1.0.0
 * @since		1.0.0
 */
class Message
{
    //-------------------------------------------------------------------------
    //        Attributes
    //-------------------------------------------------------------------------
    private $user;
    private $date;
    private $message;
    private $id;
    
    
    //-------------------------------------------------------------------------
    //        Constructor
    //-------------------------------------------------------------------------
    /**
     * Creates a representation of a message.
     *
     * @param       User $user User who answered the topic
     * @param       DateTime $date Message posting date
     * @param       string $message Message content
     * @param       int $id [Optional] Message id
     */
    public function __construct(User $user, DateTime $date, string $message, int $id = -1)
    {
        $this->user = $user;
        $this->date = $date;
        $this->message = $message;
        $this->id = $id;
    }
    
    
    //-------------------------------------------------------------------------
    //        Getters
    //-------------------------------------------------------------------------
    /**
     * Gets user who created the message.
     * 
     * @return      User User who created the message
     */
    public function getCreator() : User
    {
        return $this->user;
    }
    
    /**
     * Gets message creation date.
     *
     * @return      DateTime Message creation date
     */
    public function getDate() : DateTime
    {
        return $this->date;
    }
    
    /**
     * Gets message content.
     *
     * @return      string Message content
     */
    public function getContent() : string
    {
        return $this->message;
    }
    
    /**
     * Gets message id.
     *
     * @return      int Message id or -1 if there is no id
     */
    public function getId() : int
    {
        return $this->id;
    }
}