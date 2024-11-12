<?php

namespace YusufTogtay\Generator\Events;

use Illuminate\Queue\SerializesModels;

class GeneratorFileDeleting
{
    use SerializesModels;

    public string $type;

    public array $data;

    public function __construct(string $type, array $data)
    {
        $this->type = $type;
        $this->data = $data;
    }
}
