# Twitter
curl -X POST "https://backend.blotato.com/v2/posts" \
  -H "Content-Type: application/json" \
  -d '{
    "post": {
      "accountId": "acc_12345",
      "content": {
        "text": "Hello, world!",
        "mediaUrls": [],
        "platform": "twitter"
      },
      "target": {
        "targetType": "twitter"
      }
    }
  }'

# Twitter Thread
curl -X POST "https://backend.blotato.com/v2/posts" \
  -H "Content-Type: application/json" \
  -d '{
    "post": {
      "accountId": "acc_13579",
      "content": {
        "text": "This is the first tweet in the thread.",
        "mediaUrls": [],
        "platform": "twitter",
        "additionalPosts": [
          {
            "text": "Here'\''s the second tweet, adding more info.",
            "mediaUrls": []
          },
          {
            "text": "And here'\''s the third tweet to conclude!",
            "mediaUrls": []
          }
        ]
      },
      "target": {
        "targetType": "twitter"
      }
    }
  }'

# LinkedIn
curl -X POST "https://backend.blotato.com/v2/posts" \
  -H "Content-Type: application/json" \
  -d '{
    "post": {
      "accountId": "acc_12345",
      "content": {
        "text": "Professional update for my network",
        "mediaUrls": [],
        "platform": "linkedin"
      },
      "target": {
        "targetType": "linkedin"
      }
    }
  }'

# LinkedIn with Page ID
curl -X POST "https://backend.blotato.com/v2/posts" \
  -H "Content-Type: application/json" \
  -d '{
    "post": {
      "accountId": "acc_12345",
      "content": {
        "text": "Company update for our followers",
        "mediaUrls": [],
        "platform": "linkedin"
      },
      "target": {
        "targetType": "linkedin",
        "pageId": "123456789"
      }
    }
  }'

# Facebook
curl -X POST "https://backend.blotato.com/v2/posts" \
  -H "Content-Type: application/json" \
  -d '{
    "post": {
      "accountId": "acc_67890",
      "content": {
        "text": "Check out our latest update!",
        "mediaUrls": [],
        "platform": "facebook"
      },
      "target": {
        "targetType": "facebook",
        "pageId": "987654321"
      }
    }
  }'

# Instagram
curl -X POST "https://backend.blotato.com/v2/posts" \
  -H "Content-Type: application/json" \
  -d '{
    "post": {
      "accountId": "acc_24680",
      "content": {
        "text": "Check out this image!",
        "mediaUrls": [
          "https://example.com/image1.jpg",
          "https://example.com/image2.jpg"
        ],
        "platform": "instagram"
      },
      "target": {
        "targetType": "instagram"
      }
    }
  }'

# Pinterest
curl -X POST "https://backend.blotato.com/v2/posts" \
  -H "Content-Type: application/json" \
  -d '{
    "post": {
      "accountId": "acc_12345",
      "content": {
        "text": "Amazing DIY project idea",
        "mediaUrls": ["https://example.com/pin-image.jpg"],
        "platform": "pinterest"
      },
      "target": {
        "targetType": "pinterest",
        "boardId": "12345678"
      }
    }
  }'

# TikTok
curl -X POST "https://backend.blotato.com/v2/posts" \
  -H "Content-Type: application/json" \
  -d '{
    "post": {
      "accountId": "acc_12345",
      "content": {
        "text": "New trend alert! #trending",
        "mediaUrls": ["https://example.com/video.mp4"],
        "platform": "tiktok"
      },
      "target": {
        "targetType": "tiktok",
        "privacyLevel": "PUBLIC_TO_EVERYONE",
        "disabledComments": false,
        "disabledDuet": false,
        "disabledStitch": false,
        "isBrandedContent": false,
        "isYourBrand": false,
        "isAiGenerated": false
      }
    }
  }'

# Threads
curl -X POST "https://backend.blotato.com/v2/posts" \
  -H "Content-Type: application/json" \
  -d '{
    "post": {
      "accountId": "acc_12345",
      "content": {
        "text": "Starting a new conversation",
        "mediaUrls": [],
        "platform": "threads"
      },
      "target": {
        "targetType": "threads",
        "replyControl": "everyone"
      }
    }
  }'

# Bluesky
curl -X POST "https://backend.blotato.com/v2/posts" \
  -H "Content-Type: application/json" \
  -d '{
    "post": {
      "accountId": "acc_12345",
      "content": {
        "text": "Hello Bluesky world!",
        "mediaUrls": [],
        "platform": "bluesky"
      },
      "target": {
        "targetType": "bluesky"
      }
    }
  }'

# YouTube
curl -X POST "https://backend.blotato.com/v2/posts" \
  -H "Content-Type: application/json" \
  -d '{
    "post": {
      "accountId": "acc_12345",
      "content": {
        "text": "Video description goes here",
        "mediaUrls": ["https://example.com/video.mp4"],
        "platform": "youtube"
      },
      "target": {
        "targetType": "youtube",
        "title": "My Awesome Video",
        "privacyStatus": "public",
        "shouldNotifySubscribers": true
      }
    }
  }'

# Webhook
curl -X POST "https://backend.blotato.com/v2/posts" \
  -H "Content-Type: application/json" \
  -d '{
    "post": {
      "accountId": "acc_12345",
      "content": {
        "text": "Content for custom integration",
        "mediaUrls": [],
        "platform": "other"
      },
      "target": {
        "targetType": "webhook",
        "url": "https://mywebhook.example.com/receive"
      }
    }
  }'

# Scheduled Post Example
curl -X POST "https://backend.blotato.com/v2/posts" \
  -H "Content-Type: application/json" \
  -d '{
    "post": {
      "accountId": "acc_67890",
      "content": {
        "text": "Scheduled post example",
        "mediaUrls": [],
        "platform": "facebook"
      },
      "target": {
        "targetType": "facebook",
        "pageId": "987654321"
      }
    },
    "scheduledTime": "2025-03-10T15:30:00Z"
  }'
