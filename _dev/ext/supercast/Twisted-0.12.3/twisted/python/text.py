
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

"""Miscellany of text-munging functions.
"""

import string, types

def stringyString(object, indentation=''):
    """Expansive string formatting for sequence types.

    list.__str__ and dict.__str__ use repr() to display their
    elements.  This function also turns these sequence types
    into strings, but uses str() on their elements instead.

    Sequence elements are also displayed on seperate lines,
    and nested sequences have nested indentation.
    """
    braces = ''
    sl = []

    if type(object) is types.DictType:
        braces = '{}'
        for key, value in object.items():
            value = stringyString(value, indentation + '   ')
            if isMultiline(value):
                if endsInNewline(value):
                    value = value[:-len('\n')]
                sl.append("%s %s:\n%s" % (indentation, key, value))
            else:
                # Oops.  Will have to move that indentation.
                sl.append("%s %s: %s" % (indentation, key,
                                         value[len(indentation) + 3:]))

    elif type(object) in (types.TupleType, types.ListType):
        if type(object) is types.TupleType:
            braces = '()'
        else:
            braces = '[]'

        for element in object:
            element = stringyString(element, indentation + ' ')
            sl.append(string.rstrip(element) + ',')
    else:
        sl[:] = map(lambda s, i=indentation: i+s,
                    string.split(str(object),'\n'))

    if not sl:
        sl.append(indentation)

    if braces:
        sl[0] = indentation + braces[0] + sl[0][len(indentation) + 1:]
        sl[-1] = sl[-1] + braces[-1]

    s = string.join(sl, "\n")

    if isMultiline(s) and not endsInNewline(s):
        s = s + '\n'

    return s

def isMultiline(s):
    """Returns True if this string has a newline in it."""
    return (string.find(s, '\n') != -1)

def endsInNewline(s):
    """Returns True if this string ends in a newline."""
    return (s[-len('\n'):] == '\n')

def docstringLStrip(docstring):
    """Gets rid of unsightly lefthand docstring whitespace residue.

    You'd think someone would have done this already, but apparently
    not in 1.5.2.
    """

    if not docstring:
        return docstring

    docstring = string.replace(docstring, '\t', ' ' * 8)
    lines = string.split(docstring,'\n')

    leading = 0
    for l in xrange(1,len(lines)):
        line = lines[l]
        if string.strip(line):
            while 1:
                if line[leading] == ' ':
                    leading = leading + 1
                else:
                    break
        if leading:
            break

    outlines = lines[0:1]
    for l in xrange(1,len(lines)):
        outlines.append(lines[l][leading:])

    return string.join(outlines, '\n')

def greedyWrap(inString, width=80):
    """Given a string and a column width, return a list of lines.

    Caveat: I'm use a stupid greedy word-wrapping
    algorythm.  I won't put two spaces at the end
    of a sentence.  I don't do full justification.
    And no, I've never even *heard* of hypenation.
    """

    outLines = []

    inWords = string.split(inString)

    column = 0
    ptr_line = 0
    while inWords:
        column = column + len(inWords[ptr_line])
        ptr_line = ptr_line + 1

        if (column > width):
            if ptr_line == 1:
                # This single word is too long, it will be the whole line.
                pass
            else:
                # We've gone too far, stop the line one word back.
                ptr_line = ptr_line - 1
            (l, inWords) = (inWords[0:ptr_line], inWords[ptr_line:])
            outLines.append(string.join(l,' '))

            ptr_line = 0
            column = 0
        elif not (len(inWords) > ptr_line):
            # Clean up the last bit.
            outLines.append(string.join(inWords, ' '))
            del inWords[:]
        else:
            # Space
            column = column + 1
    # next word

    return outLines


wordWrap = greedyWrap
