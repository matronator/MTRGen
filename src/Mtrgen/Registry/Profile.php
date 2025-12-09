<?php

declare(strict_types=1);

namespace Matronator\Mtrgen\Registry;

use Matronator\Mtrgen\Store\Path;
use Matronator\Mtrgen\Store\Storage;

class Profile
{
    protected string $profilePath;
    private Storage $storage;

    public string $username;
    public string $token;

    public function __construct()
    {
        $this->storage = new Storage;
        $this->profilePath = Path::canonicalize($this->storage->homeDir . DIRECTORY_SEPARATOR . 'profile.json');

        if (!file_exists($this->profilePath))
            $this->emptyProfile();
    }

    public function emptyProfile()
    {
        $this->username = '';
        $this->token = '';

        return $this->saveProfile();
    }

    public function saveProfile(): mixed
    {
        return file_put_contents(Path::safe($this->profilePath), json_encode((object) [
            'username' => $this->username,
            'token' => $this->token,
        ]));
    }

    public function loadProfile(): object
    {
        if (!file_exists($this->profilePath))
            $this->emptyProfile();

        $data = json_decode(file_get_contents(Path::safe($this->profilePath)));

        $this->username = $data->username;
        $this->token = $data->token;

        return $data;
    }

    public function writeToProfile(string $username, string $token)
    {
        $this->username = $username;
        $this->token = $token;

        return $this->saveProfile();
    }

    public function validate(): bool
    {
        $this->loadProfile();
        if (!isset($this->username) || $this->username === '' || !isset($this->token) || $this->token === '')
            return false;

        return true;
    }

    // Magic methods

    public function __toString()
    {
        return json_encode([
            'username' => $this->username,
            'access_token' => $this->token,
        ]);
    }

    public function __debugInfo()
    {
        return [
            'username' => $this->username,
            'access_token' => $this->token,
        ];
    }

    public function __serialize(): array
    {
        return [
            'username' => $this->username,
            'access_token' => $this->token,
        ];
    }

    public function __unserialize(array $data): void
    {
        $this->username = $data['username'];
        $this->token = $data['access_token'];

        $this->storage = new Storage;
        $this->profilePath = Path::canonicalize($this->storage->homeDir . DIRECTORY_SEPARATOR . 'profile.json');
        if (!file_exists($this->profilePath))
            $this->emptyProfile();
    }
}
