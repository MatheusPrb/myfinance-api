<?php

namespace App\Application\Presenters;

use App\Models\ApplicationLog;
use App\Models\User;

final class AdminLogPresenter
{
    private const PROPERTIES_JSON_MAX = 8000;

    /**
     * @param  array<string, mixed>|null  $context
     */
    public static function extractUserId(?array $context): ?string
    {
        if ($context === null || $context === []) {
            return null;
        }

        $id = $context['userId'] ?? null;

        return is_string($id) || is_int($id) ? (string) $id : null;
    }

    public static function toArray(ApplicationLog $log, ?User $user): array
    {
        $context = $log->context;
        $extra = $log->extra;
        $userId = self::extractUserId(is_array($context) ? $context : null);

        return [
            'id' => $log->id,
            'user_id' => $userId,
            'user_name' => $user?->name,
            'action' => $log->level.':'.$log->channel,
            'subject_type' => self::stringOrNull(is_array($context) ? ($context['subject_type'] ?? null) : null),
            'subject_id' => self::stringOrNull(is_array($context) ? ($context['subject_id'] ?? null) : null),
            'description' => $log->message,
            'properties' => self::buildProperties($context, $extra),
            'ip_address' => self::stringOrNull(
                is_array($extra) ? ($extra['ip'] ?? $extra['ip_address'] ?? null) : null,
            ),
            'user_agent' => self::stringOrNull(
                is_array($extra) ? ($extra['user_agent'] ?? $extra['User-Agent'] ?? null) : null,
            ),
            'created_at' => $log->created_at?->format(DATE_ATOM),
        ];
    }

    private static function stringOrNull(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        if (is_string($value) || is_numeric($value)) {
            return (string) $value;
        }

        return null;
    }

    /**
     * @param  array<string, mixed>|null  $context
     * @param  array<string, mixed>|null  $extra
     * @return array<string, mixed>|null
     */
    private static function buildProperties(?array $context, ?array $extra): ?array
    {
        $merged = [];
        if (is_array($context) && $context !== []) {
            $merged['context'] = self::sanitizeArray($context);
        }
        if (is_array($extra) && $extra !== []) {
            $merged['extra'] = self::sanitizeArray($extra);
        }

        if ($merged === []) {
            return null;
        }

        $json = json_encode($merged, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
        if (strlen($json) > self::PROPERTIES_JSON_MAX) {
            return [
                '_truncated' => true,
                '_preview' => mb_substr($json, 0, self::PROPERTIES_JSON_MAX).'…',
            ];
        }

        return $merged;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private static function sanitizeArray(array $data): array
    {
        $out = [];
        foreach ($data as $key => $value) {
            if (! is_string($key)) {
                continue;
            }
            if (self::isSensitiveKey($key)) {
                $out[$key] = '[redacted]';

                continue;
            }
            if ($key === 'exception') {
                $out[$key] = self::exceptionSummary($value);

                continue;
            }
            if (is_array($value)) {
                $out[$key] = self::sanitizeArray($value);
            } elseif (is_scalar($value) || $value === null) {
                $out[$key] = $value;
            } else {
                $out[$key] = '[non-scalar]';
            }
        }

        return $out;
    }

    private static function isSensitiveKey(string $key): bool
    {
        return (bool) preg_match(
            '/password|passwd|token|secret|authorization|cookie|bearer|api_key|apikey|credit_card/i',
            $key,
        );
    }

    private static function exceptionSummary(mixed $value): mixed
    {
        if ($value instanceof \Throwable) {
            return [
                'class' => $value::class,
                'message' => $value->getMessage(),
            ];
        }
        if (is_array($value)) {
            return [
                'class' => $value['class'] ?? $value['type'] ?? 'unknown',
                'message' => $value['message'] ?? null,
            ];
        }
        if (is_string($value)) {
            return ['message' => mb_substr($value, 0, 500)];
        }

        return '[omitted]';
    }
}
