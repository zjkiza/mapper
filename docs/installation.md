# Installation

Add "zjkiza/dto-mapper" to your composer.json file:
```
composer require zjkiza/dto-mapper
```

## Symfony integration

Bundle wires up all classes together and provides method to easily setup.

1. Register bundle within your configuration (i.e: `bundles.php`).

   ```php
   <?php
   
   declare(strict_types=1);
   
   return [
       // other bundles
       Zjk\DtoMapper\ZJKizaMapperBundle::class =>  ['all' => true],
   ];
   ```

2. The default configuration does not use redis. In order to activate redis on the dto mapper, it is necessary to set the configuration on the package.
   In the file zjk_dto_mapper.yaml
     ```yaml
       zjk_dto_mapper:
           cache_pool: 'cache.app.zjk_dto_mapper'
     ```
   In the section for symfony cache:
     ```yaml
     framework:
         cache:
             pools:
                 cache.app.zjk_dto_mapper: ~
     ```
