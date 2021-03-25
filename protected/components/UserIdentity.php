<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class UserIdentity extends CUserIdentity {

    private $uid;
    private $company_id;
    private $department_id;

    public function authenticate() {

        $users = Users::model()->findByAttributes(array('username' => $this->username, 'online' => 1));

        if ($users == null) {
            $this->errorCode = self::ERROR_USERNAME_INVALID;
        } elseif ($users->password !== md5($this->password)) {
            $this->errorCode = self::ERROR_PASSWORD_INVALID;
        } else {
            $this->errorCode = self::ERROR_NONE;
            $this->uid = $users->id;
            $this->setState("userid", $users->id);
        }

        return !$this->errorCode;
    }

    public function getId() {
        return $this->uid;
    }

    

}
