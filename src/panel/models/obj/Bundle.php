<?php
declare (strict_types=1);

namespace models\obj;

use models\Courses;
use models\Bundles;


/**
 * Responsible for representing bundles.
 *
 * @author		William Niemiec &lt; williamniemiec@hotmail.com &gt;
 * @version		1.0.0
 * @since		1.0.0
 */
class Bundle
{
    //-------------------------------------------------------------------------
    //        Attributes
    //-------------------------------------------------------------------------
    private $id_bundle;
    private $name;
    private $price;
    private $description;
    private $courses;
    private $totalClasses;
    private $totalLength;
    
    
    //-------------------------------------------------------------------------
    //        Constructor
    //-------------------------------------------------------------------------
    /**
     * Creates a representation of a bundle.
     *
     * @param       int $id_bundle Bundle id
     * @param       string $name Bundle name
     * @param       float $price Bundle price
     * @param       string $description [Optional] Bundle description
     */
    public function __construct(int $id_bundle, string $name, float $price, string $description = '')
    {
        $this->id_bundle = $id_bundle;
        $this->name = $name;
        $this->price = $price;
        $this->description = empty($description) ? '' : $description;
    }
    
    
    //-------------------------------------------------------------------------
    //        Getters
    //-------------------------------------------------------------------------
    /**
     * Gets bundle id.
     *
     * @return      int Bundle id
     */
    public function getBundleId() : int
    {
        return $this->id_bundle;
    }
    
    /**
     * Gets bundle name.
     *
     * @return      string Bundle name
     */
    public function getName() : string
    {
        return $this->name;
    }
    
    /**
     * Gets bundle price.
     *
     * @return      float Bundle price
     */
    public function getPrice() : float
    {
        return $this->price;
    }
    
    /**
     * Gets bundle description.
     *
     * @return      string Bundle description or empty string if
     * bundle does not have a description
     */
    public function getDescription() : string
    {
        return $this->description;
    }
    
    /**
     * Gets courses that belongs to the bundle.
     * 
     * @return      \models\obj\Course[] Courses that belongs to the bundle or
     * empty array if there are no courses in the bundle
     * 
     * @implNote    Lazy initialization
     */
    public function getCourses() : array
    {
        if (empty($this->courses)) {
            $courses = new Courses();
            
            $this->courses = $courses->getFromBundle($this->id_bundle);
        }
        
        return $this->courses;
    }
    
    /**
     * Gets the total length of the bundle.
     * 
     * @return      int Total length of the bundle
     * 
     * @implNote    Lazy initialization
     */
    public function getTotalLength() : int
    {
        if (empty($this->totalLength)) {
            $bundles = new Bundles();
            $total = $bundles->countTotalClasses();
            
            $this->totalLength = $total['total_length'];
            $this->totalClasses = $total['total_classes'];
        }
        
        return $this->totalLength;
    }
    
    /**
     * Gets the total classes of the bundle.
     *
     * @return      int Total classes of the bundle
     *
     * @implNote    Lazy initialization
     */
    public function getTotalClasses() : int
    {
        if (empty($this->totalClasses)) {
            $bundles = new Bundles();
            $total = $bundles->countTotalClasses();
            
            $this->totalLength = $total['total_length'];
            $this->totalClasses = $total['total_classes'];
        }
        
        return $this->totalClasses;
    }
}
