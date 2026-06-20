<?php

class Conv
{
    private $convId;
    private $user1_id;
    private $user2_id;

    public function __construct($convID, $user1_id, $user2_id)
    {
        $this->convId = $convID;
        $this->user1_id = $user1_id;
        $this->user2_id = $user2_id;

    }
    public function getUser1_id()
    {
        return $this->user1_id;
    }
    public function getUser2_id()
    {
        return $this->user2_id;
    }
    public function setUser1_id($user)
    {
        $this->user1_id = $user;
    }
    public function setUser2_id($user)
    {
        $this->user2_id = $user;
    }
    public function toArray(): array
    {
        return [
            'id' => $this->convId,
            'user1_id' => $this->getUser1_id(),
            'user2_id' => $this->getUser2_id()
        ];
    }

    // Créer depuis BDD
    public static function fromArray(array $data): self
    {

        return new self(
            (int) $data['id'],
            $data['user1_id'] ?? '',
            $data['user2_id'] ?? '',
        );
    }
}
?>