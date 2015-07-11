<?php namespace Lighthouse\Services\Torrents\Common;


final class ErrorMessages
{
    const TorrentNotFound = 'Torrent was not found';

    const ValidationError = 'Validation error has occurred';
    const EmptyResource = 'Resource is empty or null';

    const InvalidHash = 'Hash is invalid';
    const EmptyName = 'Name cannot be empty.';
    const EmptyCategory = 'Category cannot be empty';
    const NonpositiveSize = 'Size must be a positive number';
    const InvalidUrl = 'Url is invalid';
    const UploadTimeFormat = 'Upload time must have the ISO 8601 with the UTC timezone format';
    const NegativePeerCount = 'Peer count must be a nonnegative number';
    const NegativeSeedCount = 'Seed count must be a nonnegative number';

    const ShortPhrase = 'Phrase must have at least 3 characters';
    const OutOfRangeLimit = 'Result limit must be in 1 .. 100 range';
    const UnsupportedCategory = 'Search query has unsupported torrent category';
}