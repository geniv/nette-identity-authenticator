<?php declare(strict_types=1);

namespace Identity\Authenticator\Drivers;

use Identity\Authenticator\Drivers\CombineDriver;
use Identity\Login\FormContainer;
use Identity\Login\LoginForm;
use Nette\DI\CompilerExtension;


/**
 * Class Extension
 *
 * @author  geniv
 * @package Identity\Authenticator\Drivers
 */
class Extension extends CompilerExtension
{
    /** @var array default values */
    private $defaults = [
        'autowired' => true,
        'driver'    => null,
    ];


    /**
     * Load configuration.
     */
    public function loadConfiguration()
    {
        $builder = $this->getContainerBuilder();
        $config = $this->validateConfig($this->defaults);

        if ($config['driver']) {
            // special way for combine driver
            if ($config['driver']->getEntity() == CombineDriver::class) {
                foreach ($config['driver']->arguments[0] as $index => $argument) {
                    $builder->addDefinition($this->prefix('driver.' . $index))
                        ->setFactory($argument)
                        ->setAutowired('self');
                }
            }

            $builder->addDefinition($this->prefix('driver'))
                ->setFactory($config['driver'])
                ->setAutowired($config['autowired']);
        }
    }
}
