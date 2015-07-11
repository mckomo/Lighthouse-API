# Lighthouse Service 

[![Build Status](https://travis-ci.org/mckomo/Lighthouse-Service.svg)](https://travis-ci.org/mckomo/Lighthouse-Service)

Lighthouse Service is the webservice that helps you find quickly right torrent. Main purpose of this project is to create backend for [Lighthouse](http://lighthouse.github.io) – torrent browser.

## Public endpoints

Access to this endpoints is public, thus an authentication is not required. All endpoints repond in JSON format. 

### GET /api/v1/torrents

Search torrents with given query.

##### Parameters

|Key      | Description                 | Default | Allowed values      | Required |
|---------|-----------------------------|---------|---------------------|----------|
|q        | Query phrase                | *none*  | String              | true     |
|size     | Number of returned torrents | 20      | Integer from 1..100 | false    |
|category | Category restriction        | all     | anime, applications, books, games, movies, music, other, tv, xxx | false |

##### Example response


```
GET my-domain.com/api/v1/torrents?q=ubuntu&category=applications

[  
   {  
      "hash":"FC8A15A2FAF2734DBB1DC5F7AFDC5C9BEAEB1F59",
      "name":"Ubuntu 15.04 Vivid Vervet Desktop amd64 ISO FINAL",
      "category":"Applications",
      "size":1150844928,
      "url":"http:\/\/my-domain.com\/api\/v1\/torrents\/FC8A15A2FAF2734DBB1DC5F7AFDC5C9BEAEB1F59\/file",
      "uploadedAt":"2015-04-23T15:01:23Z",
      "seedCount":883,
      "peerCount":58
   },
   ...
   {  
      "hash":"C04161A538159899814F2A1AE9BDA6A3A7574E26",
      "name":"ubuntu gnome 14 04 2 desktop amd64 iso",
      "category":"Applications",
      "size":993001472,
      "url":"http:\/\/my-domain.com\/api\/v1\/torrents\/C04161A538159899814F2A1AE9BDA6A3A7574E26\/file",
      "uploadedAt":"2015-05-05T22:13:08Z",
      "seedCount":63,
      "peerCount":1
   }
]
```

### GET /api/v1/torrents/:hash

Get torrent info.

##### Parameters

|Key      | Description                 | Default | Required |
|---------|-----------------------------|---------|----------|
|hash     | Torrent hash                | *none*  | true     |

##### Example response

```
GET my-domain.com/api/v1/torrents/FC8A15A2FAF2734DBB1DC5F7AFDC5C9BEAEB1F59

{  
   "hash":"FC8A15A2FAF2734DBB1DC5F7AFDC5C9BEAEB1F59",
   "name":"Ubuntu 15.04 Vivid Vervet Desktop amd64 ISO FINAL",
   "category":"Applications",
   "size":1150844928,
   "url":"http:\/\/my-domain.com\/api\/v1\/torrents\/FC8A15A2FAF2734DBB1DC5F7AFDC5C9BEAEB1F59\/file",
   "uploadedAt":"2015-04-23T15:01:23Z",
   "seedCount":883,
   "peerCount":58
}
```


### GET /api/v1/torrents/:hash/file

Download torrent file.

##### Parameters

|Key      | Description                 | Default | Required |
|---------|-----------------------------|---------|----------|
|hash     | Torrent hash                | *none*  | true     |

### Errors

For all statuses 4xx and 5xx you should expect error message with folowing structre.

```
{
   "error":{
      "message":"Validation error has occurred",
      "attachments":[
         "Phrase must have at least 3 characters"
      ] # Attachments field can be null
   }
}
```
