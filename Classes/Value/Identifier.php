<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Value;

final class Identifier
{
    private string $identifier;

    public function __construct(string $identifier)
    {
        if (preg_match('/\s/', $identifier)) {
            throw new InvalidIdentifierException('Spaces are not allowed within an identifier string! "' . $identifier . '"');
        }
        $this->identifier = $identifier;
    }

    /**
     * @param string $title
     * @return Identifier
     * @throws InvalidIdentifierException
     */
    public static function buildNewFromTitle(string $title): Identifier
    {
        $originalTitle = $title;
        $title = str_replace("ä", "ae", $title);
        $title = str_replace("ü", "ue", $title);
        $title = str_replace("ö", "oe", $title);
        $title = str_replace("Ä", "Ae", $title);
        $title = str_replace("Ü", "Ue", $title);
        $title = str_replace("Ö", "Oe", $title);
        $title = str_replace("ß", "ss", $title);
        $title = strtolower($title);
        $title = preg_replace('/\s/', '_', $title);

        if (is_string($title)) {
            $title = preg_replace('/[^a-z0-9_-]/', '', $title);
        }

        if (!is_string($title)) {
            throw new InvalidIdentifierException('Something went wrong with creating an identifier from title "' . $originalTitle . '"!');
        }

        return new self($title);
    }

    public function __toString(): string
    {
        return $this->identifier;
    }
}
