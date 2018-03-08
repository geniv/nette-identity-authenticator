Identity authenticator
======================

Installation
------------

```sh
$ composer require geniv/nette-identity-authenticator
```
or
```json
"geniv/nette-identity-authenticator": ">=1.0.0"
```

require:
```json
"php": ">=7.0.0",
"nette/nette": ">=2.4.0",
"dibi/dibi": ">=3.0.0"
```

Include in application
----------------------

### available source drivers:
- Identity\Authenticator\Drivers\ArrayDriver (base ident: key, id, hash)
- Identity\Authenticator\Drivers\NeonDriver (same format like Array)
- Identity\Authenticator\Drivers\DibiDriver (base ident: id, login, hash, active, role, added)
- Identity\Authenticator\Drivers\CombineDriver (combine driver Array, Neon, Dibi; order authenticate define combineOrder)

hash is return from: `Passwords::hash($password)`

`Identity\Authenticator\Drivers\CombineDriver` automatic use `autowired: self` other by configure

neon configure:
```neon
#identity authenticator
identityAuthenticator:
#   autowired: true
#   driver: Identity\Authenticator\Drivers\ArrayDriver([])
#   driver: Identity\Authenticator\Drivers\NeonDriver(%appDir%/authenticator.neon)
    driver: Identity\Authenticator\Drivers\DibiDriver(%tablePrefix%)
#   driver: Identity\Authenticator\Drivers\CombineDriver([
#       Identity\Authenticator\Drivers\DibiDriver(%tablePrefix%),
#       Identity\Authenticator\Drivers\NeonDriver(%appDir%/authenticator.neon)
#       Identity\Authenticator\Drivers\ArrayDriver([])
#   ])
```

neon configure extension:
```neon
extensions:
    identityAuthenticator: Identity\Authenticator\Bridges\Nette\Extension
```
