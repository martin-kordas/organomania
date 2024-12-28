<?php

namespace App\Services;

use GuzzleHttp\Client;
use Google\Service\YouTube;
use Google\Service\YouTube\PlaylistItemSnippet;
use Exception;

class YoutubeService
{
    
    private Youtube $youtube;
    
    public function __construct()
    {
        $httpClient = new Client([
            'headers' => [
                'referer' => 'organomania.cz'
            ]
        ]);
        $client = new \Google\Client();
        $client->setHttpClient($httpClient);
        $apiKey = env('GOOGLE_API_KEY');
        $client->setDeveloperKey($apiKey);
        
        $this->youtube = new YouTube($client);
    }

    public function getChannelAvatarUrl(string $channelId): string
    {
        // Fetch channel details
        $response = $this->youtube->channels->listChannels('snippet', ['id' => $channelId]);

        if (count($response->getItems()) > 0) {
            // Get the channel snippet
            $snippet = $response->getItems()[0]->getSnippet();

            // Get the avatar URLs
            $thumbnails = $snippet->getThumbnails();
            $highResAvatar = $thumbnails->getDefault()->getUrl();

            return $highResAvatar; // You can also return other sizes if needed
        }
        else {
            throw new Exception("Channel not found");
        }
    }
    
    function getChannelSubscriberCount(string $channelId): int
    {
        // Fetch channel statistics
        $response = $this->youtube->channels->listChannels('statistics', ['id' => $channelId]);

        if (count($response->getItems()) > 0) {
            // Get the channel statistics
            $statistics = $response->getItems()[0]->getStatistics();
            $subscriberCount = $statistics->getSubscriberCount();

            return (int)$subscriberCount;
        }
        else {
            throw new Exception("Channel not found");
        }
    }
    
    function getChannelLastVideo(string $channelId): ?PlaylistItemSnippet
    {
        // Step 1: Get the channel details to find the uploads playlist ID
        $channelResponse = $this->youtube->channels->listChannels('contentDetails', ['id' => $channelId]);

        if (count($channelResponse->getItems()) > 0) {
            $uploadsPlaylistId = $channelResponse->getItems()[0]->getContentDetails()->getRelatedPlaylists()->getUploads();

            // Step 2: Get the latest video from the uploads playlist
            $playlistResponse = $this->youtube->playlistItems->listPlaylistItems('snippet', [
                'playlistId' => $uploadsPlaylistId,
                'maxResults' => 1 // Fetch only the latest video
            ]);

            if (count($playlistResponse->getItems()) > 0) {
                $latestVideo = $playlistResponse->getItems()[0]->getSnippet();
                return $latestVideo;
            }
            else return null;
        }
        else {
            throw new Exception("Channel not found.");
        }
    }

    function getChannelVideoCount(string $channelId): int
    {
        // Fetch channel statistics
        $response = $this->youtube->channels->listChannels('statistics', ['id' => $channelId]);

        if (count($response->getItems()) > 0) {
            // Get the channel statistics
            $statistics = $response->getItems()[0]->getStatistics();
            $videoCount = $statistics->getVideoCount();

            return (int)$videoCount; // Return the total video count
        } else {
            throw new Exception("Channel not found.");
        }
    }
    
}
