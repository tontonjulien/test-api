TEST API
=========

Requirements
------------
* [Git](https://git-scm.com/downloads)
* [Docker](https://www.docker.com/community-edition#/download)
* [Docker compose](https://docs.docker.com/compose/install/)
* [Make](http://www.gnu.org/software/make/)

Install
-------

Run the following commands in a terminal :

```bash
$ git clone https://github.com/tontonjulien/test-api.git
$ cd test-api
$ make install
```
Access to the main endpoint :

```bash
curl --location --request POST 'localhost:8088' \
--data-raw '[{"id": "xxxx-xxxx-xxxx-xxxx", "event": "impressions", "timestamp": 00000000000}, ...]'
```

Run the test suite
-------

```bash
$ make test

```
