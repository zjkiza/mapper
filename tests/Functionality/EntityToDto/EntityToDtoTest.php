<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\Tests\Functionality\EntityToDto;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectRepository;
use Zjk\DtoMapper\Contract\MapperInterface;
use Zjk\DtoMapper\Tests\Functionality\EntityToDto\Dto\ExpertResponseDto;
use Zjk\DtoMapper\Tests\Functionality\EntityToDto\Dto\ImageResponseDto;
use Zjk\DtoMapper\Tests\Functionality\EntityToDto\Dto\MediaResponseDto;
use Zjk\DtoMapper\Tests\Functionality\EntityToDto\Dto\MediaResponseGetAndUuidDto;
use Zjk\DtoMapper\Tests\Functionality\EntityToDto\Dto\UserResponseDto;
use Zjk\DtoMapper\Tests\Resources\App\Entities\Media;
use Zjk\DtoMapper\Tests\Resources\KernelTestCase;

final class EntityToDtoTest extends KernelTestCase
{
    private ManagerRegistry $doctrine;

    /**
     * @var ObjectRepository<Media>
     */
    private ObjectRepository $mediaRepository;

    private MapperInterface $mapper;

    protected function setUp(): void
    {
        parent::setUp();

        /* @phpstan-ignore-next-line */
        $this->doctrine = $this->getContainer()->get(ManagerRegistry::class);
        /* @phpstan-ignore-next-line */
        $this->mediaRepository = $this->doctrine->getRepository(Media::class);
        /* @phpstan-ignore-next-line */
        $this->mapper = $this->getContainer()->get(MapperInterface::class);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        unset(
            $this->doctrine,
            $this->mediaRepository,
            $this->mapper,
        );
    }

    public function testObjectEntityToDtoWithRelationTransformerAndGetIdentifier(): void
    {
        /** @var Media $media */
        $media = $this->mediaRepository->find('60b16643-d5e0-468a-8823-499fcf07684a');

        $responseDto = $this->mapper->fromObjectEntityToDto($media, MediaResponseDto::class);

        $this->assertEquals($this->mediaA(), $responseDto);
    }

    public function testObjectEntityToDtoWithUuidTransformerGetPropertyAndIgnore(): void
    {
        /** @var Media $media */
        $media = $this->mediaRepository->find('60b16643-d5e0-468a-8823-499fcf07684a');

        $responseDto = $this->mapper->fromObjectEntityToDto($media, MediaResponseGetAndUuidDto::class);

        $this->assertEquals($this->getMediaResponseGetAndUuidDto(), $responseDto);
    }

    public function testFromCollectionEntityToDtoWithTransformer(): void
    {
        /** @var Media[] $media */
        $media = $this->mediaRepository->findAll();

        $responseDto = $this->mapper->fromCollectionEntityToDto($media, MediaResponseDto::class);

        $this->assertEquals(
            [
                $this->mediaA(),
                $this->mediaB(),
                $this->mediaC(),
            ],
            $responseDto
        );
    }

    public function getMediaResponseGetAndUuidDto(): MediaResponseGetAndUuidDto
    {
        $media = new MediaResponseGetAndUuidDto();
        $media->id = '60b16643-d5e0-468a-8823-499fcf07684a';
        $media->text = 'Description video 1';
        $media->title = 'bar';

        return $media;
    }

    public function mediaA(): MediaResponseDto
    {
        $media = new MediaResponseDto();
        $media->id = '60b16643-d5e0-468a-8823-499fcf07684a';
        $media->title = 'TITLE VIDEO 1';
        $media->description = 'Description video 1';
        $media->user = $this->getUser1();
        $media->image = [
            $this->imageA(),
            $this->imageB(),
        ];
        $media->expert = [
            $this->expertA(),
            $this->expertB(),
        ];

        return $media;
    }

    public function mediaB(): MediaResponseDto
    {
        $media = new MediaResponseDto();
        $media->id = '60b16643-d5e0-468a-8823-499fcf07684b';
        $media->title = 'TITLE VIDEO 2';
        $media->description = 'Description video 2';
        $media->user = $this->getUser2();
        $media->image = [
            $this->imageC(),
        ];
        $media->expert = [
            $this->expertA(),
        ];

        return $media;
    }

    public function mediaC(): MediaResponseDto
    {
        $media = new MediaResponseDto();
        $media->id = '60b16643-d5e0-468a-8823-499fcf07684c';
        $media->title = 'TITLE VIDEO 3';
        $media->description = 'Description video 3';
        $media->user = $this->getUser3();
        $media->image = [
            $this->imageD(),
        ];
        $media->expert = [
            $this->expertB(),
        ];

        return $media;
    }

    public function imageA(): ImageResponseDto
    {
        $image = new ImageResponseDto();
        $image->id = 'a0b51430-c935-41ee-9877-af39637ab24a';
        $image->name = 'IMAGE 1';

        return $image;
    }

    public function imageB(): ImageResponseDto
    {
        $image = new ImageResponseDto();
        $image->id = 'a0b51430-c935-41ee-9877-af39637ab24b';
        $image->name = 'IMAGE 2';

        return $image;
    }

    public function imageC(): ImageResponseDto
    {
        $image = new ImageResponseDto();
        $image->id = 'a0b51430-c935-41ee-9877-af39637ab24c';
        $image->name = 'IMAGE 3';

        return $image;
    }

    public function imageD(): ImageResponseDto
    {
        $image = new ImageResponseDto();
        $image->id = 'a0b51430-c935-41ee-9877-af39637ab24d';
        $image->name = 'IMAGE 4';

        return $image;
    }

    public function expertA(): ExpertResponseDto
    {
        $image = new ExpertResponseDto();
        $image->id = 'c0b51430-c935-41ee-9877-af39637ab24a';
        $image->title = 'Doctor 1';
        $image->name = 'EXPERT 1';

        return $image;
    }

    public function expertB(): ExpertResponseDto
    {
        $image = new ExpertResponseDto();
        $image->id = 'c0b51430-c935-41ee-9877-af39637ab24b';
        $image->title = 'Doctor 2';
        $image->name = 'EXPERT 2';

        return $image;
    }

    public function getUser1(): UserResponseDto
    {
        $user = new UserResponseDto();
        $user->id = 'd6dc85b9-58e1-407b-97d4-53af658e1e90';
        $user->name = 'User 1';

        return $user;
    }

    public function getUser2(): UserResponseDto
    {
        $user = new UserResponseDto();
        $user->id = '87101abb-4e71-427a-a433-5ea7c253e56f';
        $user->name = 'User 2';

        return $user;
    }

    public function getUser3(): UserResponseDto
    {
        $user = new UserResponseDto();
        $user->id = '787e9568-0ebf-4cd1-825f-063ded2a6588';
        $user->name = 'User 3';

        return $user;
    }
}
