<?php


namespace Allanvb\LaravelSemysms\Channels;


class SemySmsMessage
{
    public $to;

    public $text;

    public function to(string $phone)
    {
        $this->to = $phone;

        return $this;
    }

    public function text(string $message)
    {
        $this->text = $message;

        return $this;
    }
}
