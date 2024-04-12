<?php

declare(strict_types=1);

namespace Zjk\DtoMapper\Tests\Resources\Fixtures;

use Ramsey\Uuid\Uuid;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Zjk\DtoMapper\Tests\Resources\App\Entities\Expert;
use Zjk\DtoMapper\Tests\Resources\App\Entities\Image;
use Zjk\DtoMapper\Tests\Resources\App\Entities\Media;
use Zjk\DtoMapper\Tests\Resources\App\Entities\User;

final class TestFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $this->users($manager);
        $this->experts($manager);
        $this->images($manager);
        $this->media($manager);
    }

    private function users(ObjectManager $manager): void
    {
        $users = [];

        $user = new User(id: Uuid::fromString('d6dc85b9-58e1-407b-97d4-53af658e1e90'));
        $user->setName('User 1');

        $users[] = $user;

        $user = new User(id: Uuid::fromString('87101abb-4e71-427a-a433-5ea7c253e56f'));
        $user->setName('User 2');

        $users[] = $user;

        $user = new User(id: Uuid::fromString('787e9568-0ebf-4cd1-825f-063ded2a6588'));
        $user->setName('User 3');

        $users[] = $user;

        foreach ($users as $user) {
            $manager->persist($user);
            $this->addReference($user->getIdentifier(), $user);
        }

        $manager->flush();
    }

    private function experts(ObjectManager $manager): void
    {
        $experts = [];

        $expert = new Expert(id: Uuid::fromString('c0b51430-c935-41ee-9877-af39637ab24a'));
        $expert
            ->setName('Expert 1')
            ->setTitle('Doctor 1');

        $experts[] = $expert;

        $expert = new Expert(id: Uuid::fromString('c0b51430-c935-41ee-9877-af39637ab24b'));
        $expert
            ->setName('Expert 2')
            ->setTitle('Doctor 2');

        $experts[] = $expert;

        foreach ($experts as $expert) {
            $manager->persist($expert);
            $this->addReference($expert->getIdentifier(), $expert);
        }

        $manager->flush();
    }

    public function images(ObjectManager $manager): void
    {
        $images = [];

        $image = new Image(id: Uuid::fromString('a0b51430-c935-41ee-9877-af39637ab24a'));
        $image
            ->setName('Image 1');

        $images[] = $image;

        $image = new Image(id: Uuid::fromString('a0b51430-c935-41ee-9877-af39637ab24b'));
        $image
            ->setName('Image 2');

        $images[] = $image;

        $image = new Image(id: Uuid::fromString('a0b51430-c935-41ee-9877-af39637ab24c'));
        $image
            ->setName('Image 3');

        $images[] = $image;

        $image = new Image(id: Uuid::fromString('a0b51430-c935-41ee-9877-af39637ab24d'));
        $image
            ->setName('Image 4');

        $images[] = $image;

        foreach ($images as $image) {
            $manager->persist($image);
            $this->addReference($image->getIdentifier(), $image);
        }

        $manager->flush();
    }

    private function media(ObjectManager $manager): void
    {
        $medias = [];

        // 1
        $media = new Media(Uuid::fromString('60b16643-d5e0-468a-8823-499fcf07684a'));
        $media
            ->setTitle('Title video 1')
            ->setDescription('Description video 1');

        $media->setUser($this->getUser('d6dc85b9-58e1-407b-97d4-53af658e1e90'));

        $media->addExpert($this->getExpert('c0b51430-c935-41ee-9877-af39637ab24a'));
        $media->addExpert($this->getExpert('c0b51430-c935-41ee-9877-af39637ab24b'));

        $media->addImage($this->getImage('a0b51430-c935-41ee-9877-af39637ab24a'));
        $media->addImage($this->getImage('a0b51430-c935-41ee-9877-af39637ab24b'));

        $medias[] = $media;

        // 2
        $media = new Media(Uuid::fromString('60b16643-d5e0-468a-8823-499fcf07684b'));
        $media
            ->setTitle('Title video 2')
            ->setDescription('Description video 2');

        $media->setUser($this->getUser('87101abb-4e71-427a-a433-5ea7c253e56f'));

        $media->addExpert($this->getExpert('c0b51430-c935-41ee-9877-af39637ab24a'));

        $media->addImage($this->getImage('a0b51430-c935-41ee-9877-af39637ab24c'));

        $medias[] = $media;

        // 3
        $media = new Media(Uuid::fromString('60b16643-d5e0-468a-8823-499fcf07684c'));
        $media
            ->setTitle('Title video 3')
            ->setDescription('Description video 3');

        $media->setUser($this->getUser('787e9568-0ebf-4cd1-825f-063ded2a6588'));

        $media->addExpert($this->getExpert('c0b51430-c935-41ee-9877-af39637ab24b'));

        $media->addImage($this->getImage('a0b51430-c935-41ee-9877-af39637ab24d'));

        $medias[] = $media;

        foreach ($medias as $media) {
            $manager->persist($media);
            $this->addReference($media->getIdentifier(), $media);
        }

        $manager->flush();
    }

    private function getExpert(string $id): Expert
    {
        return $this->getReference($id);
    }

    private function getImage(string $id): Image
    {
        return $this->getReference($id);
    }

    private function getUser(string $id): User
    {
        return $this->getReference($id);
    }
}
