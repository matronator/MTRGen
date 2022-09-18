<?php

declare(strict_types=1);

namespace Matronator\Mtrgen\Registry;

use Matronator\Mtrgen\Store\Path;
use Matronator\Mtrgen\Store\Storage;

class Profile
{
    public string $profile;

    public function __construct()
    {
        $storage = new Storage;
        $this->profile = Path::canonicalize($storage->homeDir . '/profile.json');

        if (!file_exists($this->profile))
            $this->createProfile();
    }

    public function createProfile()
    {
        $profile = (object) [
            'username' => '',
            'token' => '',
        ];

        return $this->saveProfile($profile);
    }

    public function saveProfile(object $profile): mixed
    {
        return file_put_contents(Path::safe($this->profile), json_encode($profile));
    }

    public function loadProfile(): object
    {
        if (!file_exists($this->profile))
            $this->createProfile();

        return json_decode(file_get_contents(Path::safe($this->profile)));
    }

    public function writeToProfile(string $username, string $token)
    {
        $profile = $this->loadProfile();

        $profile->username = strtolower($username);
        $profile->token = $token;

        return $this->saveProfile($profile);
    }

    public function authenticate(): bool
    {
        $profile = $this->loadProfile();
        if (!isset($profile->username) || $profile->username === '' || !isset($profile->token) || $profile->token === '')
            return false;

        return true;
    }
}
