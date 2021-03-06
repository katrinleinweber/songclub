
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

class Request:

    def __init__(self, resolver, name, callback, errback, type, timeout):
        self.name = name
        self.callback = callback
        self.errback = errback
        self.type = type
        self.timeout = timeout
        self.resolver = resolver

    def __call__(self):
        self.resolver.resolve(self.name, self.callback, self.errback, self.type,
                              self.timeout)

class ResolverChain:

    def __init__(self, resolvers):
        self.resolvers = resolvers
        self.resolvers.reverse()

    def resolve(self, name, callback, errback=None, type=1, timeout=10):
        for resolver in self.resolvers[:-1]:
            errback = Request(resolver, name, callback, errback, type, timeout)
        self.resolvers[-1].resolve(name, callback, errback, type, timeout)
