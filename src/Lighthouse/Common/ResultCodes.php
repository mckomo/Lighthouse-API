<?php

namespace Lighthouse\Common;

final class ResultCodes
{
    const Unknown = 'unknown';
    const Successful = 'successful';
    const ResourceCreated = 'resource_created';
    const ResourceUnchanged = 'resource_unchanged';
    const ResourceNotFound = 'resource_not_found';
    const InvalidInput = 'invalid_input';
    const ServiceError = 'service_error';
}
