# Service Schema
[![Software license][ico-license]](README.md)
[![Version][ico-version-stable]][link-packagist]
[![Download][ico-downloads-monthly]][link-downloads]
[![Build status][ico-travis]][link-travis]
[![Coverage][ico-codecov]][link-codecov]


[ico-license]: https://img.shields.io/github/license/nrk/predis.svg?style=flat-square
[ico-version-stable]: https://img.shields.io/packagist/v/brightecapital/service-schema.svg
[ico-downloads-monthly]: https://img.shields.io/packagist/dm/brightecapital/service-schema.svg
[ico-travis]: https://travis-ci.com/brighte-capital/service-schema.svg?branch=master
[ico-codecov]: https://codecov.io/gh/brighte-capital/service-schema/branch/master/graph/badge.svg

[link-packagist]: https://packagist.org/packages/brightecapital/service-schema
[link-codecov]: https://codecov.io/gh/brighte-capital/service-schema
[link-travis]: https://travis-ci.com/brighte-capital/service-schema
[link-downloads]: https://packagist.org/packages/brightecapital/service-schema/stats

Service-schema was created as a tool to process messages from broker or between microservices, implementing event sourcing and Saga pattern.
Based on the concept of "event schema first", service-schema improves things a step further by introducing  schema for each service 
in order to reuse services and schemas in different events through configuration:

+ Each event might has one or many services that are listening to it
+ Each service has one schema which will be used to validate the input json

## Configuration
<pre>
"require": {
        "brightecapital/service-schema": "^1.0.0"
    }
</pre>
## Sample code
### configs 
events.json
<pre>
[
  {
    "event": "Users.afterSaveCommit.Create",
    "services": [
      "BrighteCapital\\ServiceSchema\\Tests\\Service\\Samples\\CreateContact",
      "BrighteCapital\\ServiceSchema\\Tests\\Service\\Samples\\CreateTask"
    ]
  },
  {
    "event": "Users.afterSaveCommit.Update",
    "services": [
      "BrighteCapital\\ServiceSchema\\Tests\\Service\\Samples\\UpdateContact"
    ]
  }
]
</pre>

In this events.json:
- There are 02 events that the microservice is listening to: "Users.afterSaveCommit.Create", "Users.afterSaveCommit.Update"
- Each of event have a list of services that listen to the event

services.json
<pre>
[
  {
    "service": "BrighteCapital\\ServiceSchema\\Tests\\Service\\Samples\\CreateContact",
    "schema": "/jsons/schemas/CreateContact.json",
    "callbacks": [
      "BrighteCapital\\ServiceSchema\\Tests\\Service\\Samples\\PushEventToSqs",
      "BrighteCapital\\ServiceSchema\\Tests\\Service\\Samples\\PushEventToLog"
    ]
  },
  {
    "service": "BrighteCapital\\ServiceSchema\\Tests\\Service\\Samples\\UpdateContact",
    "schema": "/jsons/schemas/UpdateContact.json",
    "callbacks": [
      "BrighteCapital\\ServiceSchema\\Tests\\Service\\Samples\\PushEventToLog"
    ]
  },
  {
    "service": "BrighteCapital\\ServiceSchema\\Tests\\Service\\Samples\\CreateTask",
    "schema": "/jsons/schemas/CreateTask.json"
  }
]
</pre>

In this services.json:
- There are 03 services:  "BrighteCapital\\ServiceSchema\\Tests\\Service\\Samples\\CreateContact", "BrighteCapital\\ServiceSchema\\Tests\\Service\\Samples\\UpdateContact", "BrighteCapital\\ServiceSchema\\Tests\\Service\\Samples\\CreateTask",
- Each service has a schema and a list of callback services

### services schema
CreateContact.json
<pre>
{
  "type": "object",
  "properties": {
    "event": {
      "type": "string",
      "minLength": 0,
      "maxLength": 256
    },
    "time": {
      "type": "string",
      "minLength": 0,
      "maxLength": 256
    },
    "payload": {
      "type": "object",
      "properties": {
        "user": {
          "type": "object",
          "properties": {
            "data": {
              "type": "object"
            },
            "class": {
              "type": "string",
              "default": "\\App\\Entity\\User"
            }
          },
          "required": [
            "data"
          ]
        },
        "account": {
          "type": "object",
          "properties": {
            "data": {
              "type": "object"
            },
            "class": {
              "type": "string",
              "default": "\\App\\Entity\\Account"
            }
          },
          "required": [
            "data"
          ]
        }
      },
      "required": [
        "user",
        "account"
      ],
      "additionalProperties": false
    }
  },
  "required": [
    "event",
    "payload"
  ],
  "additionalProperties": true
}
</pre>

In this CreateContact.json:
- It requires the message to have "name" and "payload"
- "payload" requires "user" and "account"
- "user" requires "data"
- "account" requires "data"

### Event
<pre>
$event = new Event();
$event->setName("Users.afterSaveCommit.Create");
$event->setTime("20190730123000");
$event->setPayload(["user" => ["data" => ["name" => "Ken"]], "account" => ["data" => ["name" => "Brighte"]]]);
$message = $event->toJson();
// '{"name":"Users.afterSaveCommit.Create","time":"20190730123000","payload":{"user":{"data":{"name":"Ken"}},"account":{"data":{"name":"Brighte"}}}}'
// this message is used to push to SQS or other services
</pre>

### Service
<pre>
namespace BrighteCapital\ServiceSchema\Tests\Service\Samples;

use BrighteCapital\ServiceSchema\Event\Event;
use BrighteCapital\ServiceSchema\Event\EventInterface;
use BrighteCapital\ServiceSchema\Service\Service;
use BrighteCapital\ServiceSchema\Service\ServiceInterface;

class CreateContact extends Service implements ServiceInterface
{
    public function consume(EventInterface $event = null)
    {
        echo "CreateContact";

        return new Event();
    }
}

</pre>

### Processor
<pre>
// Receive message from SQS or other services
$message = '{"name":"Users.afterSaveCommit.Create","time":"20190730123000","payload":{"user":{"data":{"name":"Ken"}},"account":{"data":{"name":"Brighte"}}}}';
// config the Processor
$processor = new Processor(["events.json"], ["services.json"], "serviceSchemaDir");
// process the message
$result = $processor->process($message);
/*
 * In this example, event "Users.afterSaveCommit.Create" has 02 services listening to it (configued in events.json)
 * "BrighteCapital\\ServiceSchema\\Tests\\Service\\Samples\\CreateContact", "BrighteCapital\\ServiceSchema\\Tests\\Service\\Samples\\CreateTask"
 * When $processor->process(message): CreateContact->run(Event) and CreateTask->run(Event) will be executed.
 * Service CreateContact has 02 callback services (configured in services.json): 
 * "BrighteCapital\\ServiceSchema\\Tests\\Service\\Samples\\PushEventToSqs","BrighteCapital\\ServiceSchema\\Tests\\Service\\Samples\\PushEventToLog"
 * When CreateContact->run(Event) returns an Event then PushEventToSqs->run(Event) and PushEventToLog->run(Event) will be executed
 */
</pre>

### Tests
Please refer to tests for sample configs of events, services, schemas and usage of Processor
- tests/jsons/configs/events.json: configuration of events
- tests/jsons/config/services.json: configuration of services
- tests/jsons/configs/schemas/: sample services schemas (CreateContact.json, CreateTask.json, UpdateContact.json)
- tests/Main/ProcessorTest.php: how to config and run the Processor
