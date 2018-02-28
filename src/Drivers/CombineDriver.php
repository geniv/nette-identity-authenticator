<?php declare(strict_types=1);

namespace Identity\Authenticator\Drivers;

use Nette\Security\AuthenticationException;
use Nette\Security\IAuthenticator;
use Nette\Security\IIdentity;


/**
 * Class CombineDriver
 *
 * @author  geniv
 * @package Identity\Authenticator\Drivers
 */
class CombineDriver implements IAuthenticator
{
    /** @var array */
    private $drivers = [];


    /**
     * CombineDriver constructor.
     *
     * @param array $drivers
     */
    public function __construct(array $drivers)
    {
        $this->drivers = $drivers;
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
        $identity = null;
        $lastException = null;

        foreach ($this->drivers as $driver) {
            try {
                $identity = $driver->authenticate($credentials);
                if ($identity) {
                    $identity->driver = get_class($driver);    // set identity data to key driver
                    return $identity;
                }
            } catch (AuthenticationException $e) {
                $lastException = $e;
            }
        }

        if (!$identity && $lastException) {
            throw $lastException;
        }
    }
}
