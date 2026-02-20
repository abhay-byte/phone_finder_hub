<?php

namespace App\Services;

class ProfanityFilter
{
    /**
     * A basic list of profane or offensive words to filter.
     * In a production environment, this might be loaded from a configuration file or database.
     * 
     * @var array
     */
    protected array $badWords = [
        'fuck',
        'shit',
        'bitch',
        'asshole',
        'dick',
        'cunt',
        'slut',
        'whore',
        'bastard',
        'faggot',
        'nigger',
        'retard', // Adding a few common ones, feel free to expand
    ];

    /**
     * Replace profane words in the given text with asterisks.
     *
     * @param string $text
     * @return string
     */
    public function censor(string $text): string
    {
        if (empty($text)) {
            return $text;
        }

        $filteredText = $text;

        foreach ($this->badWords as $word) {
            // Use word boundary \b to avoid replacing parts of legitimate words
            // i modifier makes it case-insensitive
            $pattern = '/\b' . preg_quote($word, '/') . '\b/i';
            
            // Create a replacement string of asterisks the same length as the bad word
            $replacement = str_repeat('*', strlen($word));

            $filteredText = preg_replace($pattern, $replacement, $filteredText);
        }

        return $filteredText;
    }
}
