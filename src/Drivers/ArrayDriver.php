<?php declare(strict_types=1);

namespace Identity\Authenticator\Drivers;

use Nette\Localization\ITranslator;
use Nette\Security\AuthenticationException;
use Nette\Security\IAuthenticator;
use Nette\Security\Identity;
use Nette\Security\IIdentity;
use Nette\Security\Passwords;


/**
 * Class ArrayDriver
 *
 * @author  geniv
 * @package Identity\Authenticator\Drivers
 */
class ArrayDriver implements IAuthenticator
{
    /** @var array */
    private $userList;
    /** @var ITranslator */
    private $translator;


    /**
     * ArrayDriver constructor.
     *
     * @param array            $userList
     * @param ITranslator|null $translator
     */
    public function __construct(array $userList, ITranslator $translator = null)
    {
        $this->userList = $userList;
        $this->translator = $translator;
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

        if (isset($this->userList[$login])) {
            $result = $this->userList[$login];
            if (Passwords::verify($password, $result['hash'])) {
                unset($result['hash']);

                return new Identity($result['id'], (isset($result['role']) ? $result['role'] : null), $result);
            } else {
                throw new AuthenticationException($this->translator->translate('array-driver-invalid-credential'), self::INVALID_CREDENTIAL);
            }
        } else {
            throw new AuthenticationException($this->translator->translate('array-driver-identity-not-found'), self::IDENTITY_NOT_FOUND);
        }
    }
}
