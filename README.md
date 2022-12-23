# Laravel Data transfer objects helper

[![Latest Version on Packagist](https://img.shields.io/packagist/v/bpartner/dto.svg?style=flat-square)]
(https://packagist.org/packages/bpartner/dto)
[![Total Downloads](https://img.shields.io/packagist/dt/bpartner/dto.svg?style=flat-square)](https://packagist.
org/packages/bpartner/dto)


Simple create DTO from any array.

## Installation

####Requirements:
- PHP 8.0
- Laravel 8 | 9

You can install the package via composer:

```bash
composer require bpartner/dto
```

## Usage
Declare you DTO object. You can use any types declarations.

For array or collection you can use PHPDoc annotation with type of data inside. Supported only array and collection.

```php
class DemoDto extends DtoAbstract
{
    //Optional
    protected const CLASS_FORM_REQUEST = UpdateUserFormRequest::class;
    
    /**
    * DTO fields
    */
    public string $name;
    public Carbon $date;
    public DtoOtherObject $otherObject;
    
    /** @var collection<\App\Dto\DtoOtherObject>  */
    public Collection $objectsCollection;
    
    /** @var array<\App\Dto\DtoOtherObject>  */
    public array $objectsArray;
    
    /** @var collection<\App\Dto\DtoOtherObject>  */
    public array $objectsArrayOfCollection;
    }
```

Create DTO from any array data (example: request()->all()) by facade Dto

```php
//In any place of your code
$data = request()->all(); //array data
$dto = Dto::build(DemoDto::class, $data);
```
or from FormRequest

```php
/**
 * In controller
 * 
 * Create FormRequest and assign to CLASS_FORM_REQUEST const in DTO
 * 
 */ 
public function store(DemoDto $dto)
{
    //Use $dto made from UpdateUserFormRequest
}
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

### Custom mapping

You can create DTO with custom mapping. Add static method withMap()

```php
public static function withMap(array $data): DtoInterface
{
    $mappedData = [
        'dto_param' => $data['some_param'],
    ];
    
    return new static($mappedData);
}

//Client code
$dto = Dto::build(DemoDto::class, request()->all());
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
Important! Collections and nested arrays are not flip to flat array.

## Credits

- [Alexander Zinchenko](https://github.com/bpartner)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
