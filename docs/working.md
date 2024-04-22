# Working with the mapper

To work with the mapper, it is necessary to inject the `Zjk\DtoMapper\Contract\MapperInterface` service in the constructor. The mapper has four methods depending on what data mapping you are doing.
- `fromObjectEntityToDto(object $entity, object|string $dto): object`
- `fromObjectDtoToEntity(object $dto, object|string $entity): object`
- `fromCollectionEntityToDto(iterable $collections, object|string $target): array`
- `fromCollectionDtoToEntity(iterable $collections, object|string $target): array`

Example:
```php
    use Zjk\DtoMapper\Contract\MapperInterface;
    use Doctrine\ORM\EntityManagerInterface;
    
    class MyController{
        private MapperInterface $mapper;
        
        private EntityManagerInterface $entityManager;
    
        public function __construct(EntityManagerInterface $entityManager, MapperInterface $mapper, ...)
        {
            ...
            $this->mapper = $mapper;
            $this->entityManager = $entityManager;
            ...
        }
        
        public function dtoToEntity(
           #[MapRequestPayload] PostDto $postDto
        ): void
        {
           ...
           /** @var Post $post */
           $post = $this->mapper->fromObjectDtoToEntity($postDto, Post::class);
           $this->entityManager->flush();
           ....        
        }
        
        public function entityToDto(Post $post): PostDto
        {
           ...
           $postDto = $this->mapper->fromObjectEntityToDto($post, PostDto::class);
           
           return $postDto        
        }
        
        /**
        * @return PostDto[]
        */
        public function collectionEntityToDto(): array {
            
            $allPost = $this->doctrine->getRepository(Post::class)->findAll();

            return $this->mapper->fromCollectionEntityToDto($allPost, PostDto::class);
        }
    }
```