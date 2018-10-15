<?php

require_once 'DBConfig.php';
require_once 'Hash.php';

class DB {
    //
    // STATIC MEMBERS (initialized only once)
    //
    private static  $instance,
                    $tables;

    //
    // MEMBERS
    //
    private $pdo,
            $lastErrorStatus,
            $lastErrorMsg;

    //
    // CONSTRUCTOR, private to disallow external instantiation
    //
    private function __construct() {
        try {
            $this->setErrorStatus(false);
            $this->setErrorMsg('');

            $dsn = DBConfig::DB_TYPE .
                    ':host=' . DBConfig::HOSTNAME .
                    ';dbname=' . DBConfig::DB_NAME .
                    ';charset=' . DBConfig::CHARSET .
                    ';';
            $this->pdo = new PDO($dsn, DBConfig::USERNAME, DBConfig::PASSWORD, DBConfig::OPTIONS);
            $this->getTablesList();
        } catch(PDOException $e) {
            $this->setErrorStatus(true);
            $this->setErrorMsg($e->getMessage());
        }
    }

    /**
     * @return DB
     */
    public static function getInstance() : DB {
        if(!isset(self::$instance)) {
            self::$instance = new DB();
        }

        return self::$instance;
    }

    //
    // ERROR HANDLING
    //

    /**
     * @param bool $status
     */
    private function setErrorStatus(bool $status) {
        $this->lastErrorStatus = $status;
    }

    /**
     * @return bool
     */
    public function getErrorStatus() : bool {
        return $this->lastErrorStatus;
    }

    /**
     * @param string $msg
     */
    private function setErrorMsg(string $msg) {
        $this->lastErrorMsg = $msg;
    }

    /**
     * @return string
     */
    public function getErrorMsg() : string {
        return $this->lastErrorMsg;
    }
    
    //
    // QUERIES
    //

    /**
     * @param string $sql
     * @param array $bindings
     * @param int $fetchMode
     * @param int $column
     * @return array
     */
    public function query(string $sql, array $bindings = [], int $fetchMode = PDO::FETCH_OBJ, int $column = 0) {
        $pdoStatement = $this->pdo->prepare($sql);

        foreach($bindings as $param => $bind) {
            $pdoStatement->bindValue($param, $bind);
        }

        $pdoStatement->execute();
        switch($fetchMode) {
            case PDO::FETCH_COLUMN :
                $pdoStatement->setFetchMode($fetchMode, $column);
                break;
            case PDO::FETCH_ASSOC :
                $pdoStatement->setFetchMode($fetchMode);
                break;
            default:
                break;
        }
        
        return $pdoStatement->fetchAll();
    }

    /**
     * @param string $sql
     * @param array $bindings
     * @return int
     */
    public function update(string $sql, array $bindings = []) : int {
        try {
            $this->setErrorStatus(false);
            $this->setErrorMsg('');
            $this->pdo->beginTransaction();
            $pdoStatement = $this->pdo->prepare($sql);
            foreach($bindings as $param => $bind) {
                $pdoStatement->bindValue($param, $bind);
            }
            $pdoStatement->execute();
            $this->pdo->commit();
            return $pdoStatement->rowCount();
        } catch(PDOException $e) {
            $this->setErrorStatus(false);
            $this->setErrorMsg($e->getMessage());
            $this->pdo->rollBack();
        }

        return 0;
    }

    /**
     * @param string $sql
     * @param array $bindings
     * @return int
     */
    public function insert(string $sql, array $bindings = []) : int {
        try {
            $this->setErrorStatus(false);
            $this->setErrorMsg('');
            $this->pdo->beginTransaction();
            $pdoStatement = $this->pdo->prepare($sql);
            foreach($bindings as $param => $bind) {
                $pdoStatement->bindValue($param, $bind);
            }
            $pdoStatement->execute();
            $insertedId = $this->pdo->lastInsertId();
            $this->pdo->commit();
            return $insertedId;
        } catch(PDOException $e) {
            $this->setErrorStatus(false);
            $this->setErrorMsg($e->getMessage());
            $this->pdo->rollBack();
        }

        return 0;
    }

    /**
     * @param string $sql
     * @param array $bindings
     * @return int
     */
    public function delete(string $sql, array $bindings = []) : int {
        try {
            $this->setErrorStatus(false);
            $this->setErrorMsg('');
            $this->pdo->beginTransaction();
            $pdoStatement = $this->pdo->prepare($sql);
            foreach($bindings as $param => $bind) {
                $pdoStatement->bindValue($param, $bind);
            }
            $pdoStatement->execute();
            $this->pdo->commit();
            return $pdoStatement->rowCount();
        } catch(PDOException $e) {
            $this->setErrorStatus(false);
            $this->setErrorMsg($e->getMessage());
            $this->pdo->rollBack();
        }

        return 0;
    }

    //
    // HELPERS
    //

    /**
     * @return array
     */
    private function getTablesList() : array {
        if(!(isset(self::$tables))) {
            $sql = 'SHOW TABLES';
            $pdoStatement = $this->pdo->query($sql);
            self::$tables = $pdoStatement->fetchAll(PDO::FETCH_COLUMN);
        }

        return self::$tables;
    }

    /**
     * @return array
     */
    public function getCategoryNames() : array {
        $sql = 'SELECT categoryName FROM category;';
        $categories = $this->pdo->query($sql);
        return $categories->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * @param int $id
     * @return bool
     */
    public function isAuthorIdValid(int $id) : bool {
        $sql = 'SELECT DISTINCT idUser FROM post WHERE idUser = :id';
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->bindValue('id', $id);
        $pdoStatement->execute();
        $idUsers = $pdoStatement->fetchAll(PDO::FETCH_COLUMN); // returns an empty array if no results
        if(count($idUsers)) {
            return $idUsers[0] == $id;
        }
        
        return false;
    }

    /**
     * @param int $id
     * @return bool
     */
    public function isArticleValid(int $id) : bool {
        $sql = 'SELECT idPost FROM post WHERE idPost = :id';
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->bindValue('id', $id);
        $pdoStatement->execute();
        $idPosts = $pdoStatement->fetchAll(PDO::FETCH_COLUMN);
        if(count($idPosts)) {
            return $idPosts[0] == $id;
        }

        return false;
    }

    /**
     * @param int $id
     * @return bool
     */
    public function isUserValid(int $id) : bool {
        $sql = 'SELECT idUser FROM user WHERE idUser = :id';
        $bindings = ['id' => $id];
        $userId = $this->query($sql, $bindings, PDO::FETCH_COLUMN, 0);
        if(count($userId)) {
            return $userId[0] == $id;
        }
        
        return false;
    }

    /**
     * @param string $login
     * @param string $password
     * @param string $isAdmin
     * @return bool
     */
    public function isLoginValid(string $login, string $password, string $isAdmin) : bool {
        $sql = 'SELECT hashedPass, salt FROM user WHERE login = :login AND isAdmin = :isAdmin AND isActive = "1"' ;
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->bindValue('login', escape($login));
        $pdoStatement->bindValue('isAdmin', escape($isAdmin));
        $pdoStatement->execute();
        $user = $pdoStatement->fetch(); // if failure, returns false in all cases
        if($user) {
            return $user->hashedPass == Hash::make($password, $user->salt);
        }

        return false;
    }

    /**
     * @param string $login
     * @param string $password
     * @return bool
     */
    public function isLoginAdminValid(string $login, string $password) : bool {
        return $this->isLoginValid($login, $password, "1");
    }

    /**
     * @param string $login
     * @param string $password
     * @return bool
     */
    public function isLoginUserValid(string $login, string $password) : bool {
        return $this->isLoginValid($login, $password, "0");
    }

    /**
     * @param string $login
     * @param string $token
     * @return bool
     */
    public function isSessionValid(string $login, string $token) : bool {
        $sql = 'SELECT sessionId FROM user WHERE login = :login';
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->bindValue('login', escape($login));
        $pdoStatement->execute();
        $user = $pdoStatement->fetch();
        if($user) {
            return $user->sessionId == $token;
        }

        return false;
    }

    /**
     * @param string $login
     */
    public function deleteSession(string $login) {
        $sql = 'UPDATE user SET sessionId = null WHERE login = :login';
        $bindings = ['login' => escape($login)];
        $this->update($sql, $bindings);
    }
}