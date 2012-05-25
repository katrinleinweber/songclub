# Twisted, the Framework of Your Internet
# Copyright (C) 2001 Matthew W. Lefkowitz
# 
# This library is free software; you can redistribute it and/or
# modify it under the terms of version 2.1 of the GNU Lesser General Public
# License as published by the Free Software Foundation.
# 
# This library is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
# Lesser General Public License for more details.
# 
# You should have received a copy of the GNU Lesser General Public
# License along with this library; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
"""Implement standard (and unused) TCP protocols.

These protocols are either provided by inetd, or are not provided at all.
"""

# system imports
import time, struct

# twisted import
from twisted.protocols import protocol
from twisted.internet import task


class Echo(protocol.Protocol):
    """As soon as any data is received, write it back (RFC 862)"""
    
    def dataReceived(self, data):
        self.transport.write(data)


class Discard(protocol.Protocol):
    """Discard any received data (RFC 863)"""
    
    def dataReceived(self, data):
        # I'm ignoring you, nyah-nyah
        pass


class Chargen(protocol.Protocol):
    """Generate repeating noise (RFC 864)"""
    
    def connectionMade(self):
        self.makeData()
    
    def makeData(self):
        self.transport.write("twistedzombies")
        # schedule this function to be called
        t = task.Task()
        t.addWork(self.makeData)
        task.schedule(t)


class QOTD(protocol.Protocol):
    """Return a quote of the day (RFC 865)"""
    
    def connectionMade(self):
        self.transport.write(self.getQuote())
        self.transport.loseConnection()

    def getQuote(self):
        """Return a quote. May be overrriden in subclasses."""
        return "An apple a day keeps the doctor away.\r\n"

class Who(protocol.Protocol):
    """Return list of active users (RFC 866)"""
    
    def connectionMade(self):
        self.transport.write(self.getUsers())
        self.transport.loseConnection()
    
    def getUsers(self):
        """Return active users. Override in subclasses."""
        return "root\r\n"


class Daytime(protocol.Protocol):
    """Send back the daytime in ASCII form (RFC 867)"""
    
    def connectionMade(self):
        self.transport.write(time.asctime(time.gmtime(time.time())) + '\r\n')
        self.transport.loseConnection()


class Time(protocol.Protocol):
    """Send back the time in machine readable form (RFC 868)"""
    
    def connectionMade(self):
        # is this correct only for 32-bit machines?
        result = struct.pack("!i", int(time.time()))
        self.transport.write(result)
        self.transport.loseConnection()
