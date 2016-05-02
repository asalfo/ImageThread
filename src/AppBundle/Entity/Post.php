<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Post
 *
 * @ORM\Table(name="post")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PostRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Post
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @var \DateTime $created
     *
     * @ORM\Column(name="created",type="datetime")
     */
    private $created;

    /**
     * @var string
     *
     * @ORM\Column(name="image_path", type="string", length=255)
     */
    private $image_path;
    

    /**
     * Post constructor.
     */
    public function __construct()
    {
        $this->created = new \DateTime('now');
    }


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Post
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param \DateTime $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }

    /**
     * @return string
     */
    public function getImagePath()
    {
        return $this->image_path;
    }

    /**
     * @param string $image_path
     */
    public function setImagePath($image_path)
    {
        $this->image_path = $image_path;
    }


    public function  toArray(){

        return [ $this->getImagePath(), $this->getImagePath()];
    }
}

