<?php

namespace LaFourchette\Entity;

use Symfony\Component\Serializer\Normalizer\DenormalizableInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizableInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class User implements NormalizableInterface, DenormalizableInterface
{
    /**
     * @var int
     */
    protected $idUser;

    /**
     * @var string
     */
    protected $username;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var string
     */
    protected $dn;

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->username;
    }

    /**
     * @param string $dn
     */
    public function setDn($dn)
    {
        $this->dn = $dn;
    }

    /**
     * @return string
     */
    public function getDn()
    {
        return $this->dn;
    }

    /**
     * @return int
     */
    public function getIdUser()
    {
        return $this->idUser;
    }

    /**
     * @param int $idUser
     */
    public function setIdUser($idUser)
    {
        $this->idUser = $idUser;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /** {@inheritdoc} */
    public function denormalize(DenormalizerInterface $denormalizer, $data, $format = null, array $context = array())
    {
        $this->idUser = $data['idUser'];
        $this->username = $data['username'];
        $this->email = $data['email'];
        $this->dn = $data['dn'];
    }

    /** {@inheritdoc} */
    public function normalize(NormalizerInterface $normalizer, $format = null, array $context = array())
    {
        return [
            'idUser' => $this->idUser,
            'username' => $this->username,
            'email' => $this->email,
            'dn' => $this->dn,
        ];
    }
}
