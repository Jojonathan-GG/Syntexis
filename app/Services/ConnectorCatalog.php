<?php

namespace App\Services;

/**
 * Static catalog of connector types supported by the MVP.
 */
class ConnectorCatalog
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public static function all(): array
    {
        return [
            [
                'type' => 'rest_api',
                'label' => 'REST / JSON API',
                'category' => 'API',
                'icon' => 'api',
                'description' => 'Pull JSON data from any HTTP endpoint with optional token auth.',
                'fields' => [
                    ['key' => 'endpoint', 'label' => 'Endpoint URL', 'placeholder' => 'https://api.example.com/v1/orders', 'type' => 'url'],
                    ['key' => 'token', 'label' => 'Bearer token', 'placeholder' => 'Optional', 'type' => 'password'],
                ],
            ],
            [
                'type' => 'postgresql',
                'label' => 'PostgreSQL',
                'category' => 'Database',
                'icon' => 'database',
                'description' => 'Read tables and views from a PostgreSQL database.',
                'fields' => [
                    ['key' => 'host', 'label' => 'Host', 'placeholder' => 'db.internal', 'type' => 'text'],
                    ['key' => 'database', 'label' => 'Database', 'placeholder' => 'production', 'type' => 'text'],
                    ['key' => 'username', 'label' => 'Username', 'placeholder' => 'syntexis_ro', 'type' => 'text'],
                ],
            ],
            [
                'type' => 'mysql',
                'label' => 'MySQL',
                'category' => 'Database',
                'icon' => 'database',
                'description' => 'Read tables and views from a MySQL database.',
                'fields' => [
                    ['key' => 'host', 'label' => 'Host', 'placeholder' => 'db.internal', 'type' => 'text'],
                    ['key' => 'database', 'label' => 'Database', 'placeholder' => 'production', 'type' => 'text'],
                ],
            ],
            [
                'type' => 'csv_upload',
                'label' => 'CSV / Excel Upload',
                'category' => 'File',
                'icon' => 'file',
                'description' => 'Upload flat files and infer their schema automatically.',
                'fields' => [
                    ['key' => 'path', 'label' => 'Storage path', 'placeholder' => 'uploads/orders.csv', 'type' => 'text'],
                ],
            ],
            [
                'type' => 'sftp',
                'label' => 'SFTP',
                'category' => 'File',
                'icon' => 'folder',
                'description' => 'Pick up files from a secure FTP server on a schedule.',
                'fields' => [
                    ['key' => 'host', 'label' => 'Host', 'placeholder' => 'sftp.partner.com', 'type' => 'text'],
                    ['key' => 'path', 'label' => 'Remote path', 'placeholder' => '/incoming/', 'type' => 'text'],
                ],
            ],
            [
                'type' => 'webhook',
                'label' => 'Webhook',
                'category' => 'Event',
                'icon' => 'bolt',
                'description' => 'Receive signed events pushed from external systems.',
                'fields' => [
                    ['key' => 'secret', 'label' => 'Signing secret', 'placeholder' => 'Optional', 'type' => 'password'],
                ],
            ],
            [
                'type' => 'salesforce',
                'label' => 'Salesforce',
                'category' => 'SaaS',
                'icon' => 'cloud',
                'description' => 'Sync accounts, opportunities and custom objects.',
                'fields' => [
                    ['key' => 'instance', 'label' => 'Instance URL', 'placeholder' => 'https://example.my.salesforce.com', 'type' => 'url'],
                ],
            ],
            [
                'type' => 'stripe',
                'label' => 'Stripe',
                'category' => 'SaaS',
                'icon' => 'card',
                'description' => 'Ingest charges, invoices and subscription events.',
                'fields' => [
                    ['key' => 'api_key', 'label' => 'Secret key', 'placeholder' => 'sk_live_...', 'type' => 'password'],
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    public static function find(string $type): ?array
    {
        foreach (self::all() as $connector) {
            if ($connector['type'] === $type) {
                return $connector;
            }
        }

        return null;
    }

    /**
     * @return array<int, string>
     */
    public static function types(): array
    {
        return array_column(self::all(), 'type');
    }
}
