<?php

namespace Logich;

/**
 * Interface User
 *
 * La debe implementar la clase Usuario para utilizar
 * Logich para login de usuarios.
 *
 * @author Maxi Nivoli <m_nivoli@hotmail.com>
 * @package Logich
 */

interface User {

    /**
     * Find Session Id
     *
     * Busca si el id de la session actual
     * esta asociado al usuario actual
     * retorna true si encontro, false
     * de lo contrario..
     *
     * @param string $sessionId
     * @param string $userId
     * @return boolean
     */
    public function findSessionId($sessionId = "", $userId = '');

    /**
     * Get Username
     *
     * Busca el usuario por su username, retorna
     * true en caso de que exista, false de lo
     * contrario, en este método se debe setear,
     * si es que se encontro el usuario, el objeto
     * que implementa esta interfaz y que se le pasa
     * a Logich, ya que de ese objeto Logich toma los
     * datos como pass y username entre otros.
     *
     * @param string $username
     * @return boolean
     */
    public function getUsername($username = "");

    /**
     * Set Session Id
     *
     * Setea el id de session en la db,
     * asociandolo al usuario.
     *
     * @param $userId
     * @param string $sessionId
     */
    public function setSessionId($userId, $sessionId = "naranja");

    /**
     * Update Hash
     *
     * En caso de que exista una actualización
     * para el algoritmo de hash, se hace hash
     * con el nuevo algoritmo a la pass y se
     * actualiza segun el usuario.
     *
     * @param $userId
     * @param $hash
     */
    public function updateHash($userId, $hash);

    /**
     * Get Rol
     *
     * En caso de que el esquema implemente
     * roles se debe retornar el nombre del
     * rol de acuerdo al id del usuario.
     *
     * @param $userId
     * @return string
     */
    public function getRol($userId);
}