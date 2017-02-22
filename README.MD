# Lighthouse Service 

[![Build Status](https://travis-ci.org/mckomo/Lighthouse-API.svg?branch=master)](https://travis-ci.org/mckomo/Lighthouse-API)
[![Code Climate](https://codeclimate.com/github/mckomo/Lighthouse-Service/badges/gpa.svg)](https://codeclimate.com/github/mckomo/Lighthouse-Service)

Lighthouse Service is the webservice that helps you find quickly right torrent. Main purpose of this project is to create backend for [Lighthouse](http://mckomo.github.io/Lighthouse/) – torrent browser.

## Public endpoints

Access to this endpoints is public, thus an authentication is not required. All endpoints respond in JSON format. Current API address is [http://light-tower.cloudapp.net/api/v1](http://light-tower.cloudapp.net/api/v1).

### GET /torrents

Search torrents with given query.

##### Parameters

|Key      | Description                 | Default | Allowed values      | Required |
|---------|-----------------------------|---------|---------------------|----------|
|q        | Query phrase                | *none*  | String              | true     |
|limit    | Number of returned torrents | 20      | Integer from 1..100 | false    |
|category | Category restriction        | all     | anime, applications, books, games, movies, music, other, tv, xxx | false |
|sort_by  | Sort by given field         | seedCount | seedCount, peerCount, uploadedAt, name, size, category (value is case sensitive)| false|

##### Example response


```
GET api.lighthouse.pm/torrents?q=ubuntu&category=applications

[
   {
      "hash":"3F19B149F53A50E14FC0B79926A391896EABAB6F",
      "name":"Ubuntu 15.10 Desktop (64-bit)",
      "filename":"ubuntu-1510-desktop-64-bit.torrent",
      "category":"Applications",
      "size":1178386432,
      "url":"http:\/\/torcache.net\/torrent\/3F19B149F53A50E14FC0B79926A391896EABAB6F.torrent",
      "magnetLink":"magnet:?xt=urn:btih:3F19B149F53A50E14FC0B79926A391896EABAB6F&dn=&tr=udp%3A%2F%2Ftracker.publicbt.com%3A80&tr=udp%3A%2F%2Ftracker.openbittorrent.com%3A80&tr=udp%3A%2F%2Ftracker.ccc.de%3A80&tr=udp%3A%2F%2Ftracker.istole.it%3A80",
      "uploadedAt":"2015-10-22T12:01:16Z",
      "seedCount":49,
      "peerCount":11
   },
   {
      "hash":"B415C913643E5FF49FE37D304BBB5E6E11AD5101",
      "name":"Ubuntu 14.10 Desktop 64bit ISO",
      "filename":"ubuntu-1410-desktop-64bit-iso.torrent",
      "category":"Applications",
      "size":1162936320,
      "url":"http:\/\/torcache.net\/torrent\/B415C913643E5FF49FE37D304BBB5E6E11AD5101.torrent",
      "magnetLink":"magnet:?xt=urn:btih:B415C913643E5FF49FE37D304BBB5E6E11AD5101&dn=&tr=udp%3A%2F%2Ftracker.publicbt.com%3A80&tr=udp%3A%2F%2Ftracker.openbittorrent.com%3A80&tr=udp%3A%2F%2Ftracker.ccc.de%3A80&tr=udp%3A%2F%2Ftracker.istole.it%3A80",
      "uploadedAt":"2014-10-23T17:33:00Z",
      "seedCount":47,
      "peerCount":4
   },
   ...
]
```

### GET /torrents/:hash

Get torrent info.

##### Parameters

|Key      | Description                 | Default | Required |
|---------|-----------------------------|---------|----------|
|hash     | Torrent hash                | *none*  | true     |

##### Example response

```
GET api.lighthouse.pm/torrents/B415C913643E5FF49FE37D304BBB5E6E11AD5101

{
  "hash":"B415C913643E5FF49FE37D304BBB5E6E11AD5101",
  "name":"Ubuntu 14.10 Desktop 64bit ISO",
  "filename":"ubuntu-1410-desktop-64bit-iso.torrent",
  "category":"Applications",
  "size":1162936320,
  "url":"http:\/\/torcache.net\/torrent\/B415C913643E5FF49FE37D304BBB5E6E11AD5101.torrent",
  "magnetLink":"magnet:?xt=urn:btih:B415C913643E5FF49FE37D304BBB5E6E11AD5101&dn=&tr=udp%3A%2F%2Ftracker.publicbt.com%3A80&tr=udp%3A%2F%2Ftracker.openbittorrent.com%3A80&tr=udp%3A%2F%2Ftracker.ccc.de%3A80&tr=udp%3A%2F%2Ftracker.istole.it%3A80",
  "uploadedAt":"2014-10-23T17:33:00Z",
  "seedCount":47,
  "peerCount":4
}
```

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

## Projects using Lighthouse-API

- [https://github.com/mckomo/Lighthouse-Web](https://github.com/mckomo/Lighthouse-Web)
- [https://github.com/mckomo/Lighthouse-Alfred](https://github.com/mckomo/Lighthouse-Alfred)
