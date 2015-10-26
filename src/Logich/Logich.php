<?php

namespace Logich;
use Misc\Session;

/**
 * Class Logich
 *
 * Se encarga del login, logout y verificación de
 * autentificación y permisos.
 *
 * @author Maxi Nivoli <m_nivoli@hotmail.com>
 * @package Logich
 */

class Logich{

    /**
     * @var
     */
	private $username;

    /**
     * @var
     */
	private $password;

    /**
     * @var
     */
	private $userModel;

    /**
     * @var Logich
     */
    private static $instance;

    /**
     * Get Instance
     *
     * Retorna una instancia de
     * Siervo.
     *
     * @return Logich
     */
    public static function getInstance(){
        if(!isset(self::$instance)){
            $clase = __CLASS__;
            self::$instance = new $clase;
        }
        return self::$instance;
    }

    /**
     * __clone
     *
     * Para que no se puedan crear nuevos
     * objetos por medio de la clonación.
     *
     * @return void
     */
    private function __clone(){}

	/**
	 * Construct
	 *
	 * Constructor privado de Logich, ya que el mismo implementa un Singleton.
	 *
	 * @access private
	 */
	private function __construct(){
		self::initLogin();
	}

	public function setUserModel(User $userModel = null){
		$this->userModel = $userModel;
	}

    public function getUserModel(){
        return $this->userModel;
    }

	/**
	 * Init Login
	 *
	 * Inicia el login, en este caso es solo iniciar una session.
	 * 
	 * @access public
	 * @return SE PODRIA HACER ALGUN TIPO DE CHEQUEO YA QUE DEVUELVE UN BOOLEAN EL METODO LLAMADO.
	 */
	public static function initLogin(){
		Session::start();			
	}

	/**
	 * Is Auth
	 *
	 * Verifica que el usuario este autenticado para navegar por las secciones que necesiten 
	 * logueo.
	 *
	 * @access public
	 * @return boolean true si esta autenticado, de lo contrario false.
	 */
	public function isAuth(){
        return $this->userModel->findSessionId(Session::getId(), Session::get('userId'));
	}	

	/**
	 * Set Login Data From POST
	 *
	 * Setea los datos que llegan desde POST.
	 *
	 * @access private
	 */
	private function setLoginData($user, $pass){
		$this->username = $user;
		$this->password = $pass;
	}

	/**
	 * Verifica que el nombre de usuario exista en la DB.
	 *
	 * VER SI ES MEJOR HACERLO PRIVADO!??!
	 * 
	 * @access public 
	 * @return boolean true en caso de que exista, de lo contrario false.
	 */
	public function checkUsername(){
        return $this->userModel->getUsername($this->username);
	}

	/**
	 * Login
	 *
	 * Loguea al usuario.
	 *
	 * @access public
	 * @return boolean true en caso de éxito, de lo contrario false.
	 */
	public function login($user = "", $pass = ""){
		$login = false;
		$this->setLoginData($user, $pass);
		if($this->checkUsername()){
			if($this->checkPassword()){
                $this->checkHash();
				$this->setUserSessionId();
				$this->setSession();
				$login = true;
			}
		}
		return $login;
	}

    /**
     * Check Hash
     *
     * Verifica si el algoritmo usado para generar el hash es
     * el más actualizado y supuestamente seguro.
     *
     */
    private function checkHash(){
        if(password_needs_rehash($this->password, PASSWORD_DEFAULT)):
            $this->reHash();
        endif;
    }

    /**
     * Re Hash
     *
     * Le dice al modelo de usuario que actualice el hash
     * de la password.
     *
     */
    private function reHash(){
        $this->userModel->updateHash($this->userModel->id, password_hash($this->password, PASSWORD_DEFAULT));
    }

	/**
	 * Logout
	 *
	 * Desloguea al usuario.
	 *
	 * @access public
	 * @return NO RETORNA NADA, VER SI ES NECESARIO QUE LO HAGA!??!
	 */
	public function logout(){
		$this->userModel->setSessionId(Session::get('userId'));
		Session::regenerateId();
		Session::destroy();
		Session::start();
	}

	/**
	 * Check Password
	 *
	 * Compara la pass de la DB con la que ingreso el usuario que se quiere loguear.
	 *
	 * @access public
	 * @return boolean true en caso de que sean iguales, en caso contrario false.
	 */
	public function checkPassword(){
		return password_verify($this->password, $this->userModel->password);
	}

    /**
     * Hash Password
     *
     * Obtiene un hash de la password ingresada.
     *
     * @param $password
     * @return String código hash generado.
     */
	public static function hashPassword($password){
        return password_hash($password, PASSWORD_DEFAULT);
	}

	/**
	 * Set User Session Id
	 *
	 * Le setea en DB el id de la sesison al usuario.
	 *
	 * @access private
	 */
	private function setUserSessionId(){
		$this->userModel->setSessionId($this->userModel->id, Session::getId());
	}

	/**
	 * Set Seesion
	 *
	 * Setea variables de session a la session creada por el login.
	 *
	 * @access private
	 */
	private function setSession(){
		Session::set("username", $this->userModel->username);
		Session::set("pass", $this->userModel->password);
        Session::set("userId", $this->userModel->id);
        Session::set('rol', $this->userModel->getRol($this->userModel->id));
	}
}