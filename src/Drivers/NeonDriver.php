<?php declare(strict_types=1);

namespace Identity\Authenticator\Drivers;

use Nette\Neon\Neon;


/**
 * Class NeonDriver
 *
 * @author  geniv
 * @package Identity\Authenticator\Drivers
 */
class NeonDriver extends ArrayDriver
{

    /**
     * NeonDriver constructor.
     *
     * @param string $path
     */
    public function __construct(string $path)
    {
        $userList = [];
        if ($path && file_exists($path)) {
            $userList = Neon::decode(file_get_contents($path));
        }
        parent::__construct($userList);
    }
}
