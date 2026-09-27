<?php

namespace App\Services;

use MuxPhp\Configuration;
use MuxPhp\Api\LiveStreamsApi;
use MuxPhp\Models\CreateLiveStreamRequest;
use MuxPhp\Models\CreatePlaybackIDRequest;
use MuxPhp\Models\CreateSimulcastTargetRequest;
use GuzzleHttp\Client;

class MuxService
{
    protected $api;

    public function __construct()
    {
        $config = Configuration::getDefaultConfiguration()
            ->setUsername(config('services.mux.token_id'))
            ->setPassword(config('services.mux.token_secret'));

        $this->api = new LiveStreamsApi(new Client(), $config);
    }

    /**
     * Create a new Mux Live Stream.
     */
    public function createStream($simulcastTargets = [])
    {
        $playbackReq = new CreatePlaybackIDRequest(["policy" => "public"]);

        $streamReqData = [
            "playback_policy" => ["public"],
            "new_asset_settings" => ["playback_policy" => ["public"]],
        ];

        // Add simulcast targets if any
        if (!empty($simulcastTargets)) {
            $targets = [];
            foreach ($simulcastTargets as $target) {
                if (!empty($target['url']) && !empty($target['stream_key'])) {
                    $targets[] = new CreateSimulcastTargetRequest([
                        "passthrough" => "Simulcast",
                        "stream_key" => $target['stream_key'],
                        "url" => $target['url']
                    ]);
                }
            }
            if (count($targets) > 0) {
                $streamReqData["simulcast_targets"] = $targets;
            }
        }

        $streamReq = new CreateLiveStreamRequest($streamReqData);

        $response = $this->api->createLiveStream($streamReq);
        return $response->getData();
    }

    /**
     * Delete a Mux Live Stream.
     */
    public function deleteStream($streamId)
    {
        if (!$streamId) return false;
        
        try {
            $this->api->deleteLiveStream($streamId);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get Stream details.
     */
    public function getStream($streamId)
    {
        if (!$streamId) return null;
        
        try {
            $response = $this->api->getLiveStream($streamId);
            return $response->getData();
        } catch (\Exception $e) {
            return null;
        }
    }
}
