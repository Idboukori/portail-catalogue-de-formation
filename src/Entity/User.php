<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(fields="email", message="Email already used")
 * @UniqueEntity(fields="username", message="Username already used")
 */
class User implements UserInterface , \Serializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     *  @Assert\Email(
     *  message = "The email '{{ value }}' is not a valid email.",
     *  checkMX = true
     * )
     */
    private $email;

    

     /**
     * @ORM\Column(type="string", length=20)
     */
    private $username;


    /**
     * @ORM\Column(type="string", length=20)
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $prenom;

    /**
   * @Assert\NotBlank()
   * @Assert\Length(max=4096)
   * @Assert\EqualTo(propertyPath="password", message=" Vous n'avez pas tapé le meme Mot de passe")
   */
  private $confirm_password;

  /**
     * @ORM\Column(type="json")
     * @ORM\Column(type="array")
     */
    private $roles ;

    public function __construct()
    {
        $this->isActive = true;
		$this->roles = ['ROLE_USER'];
    }

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * Assert\Length(min="8",minMessage="Votre mot de passe doit etre au minimum 8 caractére")
     */
    private $password;


    /**
	 * @ORM\Column(name="is_active", type="boolean")
	 */
    private $isActive;
    
     /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var \DateTime
     */
    private $passwordRequestedAt;
    
    /**
    * @var string
    *
    * @ORM\Column(type="string", length=255, nullable=true)
    */
    private $token;

    /**
     * @ORM\Column(type="integer")
     */
    private $state;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $image;

    public function __toString(){
        return $this->id."";
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }


    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }


    public function getNom(): string
    {
        return (string) $this->nom;
    }


    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }
    
    public function getPrenom(): string
    {
        return (string) $this->prenom;
    }


    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }



    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        return (array)$this->roles;
        

         
    }

    public function setRoles(array $roles): self
    {
        if (!in_array('ROLE_USER', $roles))
		{
			$roles[] = 'ROLE_USER';
		}
		foreach ($roles as $role)
		{
			if(substr($role, 0, 5) !== 'ROLE_') {
				throw new InvalidArgumentException("Chaque rôle doit commencer par 'ROLE_'");
			}
		}
		$this->roles = $roles;
		return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getConfirmPassword(): string
    {
        return (string) $this->confirm_password;
    }

    public function setConfirmPassword(string $confirm_password): self
    {
        $this->confirm_password = $confirm_password;

        return $this;
    }


    /*
	 * Get isActive
	 */
	public function getIsActive()
                  	{
                  		return $this->isActive;
                  	}
	/*
	 * Set isActive
	 */
	public function setIsActive($isActive)
                  	{
                  		$this->isActive = $isActive;
                  		return $this;
                  	}


    /** @see \Serializable::serialize() */
	public function serialize()
                  	{
                  		return serialize(array(
                  			$this->id,
                  			$this->username,
                  			$this->password,
                  			$this->isActive,
                  			// voir remarques sur salt plus haut
                  			// $this->salt,
                  		));
                  	}
	/** @see \Serializable::unserialize() */
	public function unserialize($serialized)
                  	{
                  		list (
                  			$this->id,
                  			$this->username,
                  			$this->password,
                  			$this->isActive,
                  			// voir remarques sur salt plus haut
                  			// $this->salt
                  		) = unserialize($serialized);
                  	}

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }


    /*
     * Get passwordRequestedAt
     */
    public function getPasswordRequestedAt()
    {
        return $this->passwordRequestedAt;
    }
    /*
     * Set passwordRequestedAt
     */
    public function setPasswordRequestedAt($passwordRequestedAt)
    {
        $this->passwordRequestedAt = $passwordRequestedAt;
        return $this;
    }
    /*
     * Get token
     */
    public function getToken()
    {
        return $this->token;
    }
    /*
     * Set token
     */
    public function setToken($token)
    {
        $this->token = $token;
        return $this;
    }

    public function getState(): ?int
    {
        return $this->state;
    }

    public function setState(int $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }


}
