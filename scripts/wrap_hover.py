# Wrap "indent &:hover {" ... "indent }" in @include hover-only (multiline regex)
import re
import os

base = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
path = os.path.join(base, 'resources', 'sources', 'admin', 'admin-layout-redesign.scss')

with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# Match: (indent)&:hover {\n (body) \n (same indent)}
# Body can be multiple lines; we need to find the closing at same indent
def replacer(m):
    indent = m.group(1)
    body = m.group(2)
    return indent + '@include hover-only {\n' + indent + '    &:hover {\n' + body + indent + '    }\n' + indent + '}'

# Pattern: (indent) &:hover { newline, then (body) until we see newline + indent + }
# For body we need to match lines that don't start with "indent }"
pattern = re.compile(
    r'^(\s+)&:hover\s*\{\n((?:(?!^\1\}).)*?)^\1\}',
    re.MULTILINE | re.DOTALL
)
new_content, n = pattern.subn(replacer, content)
with open(os.path.join(base, 'scripts', 'wrap_result.txt'), 'w') as f:
    f.write('Wrapped %d blocks\n' % n)
if n > 0:
    with open(path, 'w', encoding='utf-8') as f:
        f.write(new_content)
