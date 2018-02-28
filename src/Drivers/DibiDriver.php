<?php declare(strict_types=1);

namespace Identity\Authenticator\Drivers;

use Dibi\Connection;
use Dibi\Fluent;
use Nette\Security\AuthenticationException;
use Nette\Security\IAuthenticator;
use Nette\Security\IIdentity;
use Nette\Security\Passwords;
use Nette\Security\Identity;
use Nette\SmartObject;


/**
 * Class DibiDriver
 *
 * @author  geniv
 * @package Identity\Authenticator\Drivers
 */
class DibiDriver implements IAuthenticator
{
    use SmartObject;

    // define constant table names
    const
        TABLE_NAME = 'identity';

    /** @var Connection */
    private $connection;
    /** @var string */
    private $tableIdentity;
    /** @var array */
    private $columns = ['id', 'login', 'hash', 'username', 'email', 'role', 'active', 'added'];


    /**
     * DibiDriver constructor.
     *
     * @param string     $tablePrefix
     * @param Connection $connection
     */
    public function __construct(string $tablePrefix, Connection $connection)
    {
        $this->connection = $connection;
        // define table names
        $this->tableIdentity = $tablePrefix . self::TABLE_NAME;
    }


    /**
     * Get columns.
     *
     * @return array
     */
    public function getColumns(): array
    {
        return $this->columns;

    }


    /**
     * Set columns.
     *
     * @param $columns
     * @return $this
     */
    public function setColumns(array $columns): self
    {
        $this->columns = $columns;
        return $this;
    }


    /**
     * Get list.
     *
     * @return Fluent
     */
    public function getList(): Fluent
    {
        return $this->connection->select($this->columns)
            ->from($this->tableIdentity);
    }


    /**
     * Insert user.
     *
     * @param string $login
     * @param string $password
     * @param string $role
     * @param bool   $active
     * @return \Dibi\Result|int
     * @throws \Dibi\Exception
     */
    public function insertUser(string $login, string $password, string $role = '', bool $active = true)
    {
        // insert to base columns
        $args = [
            'login'     => $login,
            'hash'      => $this->getHash($password),
            'role'      => $role ?: null,
            'active'    => $active,
            'added%sql' => 'NOW()',
        ];
        return $this->connection->insert($this->tableIdentity, $args)->execute();
    }


    /**
     * Delete user.
     *
     * @param int $id
     * @return \Dibi\Result|int
     * @throws \Dibi\Exception
     */
    public function deleteUser(int $id)
    {
        return $this->connection->delete($this->tableIdentity)
            ->where(['id' => $id])
            ->execute();
    }


    /**
     * Get hash.
     *
     * @param string $password
     * @return string
     */
    public function getHash(string $password): string
    {
        return Passwords::hash($password);
    }


    /**
     * Performs an authentication against e.g. database.
     * and returns IIdentity on success or throws AuthenticationException
     *
     * @param array $credentials
     * @return IIdentity
     * @throws AuthenticationException
     */
    public function authenticate(array $credentials)
    {
        list($login, $password) = $credentials;

        $result = $this->getList()
            ->where(['login' => $login, 'active' => true])
            ->fetch();

        if ($result) {
            if (Passwords::verify($password, $result['hash'])) {
                if ($result['active']) {
                    $arr = $result->toArray();
                    unset($arr['hash']);

                    return new Identity($result['id'], $result['role'], $arr);
                } else {
                    throw new AuthenticationException('Not active account.', self::NOT_APPROVED);
                }
            } else {
                throw new AuthenticationException('The password is incorrect.', self::INVALID_CREDENTIAL);
            }
        } else {
            throw new AuthenticationException('The username is incorrect.', self::IDENTITY_NOT_FOUND);
        }
    }
}
