# Laravel Data transfer objects helper

[![Latest Version on Packagist](https://img.shields.io/packagist/v/bpartner/data-helper.svg?style=flat-square)](https://packagist.org/packages/bpartner/dto)
[![Total Downloads](https://img.shields.io/packagist/dt/bpartner/data-helper.svg?style=flat-square)](https://packagist.org/packages/bpartner/dto)


Simple create DTO from any array.

## Installation

You can install the package via composer:

```bash
composer require bpartner/data-helper
```

## Usage
Declare you Dto object. You can use any types declarations.

For array or collection you can use PHPDoc annotation with type of data inside. Supported only array and collection.

```php
class DemoDto extends DtoAbstract
{
    public string $name;
    public Carbon $date;
    public DtoOtherObject $otherObject;
    
    /** @var collection<\App\Dto\DtoOtherObject>  */
    public Collection $objectsCollection;
    
    /** @var array<\App\Dto\DtoOtherObject>  */
    public array $objectsArray;
    
    /** @var collection<\App\Dto\DtoOtherObject>  */
    public array $objectsArrayOfCollection;
    
    public Fluent $fluent;
    public Request $request;
}
```

Create DTO from any array data (example: request()->all()) by facade Dto

```php
$data = request()->all();
$dto = Dto::build(DemoDto::class, $data);
```

Now you can transfer your DTO to any Object as parameter and use:

```php
$name = $dto->name;
$objectCollection = $dto->objectsCollection;
```

You can transform input parameters to any string format by third parameter. (example: first_name -> firstName)

```php
class DemoDto extends DtoAbstract
{
    public string $firstName;
}

$inputData = [
    'first_name' => 'John Doe'
];

$dto = Dto::build(DemoDto::class, $inputData, DtoFactory::CAMEL_CASE);

```

Transform your DTO to array or flat array.

```php
$array = $dto->toArray();

array:4 [
  "name" => "Demo data"
  "date" => "06-11-2021"
  "phone" => array:2 [
    "type" => "home"
    "number" => "500-123-123"
  ]
  "phones" => array:2 [
    0 => array:2 [
      "type" => "work"
      "number" => "500-123-122"
    ]
    1 => array:2 [
      "type" => "private"
      "number" => "500-123-124"
    ]
  ]
]

$flat = $dto->flatArray();

array:5 [                       
  "name" => "Demo data"         
  "date" => "06-11-2021"        
  "type" => "home"              
  "number" => "500-123-123"     
  "phones" => array:2 [         
    0 => array:2 [              
      "type" => "work"          
      "number" => "500-123-122" 
    ]                           
    1 => array:2 [              
      "type" => "private"       
      "number" => "500-123-124" 
    ]                           
  ]                             
]                               
```
Important! Collection and nested array are not flip to flat array.

## Credits

- [Alexander Zinchenko](https://github.com/bpartner)
- Thanks to [Andrey Iatsenko](https://github.com/yzen-dev/plain-to-class)  for idea and some code :)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
