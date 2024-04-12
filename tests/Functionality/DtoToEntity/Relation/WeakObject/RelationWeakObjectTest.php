<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\Tests\Functionality\DtoToEntity\Relation\WeakObject;

use Doctrine\ORM\EntityManager;
use Zjk\DtoMapper\Contract\MapperInterface;
use Zjk\DtoMapper\Tests\Functionality\DtoToEntity\Relation\WeakObject\Dto\ImageDto;
use Zjk\DtoMapper\Tests\Functionality\DtoToEntity\Relation\WeakObject\Dto\MediaDto;
use Zjk\DtoMapper\Tests\Resources\App\Entities\Image;
use Zjk\DtoMapper\Tests\Resources\App\Entities\Media;
use Zjk\DtoMapper\Tests\Resources\KernelTestCase;

final class RelationWeakObjectTest extends KernelTestCase
{
    private EntityManager $entityManager;

    private MapperInterface $mapper;

    protected function setUp(): void
    {
        parent::setUp();

        /* @phpstan-ignore-next-line */
        $this->entityManager = $this->getContainer()->get('doctrine.orm.entity_manager');

        /* @phpstan-ignore-next-line */
        $this->mapper = $this->getContainer()->get(MapperInterface::class);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        unset(
            $this->entityManager,
            $this->mapper,
        );
    }

    public function testAddNewEntityToRelation(): void
    {
        $mediaId = '60b16643-d5e0-468a-8823-499fcf07684b';

        /** @var Media $media */
        $media = $this->entityManager->find(Media::class, $mediaId);

        /**
         * @phpstan-ignore-next-line
         */
        $initialStateImageIds = \array_map(static function (Image $expert): string {
            return $expert->getIdentifier();
        }, $media->getImage()->toArray());

        $this->assertEquals(
            ['a0b51430-c935-41ee-9877-af39637ab24c'],
            $initialStateImageIds
        );

        $initialImageDto = new ImageDto(
            id: 'a0b51430-c935-41ee-9877-af39637ab24c',
            name: 'Image 3'
        );

        $addImageDot = new ImageDto(
            name: 'Image add'
        );

        $mediaDto = new MediaDto(
            id: $mediaId,
            image: [
                $initialImageDto,
                $addImageDot,
            ],
        );

        $this->mapper->fromObjectDtoToEntity($mediaDto, Media::class);
        $this->entityManager->flush();
        $this->entityManager->clear();

        /** @var Media $media */
        $media = $this->entityManager->find(Media::class, $mediaId);

        /**
         * @phpstan-ignore-next-line
         */
        $imageName = \array_map(static function (Image $image): string {
            return $image->getName();
        }, $media->getImage()->toArray());

        // The entity has been added from the relation
        $this->assertSame([], \array_diff($imageName, ['Image add', 'Image 3']));

        // The entity has been added from the table
        $image = $this->entityManager->find(Image::class, 'a0b51430-c935-41ee-9877-af39637ab24b');
        $this->assertInstanceOf(Image::class, $image);
    }

    public function testRemoveEntityFromRelation(): void
    {
        $mediaId = '60b16643-d5e0-468a-8823-499fcf07684a';

        /** @var Media $media */
        $media = $this->entityManager->find(Media::class, $mediaId);

        /**
         * @phpstan-ignore-next-line
         */
        $initialStateImageIds = \array_map(static function (Image $expert): string {
            return $expert->getIdentifier();
        }, $media->getImage()->toArray());

        $this->assertEquals(
            [
                'a0b51430-c935-41ee-9877-af39637ab24a',
                'a0b51430-c935-41ee-9877-af39637ab24b',
            ],
            $initialStateImageIds
        );

        // Remove image ID a0b51430-c935-41ee-9877-af39637ab24b
        $mediaDto = new MediaDto(
            id: $mediaId,
            image: [
                new ImageDto(
                    id: 'a0b51430-c935-41ee-9877-af39637ab24a',
                    name: 'Image 1'
                ),
            ],
        );

        $this->mapper->fromObjectDtoToEntity($mediaDto, Media::class);
        $this->entityManager->flush();
        $this->entityManager->clear();

        /** @var Media $media */
        $media = $this->entityManager->find(Media::class, $mediaId);

        /** @var Image[] $images */
        $images = $media->getImage()->toArray();

        $this->assertCount(1, $images);
        $this->assertSame('Image 1', $images[0]->getName());
        $this->assertSame('a0b51430-c935-41ee-9877-af39637ab24a', $images[0]->getIdentifier());

        // The entity has been removed from the table
        $image = $this->entityManager->find(Image::class, 'a0b51430-c935-41ee-9877-af39637ab24b');
        $this->assertNull($image);
    }

    public function testEditEntityToRelation(): void
    {
        $mediaId = '60b16643-d5e0-468a-8823-499fcf07684b';

        /** @var Media $media */
        $media = $this->entityManager->find(Media::class, $mediaId);

        /** @var Image[] $images */
        $images = $media->getImage()->toArray();

        $this->assertCount(1, $images);
        $this->assertSame('Image 3', $images[0]->getName());
        $this->assertSame('a0b51430-c935-41ee-9877-af39637ab24c', $images[0]->getIdentifier());

        $mediaDto = new MediaDto(
            id: $mediaId,
            image: [
                new ImageDto(
                    id: 'a0b51430-c935-41ee-9877-af39637ab24c',
                    name: 'Image 3 update'
                ),
            ],
        );

        $this->mapper->fromObjectDtoToEntity($mediaDto, Media::class);
        $this->entityManager->flush();
        $this->entityManager->clear();

        /** @var Media $media */
        $media = $this->entityManager->find(Media::class, $mediaId);

        /** @var Image[] $images */
        $images = $media->getImage()->toArray();

        $this->assertCount(1, $images);
        $this->assertSame('Image 3 update', $images[0]->getName());
        $this->assertSame('a0b51430-c935-41ee-9877-af39637ab24c', $images[0]->getIdentifier());
    }
}
