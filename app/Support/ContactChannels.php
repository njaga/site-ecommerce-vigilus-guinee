<?php

namespace App\Support;

class ContactChannels
{
    public function whatsappUrl(): ?string
    {
        $number = preg_replace('/[\s()+.-]/', '', (string) config('site.whatsapp'));
        if (! preg_match('/^[1-9][0-9]{7,14}$/', $number)) {
            return null;
        }

        return 'https://wa.me/'.$number.'?'.http_build_query([
            'text' => 'Bonjour Vigilus Guinée, je souhaite échanger avec vous sur mon projet.',
        ], '', '&', PHP_QUERY_RFC3986);
    }

    public function mapQuery(): string
    {
        return config('services.google_maps.query') ?: (config('site.address') ?: 'Conakry, Guinée');
    }

    public function mapUrl(): string
    {
        return 'https://www.google.com/maps/search/?'.http_build_query([
            'api' => 1, 'query' => $this->mapQuery(),
        ], '', '&', PHP_QUERY_RFC3986);
    }

    public function mapEmbedUrl(): ?string
    {
        $key = config('services.google_maps.embed_api_key');
        if (! $key) {
            return null;
        }

        return 'https://www.google.com/maps/embed/v1/place?'.http_build_query([
            'key' => $key, 'q' => $this->mapQuery(), 'language' => 'fr',
        ], '', '&', PHP_QUERY_RFC3986);
    }
}
