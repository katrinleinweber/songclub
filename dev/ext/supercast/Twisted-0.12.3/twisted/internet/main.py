
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


# System Imports
import os

# Twisted Import
from twisted.python.runtime import platform

if platform.getType() != 'java':
    import select, signal
    from errno import EINTR

import traceback
import sys
import socket
CONNECTION_LOST = -1
CONNECTION_DONE = -2

theApplication = None

# Twisted Imports

from twisted.python import threadable, log, delay
from twisted.persisted import styles

# Sibling Imports

theTimeouts = delay.Delayed() # A delay for non-peristent delayed actions
theTimeouts.ticktime = 1

def addTimeout(method, seconds):
    """Add a method which will time out after a given interval.

    The given method will always time out before a server shuts down,
    and will never persist.
    """
    theTimeouts.later(method, seconds)


class DummyResolver:
    """
    An implementation of a synchronous resolver, from Python's socket stuff.
    This may be ill-placed.
    """
    def resolve(self, name, callback, errback=None, type=1, timeout=10):
        if type != 1:
            errback()
            return
        try:
            address = socket.gethostbyname(name)
        except socket.error:
            errback()
        else:
            callback(address)

reads = {}
writes = {}
running = None
delayeds = [theTimeouts]
shutdowns = [theTimeouts.runEverything]
resolver = DummyResolver()
interruptCountdown = 5

def shutDown(a=None, b=None):
    """Run all shutdown callbacks (save all running Applications) and exit.

    This is called by various signal handlers which should cause
    the process to exit.  It can also be called directly in order
    to trigger a clean shutdown.
    """
    global running, interruptCountdown
    if running:
        if threadable.threaded:
            removeReader(waker)
        running = 0
        log.msg('Starting Shutdown Sequence.')
    elif interruptCountdown > 0:
        log.msg('Raising exception in %s more interrupts!' % interruptCountdown)
        interruptCountdown = interruptCountdown - 1
    else:
        raise RuntimeError("Shut Down Exception!")


def runUntilCurrent():
    """Run all delayed loops and return a timeout for when the next call expects to be made.
    """
    # This code is duplicated for efficiency later.
    timeout = None
    for delayed in delayeds:
        delayed.runUntilCurrent()
        newTimeout = delayed.timeout()
        if ((newTimeout is not None) and
            ((timeout is None) or
             (newTimeout < timeout))):
            timeout = newTimeout
    return timeout


def doSelect(timeout,
             # Since this loop should really be as fast as possible,
             # I'm caching these global attributes so the interpreter
             # will hit them in the local namespace.
             reads=reads,
             writes=writes,
             rhk=reads.has_key,
             whk=writes.has_key):
    """Run one iteration of the I/O monitor loop.

    This will run all selectables who had input or output readiness
    waiting for them.
    """
    while 1:
        try:
            r, w, ignored = select.select(reads.keys(),
                                          writes.keys(),
                                          [], timeout)
            break
        except select.error,se:
            if se.args[0] in (0, 2):
                # windows does this if it got an empty list
                if (not reads) and (not writes):
                    return
                else:
                    raise
            elif se.args[0] == EINTR:
                # If this is just an interrupted system call, continue on
                # unless it was generated by a signal handler which will
                # set running to false.
                if not running:
                    return
                # If the timeout is 0 anyway, just bail.
                if not timeout:
                    return
            else:
                raise

    for selectables, method, dict in ((r, "doRead", reads),
                                      (w,"doWrite", writes)):
        hkm = dict.has_key
        for selectable in selectables:
            # if this was disconnected in another thread, kill it.
            if not hkm(selectable):
                continue
            # This for pausing input when we're not ready for more.
            log.logOwner.own(selectable)
            try:
                why = getattr(selectable, method)()
                handfn = getattr(selectable, 'fileno', None)
                if not handfn or handfn() == -1:
                    why = CONNECTION_LOST
            except:
                traceback.print_exc(file=log.logfile)
                why = CONNECTION_LOST
            if why:
                removeReader(selectable)
                removeWriter(selectable)
                try:
                    selectable.connectionLost()
                except:
                    traceback.print_exc(file=log.logfile)
            log.logOwner.disown(selectable)


def iterate(timeout=0.):
    """Do one iteration of the main loop.

    I will run any simulated (delayed) code, and process any pending I/O.
    I will not block.  This is meant to be called from a high-freqency
    updating loop function like the frame-processing function of a game.
    """
    for delayed in delayeds:
        delayed.runUntilCurrent()
    doSelect(timeout)


def run():
    """Run input/output and dispatched/delayed code.

    This call never returns.  It is the main loop which runs
    delayed timers (see twisted.python.delay and addDelayed),
    and the I/O monitor (doSelect).
    """
    global running
    running = 1
    threadable.registerAsIOThread()
    signal.signal(signal.SIGINT, shutDown)
    signal.signal(signal.SIGTERM, shutDown)

    # Catch Ctrl-Break in windows (only available in 2.2b1 onwards)
    if hasattr(signal, "SIGBREAK"):
        signal.signal(signal.SIGBREAK, shutDown)

    if platform.getType() == 'posix':
        signal.signal(signal.SIGCHLD, process.reapProcess)

    try:
        try:
            while running:
                # Advance simulation time in delayed event
                # processors.
                timeout = None
                for delayed in delayeds:
                    delayed.runUntilCurrent()
                    newTimeout = delayed.timeout()
                    if ((newTimeout is not None) and
                        ((timeout is None) or
                         (newTimeout < timeout))):
                        timeout = newTimeout
                doSelect(timeout)
        except select.error:
            log.msg('shutting down after select() loop interruption')
            if running:
                log.msg('Warning!  Shutdown not called properly!')
                traceback.print_exc(file=log.logfile)
                shutDown()
            if platform.getType() =='win32':
                log.msg("(Logging traceback for WinXX exception info)")
                traceback.print_exc(file=log.logfile)
        except:
            log.msg("Unexpected error in Selector.run.")
            traceback.print_exc(file=log.logfile)
            shutDown()
            raise
        else:
            log.msg('Select loop terminated.')

    finally:
        for reader in reads.keys():
            if reads.has_key(reader):
                del reads[reader]
            if writes.has_key(reader):
                del writes[reader]
            log.logOwner.own(reader)
            try:
                reader.connectionLost()
            except:
                traceback.print_exc(file=log.logfile)
            log.logOwner.disown(reader)
        # TODO: implement shutdown callbacks for gtk & tk
        for callback in shutdowns:
            try:
                callback()
            except:
                traceback.print_exc(file=log.logfile)

def addShutdown(function):
    """Add a function to be called at shutdown.
    """
    shutdowns.append(function)

def addDelayed(delayed):
    """Add a Delayed object to the event loop.
    """
    delayeds.append(delayed)

def removeDelayed(delayed):
    """Remove a Delayed object from the event loop.
    """
    delayeds.remove(delayed)

def addReader(reader):
    """Add a FileDescriptor for notification of data available to read.
    """
    reads[reader] = 1

def addWriter(writer):
    """Add a FileDescriptor for notification of data available to write.
    """
    writes[writer] = 1

def removeReader(reader):
    """Remove a Selectable for notification of data available to read.
    """
    if reads.has_key(reader):
        del reads[reader]

def removeWriter(writer):
    """Remove a Selectable for notification of data available to write.
    """
    if writes.has_key(writer):
        del writes[writer]

class _Win32Waker(styles.Ephemeral):
    """I am a workaround for the lack of pipes on win32.

    I am a pair of connected sockets which can wake up the main loop
    from another thread.
    """
    def __init__(self):
        """Initialize.
        """
        # Following select_trigger (from asyncore)'s example;
        address = ("127.0.0.1",19939)
        server = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
        client = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
        client.setsockopt(socket.IPPROTO_TCP, 1, 1)
        server.bind(address)
        server.listen(1)
        client.connect(address)
        reader, clientaddr = server.accept()
        client.setblocking(1)
        reader.setblocking(0)
        self.r = reader
        self.w = client
        self.fileno = self.r.fileno

    def wakeUp(self):
        """Send a byte to my connection.
        """
        self.w.send('x')

    def doRead(self):
        """Read some data from my connection.
        """
        self.r.recv(8192)

class _UnixWaker(styles.Ephemeral):
    """This class provides a simple interface to wake up the select() loop.

    This is necessary only in multi-threaded programs.
    """
    def __init__(self):
        """Initialize.
        """
        i, o = os.pipe()
        self.i = os.fdopen(i,'r')
        self.o = os.fdopen(o,'w')
        self.fileno = self.i.fileno

    def doRead(self):
        """Read one byte from the pipe.
        """
        self.i.read(1)

    def wakeUp(self):
        """Write one byte to the pipe, and flush it.
        """
        try:
            self.o.write('x')
            self.o.flush()
        except ValueError:
            # o has been closed
            pass

    def connectionLost(self):
        """Close both ends of my pipe.
        """
        self.i.close()
        self.o.close()

if platform.getType() == 'posix':
    _Waker = _UnixWaker
elif platform.getType() == 'win32':
    _Waker = _Win32Waker

def wakeUp():
    if not threadable.isInIOThread():
        waker.wakeUp()


wakerInstalled = 0

def installWaker():
    """Install a `waker' to allow other threads to wake up the IO thread.
    """
    global addReader, addWriter, waker, wakerInstalled
    if not wakerInstalled:
        wakerInstalled = 1
        waker = _Waker()
        addReader(waker)

def initThreads():
    """Perform initialization required for threading.
    """
    if platform.getType() != 'java':
        installWaker()

threadable.whenThreaded(initThreads)
# Sibling Import
import process

# Work on Jython
if platform.getType() == 'java':
    import jnternet

# backward compatibility stuff
import app
Application = app.Application