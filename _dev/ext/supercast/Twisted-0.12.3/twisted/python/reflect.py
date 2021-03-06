
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

"""
Standardized versions of various cool things that you can do with
Python's reflection capabilities.  This should probably involve
metaclasses somehow, but I don't understand them, so nyah :-)
"""

# System Imports
import sys
import types
import cStringIO
import traceback

# Sibling Imports
import reference

class Settable:
    """
    A mixin class for syntactic sugar.  Lets you assign attributes by
    calling with keyword arguments; for example, x(a=b,c=d,y=z) is the
    same as x.a=b;x.c=d;x.y=z.  The most useful place for this is
    where you don't want to name a variable, but you do want to set
    some attributes; for example, X()(y=z,a=b).
    """
    def __init__(self, **kw):
        apply(self,(),kw)

    def __call__(self,**kw):
        for key,val in kw.items():
            setattr(self,key,val)
        return self

class Accessor:

    """
    Extending this class will give you explicit accessor methods; a
    method called set_foo, for example, is the same as an if statement
    in __setattr__ looking for 'foo'.  Same for get_foo and del_foo.
    There are also reallyDel and reallySet methods, so you can
    override specifics in subclasses without clobbering __setattr__
    and __getattr__.
    """

    def __setattr__(self, k,v):
        kstring='set_%s'%k
        # early-out for references, since they will be reassigned
        # later, and no accessor method should have to know what to do
        # with them.
        if (not isinst(v, reference.Reference) and
            hasattr(self.__class__,kstring)):
            return getattr(self,kstring)(v)
        else:
            self.reallySet(k,v)

    def __getattr__(self, k):
        kstring='get_%s'%k
        if hasattr(self.__class__,kstring):
            return getattr(self,kstring)()
        raise AttributeError("%s instance has no accessor for: %s" % (str(self.__class__),k))

    def __delattr__(self, k):
        kstring='del_%s'%k
        if hasattr(self.__class__,kstring):
            getattr(self,kstring)()
            return
        self.reallyDel(k)

    def reallySet(self, k,v):
        """
        *actually* set self.k to v without incurring side-effects.
        This is a hook to be overridden by subclasses.
        """
        self.__dict__[k]=v

    def reallyDel(self, k):
        """
        *actually* del self.k without incurring side-effects.  This is a
        hook to be overridden by subclasses.
        """
        del self.__dict__[k]


class Summer(Accessor):
    """
    Extend from this class to get the capability to maintain 'related
    sums'.  Have a tuple in your class like the following:

    sums=(('amount','credit','credit_total'),
          ('amount','debit','debit_total'))

    and the 'credit_total' member of the 'credit' member of self will
    always be incremented when the 'amount' member of self is
    incremented, similiarly for the debit versions.
    """

    def reallySet(self, k,v):
        "This method does the work."
        for sum in self.sums:
            attr=sum[0]
            obj=sum[1]
            objattr=sum[2]
            if k == attr:
                try:
                    oldval=getattr(self, attr)
                except:
                    oldval=0
                diff=v-oldval
                if hasattr(self, obj):
                    ob=getattr(self,obj)
                    if ob is not None:
                        try:oldobjval=getattr(ob, objattr)
                        except:oldobjval=0.0
                        setattr(ob,objattr,oldobjval+diff)

            elif k == obj:
                if hasattr(self, attr):
                    x=getattr(self,attr)
                    setattr(self,attr,0)
                    y=getattr(self,k)
                    Accessor.reallySet(self,k,v)
                    setattr(self,attr,x)
                    Accessor.reallySet(self,y,v)
        Accessor.reallySet(self,k,v)

class Promise:
    """I represent an object not yet available.

    Methods called on me will be queued and sent as soon as the object becomes
    available.  Typically my __become__ method is registered as a callback with
    some event that will return my new identity.
    """
    def __init__(self):
        self.calls = []

    def __become__(self, new_self):
        for c in self.calls:
            apply(getattr(new_self, c[0]), c[1])
        self.__class__ = new_self.__class__
        self.__dict__ = new_self.__dict__

    def __getattr__(self, key):
        return QueueMethod(key, self.calls)

class QueueMethod:
    """ I represent a method that doesn't exist yet."""
    def __init__(self, name, calls):
        self.name = name
        self.calls = calls
    def __call__(self, *args):
        self.calls.append((self.name, args))


def funcinfo(function):
    """
    this is more documentation for myself than useful code.
    """
    code=function.func_code
    name=function.func_name
    argc=code.co_argcount
    argv=code.co_varnames[:argc]
    defaults=function.func_defaults

    print 'The function',name,'accepts',argc,'arguments.'
    if defaults:
        required=argc-len(defaults)
        print 'It requires',required,'arguments.'
        print 'The arguments required are: ',argv[:required]
        print 'additional arguments are:'
        for i in range(argc-required):
            j=i+required
            print argv[j],'which has a default of',defaults[i]


# currentThread uses 'print'; that's no good.
def currentThread():
    from threading import _get_ident,_active,_DummyThread
    try: return _active[_get_ident()]
    except KeyError: return _DummyThread()
# del _get_ident,_active,_DummyThread

class ThreadAttr:
    def __init__(self,threads=None,default=None):
        self.__dict__['_threads']=threads or {}
        self.__dict__['_default']=default
    def __get(self):
        try: return self._threads[currentThread()]
        except KeyError: return self._default
    def __getattr__(self,key):
        return getattr(self.__get(),key)
    def __setattr__(self,key,val):
        return setattr(self.__get(),key)
    def __delattr__(self,key):
        return delattr(self.__get(),key)

ISNT=0
WAS=1
IS=2

def qual(clazz):
    return  clazz.__module__+'.'+clazz.__name__

def getcurrent(clazz):
    assert type(clazz) == types.ClassType, 'must be a class...'
    module = namedModule(clazz.__module__)
    currclass = getattr(module, clazz.__name__, None)
    if currclass is None:
        print "Reflection Warning: class %s deleted from module %s" % (
            clazz.__name__, clazz.__module__)
        return clazz
    return currclass

# class graph nonsense

# I should really have a better name for this...
def isinst(inst,clazz):
    if type(inst) != types.InstanceType or type(clazz)!=types.ClassType:
        return isinstance(inst,clazz)
    cl = inst.__class__
    cl2 = getcurrent(cl)
    clazz = getcurrent(clazz)
    if issubclass(cl2,clazz):
        if cl == cl2:
            return WAS
        else:
            inst.__class__ = cl2
            return IS
    else:
        return ISNT

def namedModule(name):
    return __import__(name, {}, {}, 'x')

def _reclass(clazz):
    clazz = getattr(namedModule(clazz.__module__),clazz.__name__)
    clazz.__bases__ = tuple(map(_reclass, clazz.__bases__))
    return clazz

def refrump(obj):
    x = _reclass(obj.__class__)
    if x is not obj.__class__:
        obj.__class__ = x
    return obj

def macro(name, filename, source, **identifiers):
    """macro(name, source, **identifiers)

    This allows you to create macro-like behaviors in python.  See
    twisted.python.hook for an example of its usage.
    """

    if not identifiers.has_key('name'):
        identifiers['name'] = name
    source = source % identifiers
    codeplace = "<%s (macro)>" % filename
    code = compile(source, codeplace, 'exec')
    dict = {}
    exec code in dict, dict
    return dict[name]

def safe_repr(obj):
    """safe_repr(anything) -> string
    Returns a string representation of an object (or a traceback, if that
    object's __repr__ raised an exception) """

    try:
        return repr(obj)
    except:
        io = cStringIO.StringIO()
        traceback.print_exc(file=io)
        return "exception in repr!\n"+ io.getvalue()


##the following were factored out of usage

def prefixedMethodNames(classObj, prefix):
    """A list of method names with a given prefix in a given class.
    """
    dct = {}
    addMethodNamesToDict(classObj, dct, prefix)
    return dct.keys()

def addMethodNamesToDict(classObj, dict, prefix, baseClass=None):
    """
    addMethodNamesToDict(classObj, dict, prefix, baseClass=None) -> dict
    this goes through 'classObj' (and it's bases) and puts method names
    starting with 'prefix' in 'dict' with a value of 1. if baseClass isn't
    None, methods will only be added if classObj is-a baseClass

    the resulting dict should look something like:
    {"methodname": 1, "methodname2": 1}.
    """
    for base in classObj.__bases__:
        addMethodNamesToDict(base, dict, prefix, baseClass)

    if baseClass is None or baseClass in classObj.__bases__:
        for name, method in classObj.__dict__.items():
            optName = name[len(prefix):]
            if ((type(method) is types.FunctionType)
                and (name[:len(prefix)] == prefix)
                and (len(optName))):
                dict[optName] = 1


def accumulateClassDict(classObj, attr, dict, baseClass=None):
    """Accumulate all attributes of a given name in a class heirarchy into a single dictionary.

    Assuming all class attributes of this name are dictionaries.
    If any of the dictionaries being accumulated have the same key, the
    one highest in the class heirarchy wins.
    (XXX: If \"higest\" means \"closest to the starting class\".)

    Ex::

    | class Soy:
    |   properties = {\"taste\": \"bland\"}
    |
    | class Plant:
    |   properties = {\"colour\": \"green\"}
    |
    | class Seaweed(Plant):
    |   pass
    |
    | class Lunch(Soy, Seaweed):
    |   properties = {\"vegan\": 1 }
    |
    | dct = {}
    |
    | accumulateClassDict(Lunch, \"properties\", dct)
    |
    | print dct

    {\"taste\": \"bland\", \"colour\": \"green\", \"vegan\": 1}
    """
    for base in classObj.__bases__:
        accumulateClassDict(base, attr, dict)
    if baseClass is None or baseClass in classObj.__bases__:
        dict.update(getattr(classObj, attr, {}))

def accumulateClassList(classObj, attr, listObj, baseClass=None):
    """Accumulate all attributes of a given name in a class heirarchy into a single list.

    Assuming all class attributes of this name are lists.
    """
    for base in classObj.__bases__:
        accumulateClassList(base, attr, listObj)
    if baseClass is None or baseClass in classObj.__bases__:
        listObj.extend(getattr(classObj, attr, []))

def isSame(a, b):
    return (a is b)
def isLike(a, b):
    return (a == b)

def modgrep(goal):
    return objgrep(sys.modules, goal, isLike, 'sys.modules')

def objgrep(start, goal, eq=isLike, path='', paths=None, seen=None):
    '''An insanely CPU-intensive process for finding stuff.
    '''
    if paths is None:
        paths = []
    if seen is None:
        seen = {}
    if eq(start, goal):
        paths.append(path)
    if seen.has_key(id(start)):
        if seen[id(start)] is start:
            return
    seen[id(start)] = start
    if isinstance(start, types.DictionaryType):
        r = []
        for k, v in start.items():
            objgrep(k, goal, eq, path+'{'+repr(v)+'}', paths, seen)
            objgrep(v, goal, eq, path+'['+repr(k)+']', paths, seen)
    elif isinstance(start, types.ListType) or isinstance(start, types.TupleType):
        for idx in xrange(len(start)):
            objgrep(start[idx], goal, eq, path+'['+str(idx)+']', paths, seen)
    elif (isinstance(start, types.InstanceType) or
          isinstance(start, types.ClassType) or
          isinstance(start, types.ModuleType)):
        for k, v in start.__dict__.items():
            objgrep(v, goal, eq, path+'.'+k, paths, seen)
        if isinstance(start, types.InstanceType):
            objgrep(start.__class__, goal, eq, path+'.__class__', paths, seen)
    return paths
