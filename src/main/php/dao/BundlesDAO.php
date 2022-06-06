<?php
declare (strict_types=1);

namespace dao;


use repositories\Database;
use domain\Bundle;
use domain\enum\OrderDirectionEnum;
use domain\enum\BundleOrderTypeEnum;


/**
 * Responsible for managing 'bundles' table.
 */
class BundlesDAO
{
    //-------------------------------------------------------------------------
    //        Attributes
    //-------------------------------------------------------------------------
    private $db;
    
    
    //-------------------------------------------------------------------------
    //        Constructor
    //-------------------------------------------------------------------------
    /**
     * Creates 'bundles' table manager.
     *
     * @param       Database $db Database
     */
    public function __construct(Database $db)
    {
        $this->db = $db->getConnection();
    }
    
    
    //-------------------------------------------------------------------------
    //        Methods
    //-------------------------------------------------------------------------
    /**
     * Gets a bundle
     * 
     * @param       int idBundle Bundle id or null if there is no bundle with
     * the given id
     * 
     * @return      Bundle Bundle with the given id
     * 
     * @throws      \InvalidArgumentException If bundle id is empty or less 
     * than or equal to zero
     */
    public function get(int $idBundle) : Bundle
    {
        if (empty($idBundle) || $idBundle <= 0) {
            throw new \InvalidArgumentException("Bundle id cannot be empty ".
                "or less than or equal to zero");
        }
        
        $response = null;
        
        // Query construction
        $sql = $this->db->prepare("
            SELECT  *
            FROM    bundles
            WHERE   id_bundle = ?
        ");
        
        // Executes query
        $sql->execute(array($idBundle));
        
        // Parses results
        if ($sql && $sql->rowCount() > 0) {
            $bundle = $sql->fetch();
            $response = new Bundle(
                (int) $bundle['id_bundle'], 
                $bundle['name'], 
                (float) $bundle['price'],
                $bundle['logo'],
                $bundle['description']
            );
        }
        
        return $response;
    }

    /**
     * Gets all registered bundles. If a filter option is provided, it gets 
     * only those bundles that satisfy these filters.
     * 
     * @param       int idStudent [Optional] Student id 
     * @param       int $limit [Optional] Maximum bundles returned
     * @param       string $name [Optional] Bundle name
     * @param       BundleOrderTypeEnum order_by [Optional] Ordering criteria 
     * @param       OrderDirectionEnum $orderType [Optional] Order that the 
     * elements will be returned. Default is ascending.
     * 
     * @return      array Bundles with the provided filters or empty array if
     * no bundles are found. If a student id is provided, also returns, for 
     * each bundle, if this student has it. Each position of the returned array
     * has the following keys:
     * <ul>
     *  <li><b>bundle</b>: Bundle information</li>
     *  <li><b>has_bundle</b>: If the student with the given id has this
     *  bundle</li>
     * </ul>
     */
    public function getAll(int $idStudent = -1, int $limit = -1, string $name = '',
        BundleOrderTypeEnum $orderBy = null, OrderDirectionEnum $orderType = null) : array
    {
        $response = array();
        $bindParams = array();

        if (empty($orderType)) {
            $orderType = new OrderDirectionEnum(OrderDirectionEnum::ASCENDING);
        }
        
        // Query construction
        $query = "
            SELECT      id_bundle, name, bundles.price, logo, description,
                        COUNT(id_course) AS courses,
        ";
        
        // If a student was provided, for each bundle add the information if he
        // has the bundle or not
        if ($idStudent > 0) {
            $query .= "
                        CASE
                            WHEN id_student = ? THEN 1
                            ELSE 0
                        END AS has_bundle,
            ";
            
            $bindParams[] = $idStudent;
        }
        
        $query .= "
                        COUNT(id_student) AS sales
            FROM        bundles 
                        NATURAL LEFT JOIN bundle_courses
                        LEFT JOIN purchases USING (id_bundle)
            GROUP BY    id_bundle, name, bundles.price, description
        ";
        
        // Limits the search to a specified name (if a name was specified)
        if (!empty($name)) {
            $query .= empty($orderBy) ? " HAVING name LIKE ?" : " HAVING name LIKE ?";
            $bindParams[] = $name.'%';
        }
        
        // Sets order by criteria (if any)
        if (!empty($orderBy)) {
            $query .= " ORDER BY ".$orderBy->get()." ".$orderType->get();
        }

        // Limits the results (if a limit was given)
        if ($limit > 0) {
            $query .= " LIMIT ".$limit;
        }
        
        // Prepares query
        $sql = $this->db->prepare($query);

        // Executes query
        $sql->execute($bindParams);
        
        // Parses results
        if ($sql && $sql->rowCount() > 0) {
            $bundles = $sql->fetchAll();
            $i = 0;
    
            foreach ($bundles as $bundle) {
                $response[$i]['bundle'] = new Bundle(
                    (int) $bundle['id_bundle'],
                    $bundle['name'],
                    (float) $bundle['price'],
                    $bundle['logo'],
                    $bundle['description']
                );
                
                if ($idStudent > 0) {
                    $response[$i]['has_bundle'] = $bundle['has_bundle'] > 0;
                }
                
                $i++;
            }
        }

        return $response;
    }
    
    /**
     * Gets bundles that contain at least all courses that the bundle with the
     * given id has, not including bundles that a student already has (if 
     * provided).
     * 
     *  @param      int idBundle Bundle id
     *  @param      int idStudent [Optional] Student id 
     *  
     *  @return     Bundle[] Bundles that are contained in the given bundle 
     *  disregarding those that the student already has
     *  
     *  @throws      \InvalidArgumentException If bundle id is empty or less 
     *  than or equal to zero
     */
    public function extensionBundles(int $idBundle, int $idStudent = -1) : array
    {
        if (empty($idBundle) || $idBundle <= 0) {
            throw new \InvalidArgumentException("Bundle id cannot be empty ".
                "or less than or equal to zero");
        }
            
        $response = array();
        $bind_params = array($idBundle);
        
        // Query construction
        $query = "
            SELECT  b.id_bundle, b.name, b.price, b.logo, b.description
            FROM    bundles b
                    LEFT JOIN purchases USING (id_bundle)
            WHERE   id_bundle != ? AND
        ";
        
        if ($idStudent > 0) {
            $query .= " 
                    (id_student IS NULL OR id_student != ?) AND 
            ";
            
            $bind_params[] = $idStudent;
        }
        
        $bind_params[] = $idBundle;
        
        $query .= "
                    NOT EXISTS (
                        SELECT  *
                        FROM    bundle_courses 
                        WHERE   id_bundle = ? AND
                                id_course NOT IN (SELECT    id_course
                                                  FROM      bundle_courses
                                                  WHERE     id_bundle = b.id_bundle)
                    )
         ";

        $sql = $this->db->prepare($query);
        
        // Executes query
        $sql->execute($bind_params);
        
        // Parses results
        if ($sql && $sql->rowCount() > 0) {
            foreach ($sql->fetchAll() as $bundle) {
                $response[] = new Bundle(
                    (int) $bundle['id_bundle'],
                    $bundle['name'],
                    (float) $bundle['price'],
                    $bundle['logo'],
                    $bundle['description']
                );
            }
        }

        return $response;
    }
    
    /**
     * Gets bundles that do not contain any courses in common with a
     * supplied bundle, disregarding those that a student already has (if 
     * provided).
     * 
     * @param       int idBundle Bundle id
     * @param       int idStudent [Optional] Student id
     * 
     * @return      Bundle[] Bundles that does not have courses contained in 
     * the given bundle disregarding those that the student already has
     * 
     * @throws      \InvalidArgumentException If bundle id is empty or less than
     * or equal to zero
     */
    public function unrelatedBundles(int $idBundle, int $idStudent = -1) : array
    {
        if (empty($idBundle) || $idBundle <= 0) {
            throw new \InvalidArgumentException("Bundle id cannot be empty ".
                "or less than or equal to zero");
        }
        
        $response = array();
        $bindParams = array($idBundle);
        
        // Query construction
        if ($idStudent > 0) {
            $query = "
                SELECT  *
                FROM    bundles b
                WHERE   id_bundle != ? AND
            ";
            
            $bindParams[] = $idStudent;
        }
        else {
            $query = "
                SELECT  *
                FROM    bundles b
                WHERE   id_bundle != ? AND
            ";
        }
        
        $query .= "
                NOT EXISTS (
                    SELECT  *
                    FROM    bundle_courses
                    WHERE   id_bundle = ? AND
                            id_course IN (SELECT id_course
                                          FROM   bundle_courses
                                          WHERE  id_bundle = b.id_bundle)
                )
        ";
        
        $bindParams[] = $idBundle;
        
        $sql = $this->db->prepare($query);
        
        $sql->execute($bindParams);
        
        // Parses results
        if ($sql && $sql->rowCount() > 0) {
            foreach ($sql->fetchAll() as $bundle) {
                $response[] = new Bundle(
                    (int) $bundle['id_bundle'], 
                    $bundle['name'], 
                    (float) $bundle['price'],
                    $bundle['logo'],
                    $bundle['description']
                );
                
            }
        }
            
        return $response;
    }
    
    /**
     * Gets the total number of classes that a bundle has along with its 
     * duration (in minutes).
     * 
     * @param       int idBundle Bundle id
     * 
     * @return      array Total of classes that the bundle has along with its 
     * duration (in minutes). The returned array has the following keys:
     * <ul>
     *  <li><b>total_classes</b>: Total of classes that the bundle has</li>
     *  <li><b>total_length</b>: Total duration of the classes that the bundle
     *  has</li>
     * </ul>
     * 
     * @throws      \InvalidArgumentException If bundle id is empty or less 
     * than or equal to zero
     * 
     * @implSpec    It will always return an array with the two keys informed
     * above, even if both have zero value
     */
    public function countTotalClasses(int $idBundle) : array
    {
        if (empty($idBundle) || $idBundle <= 0) {
            throw new \InvalidArgumentException("Bundle id cannot be empty ".
                "or less than or equal to zero");
        }
            
        $response = array(
            "total_classes" => 0,
            "total_length" => 0
        );
        
        // Query construction
        $sql = $this->db->prepare("
            SELECT      COUNT(id_module) AS total_classes, 
                        SUM(length) AS total_length
            FROM        (SELECT      id_module, 5 AS length
                         FROM        questionnaires
                         UNION ALL
                         SELECT      id_module, length
                         FROM        videos) AS tmp
            GROUP BY    id_module
            HAVING      id_module IN (SELECT    id_module
                                      FROM      course_modules NATURAL JOIN bundle_courses
                                      WHERE     id_bundle = ?)
        ");
        
        // Executes query
        $sql->execute(array($idBundle));
        
        // Parses results
        if ($sql && $sql->rowCount() > 0) {
            foreach ($sql->fetchAll() as $result) {
                $response["total_classes"] += $result["total_classes"];
                $response["total_length"] += $result["total_length"];
            }
        }
        
        return $response;
    }
    
    /**
     * Gets total of bundles.
     *
     * @return      int Total of bundles
     */
    public function getTotal() : int
    {
        return (int) $this->db->query("
            SELECT  COUNT(*) AS total
            FROM    bundles
        ")->fetch()['total'];
    }
}