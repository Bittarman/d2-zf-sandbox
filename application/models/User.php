<?php



/**
 * User
 */
class User
{
    /**
     * @var string $name
     */
    private $name;

    /**
     * @var string $other_name
     */
    private $other_name;

    /**
     * @var integer $id
     */
    private $id;

    /**
     * Set name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get name
     *
     * @return string $name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set other_name
     *
     * @param string $otherName
     */
    public function setOtherName($otherName)
    {
        $this->other_name = $otherName;
    }

    /**
     * Get other_name
     *
     * @return string $otherName
     */
    public function getOtherName()
    {
        return $this->other_name;
    }

    /**
     * Get id
     *
     * @return integer $id
     */
    public function getId()
    {
        return $this->id;
    }

}