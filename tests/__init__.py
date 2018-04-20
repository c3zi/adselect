import socket
import json

from twisted.trial import unittest
from twisted.internet import defer, reactor
from twisted.internet.protocol import Protocol
from twisted.web.client import Agent
from twisted.internet.defer import succeed
from twisted.web.iweb import IBodyProducer
from twisted.web.http_headers import Headers

from zope.interface import implements

from adselect.iface import server as iface_server
from adselect.iface import const as iface_consts
from adselect import db


class StringProducer(object):
    implements(IBodyProducer)

    def __init__(self, body):
        self.body = body
        self.length = len(body)

    def startProducing(self, consumer):
        consumer.write(self.body)
        return succeed(None)

    def pauseProducing(self):
        pass

    def stopProducing(self):
        pass


class ReceiverProtocol(Protocol):
    def __init__(self, finished):
        self.finished = finished
        self.body = []

    def dataReceived(self, databytes):
        self.body.append(databytes)

    def connectionLost(self, reason):
        self.finished.callback(''.join(self.body))


class DBTestCase(unittest.TestCase):
    @defer.inlineCallbacks
    def setUp(self):
        self.conn = yield db.get_mongo_connection()
        self.db = yield db.get_mongo_db()

        yield db.configure_db()
        self.timeout = 5

    @defer.inlineCallbacks
    def tearDown(self):
        yield self.conn.drop_database(self.db)
        yield db.disconnect()


class WebTestCase(DBTestCase):

    @defer.inlineCallbacks
    def setUp(self):
        yield super(WebTestCase, self).setUp()

        self.port = iface_server.configure_iface()
        self.client = Agent(reactor)

    @defer.inlineCallbacks
    def tearDown(self):
        yield super(WebTestCase, self).tearDown()

        self.port.stopListening()

    @defer.inlineCallbacks
    def get_response(self, method, params=None):
        post_data = StringProducer(json.dumps({
            "jsonrpc": "2.0",
            "id": "test_hit",
            "method": method,
            "params": params
        }))

        host = socket.gethostbyname(socket.gethostname())

        response = yield self.client.request('POST',
                                             'http://{0}:{1}'.format(host, iface_consts.SERVER_PORT),
                                             Headers({'content-type': ['text/plain']}),
                                             post_data)

        finished = defer.Deferred()
        response.deliverBody(ReceiverProtocol(finished))
        data = yield finished
        defer.returnValue(json.loads(data) if data else None)