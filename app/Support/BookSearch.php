<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

final class BookSearch
{
    private const COLUMNS = ['title', 'author', 'sku', 'publisher', 'isbn'];

    public static function apply(Builder $query, string $term): Builder
    {
        $terms = preg_split('/\s+/u', trim($term), -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $driver = DB::connection()->getDriverName();

        foreach ($terms as $word) {
            if ($driver === 'sqlite') {

                $pattern = '*' . self::sqliteGlobCasePattern($word) . '*';
                $query->where(function (Builder $fields) use ($pattern) {
                    foreach (self::COLUMNS as $index => $column) {
                        $method = $index === 0 ? 'whereRaw' : 'orWhereRaw';
                        $fields->{$method}("{$column} GLOB ?", [$pattern]);
                    }
                });
                continue;
            }

            $like = '%' . $word . '%';
            $query->where(function (Builder $fields) use ($like) {
                foreach (self::COLUMNS as $index => $column) {
                    $method = $index === 0 ? 'where' : 'orWhere';
                    $fields->{$method}($column, 'like', $like);
                }
            });
        }

        return $query;
    }

    private static function sqliteGlobCasePattern(string $word): string
    {
        $characters = preg_split('//u', $word, -1, PREG_SPLIT_NO_EMPTY) ?: str_split($word);
        $pattern = '';

        foreach ($characters as $character) {
            $lower = function_exists('mb_strtolower') ? mb_strtolower($character, 'UTF-8') : strtolower($character);
            $upper = function_exists('mb_strtoupper') ? mb_strtoupper($character, 'UTF-8') : strtoupper($character);

            if ($lower !== $upper) {
                $pattern .= '[' . $lower . $upper . ']';
            } elseif (in_array($character, ['*', '?', '[', ']'], true)) {

                $pattern .= match ($character) {
                    '*' => '[*]',
                    '?' => '[?]',
                    '[' => '[[]',
                    ']' => '[]]',
                };
            } else {
                $pattern .= $character;
            }
        }

        return $pattern;
    }
}
