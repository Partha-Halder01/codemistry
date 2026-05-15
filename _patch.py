import sys
p = 'domains/codemistry.in/public_html/assets/index-cZV0TcH-.js'
old = ',z="918910710136"'
new = ',z="918967739189"'
d = open(p).read()
n = d.count(old)
d2 = d.replace(old, new)
open(p, 'w').write(d2)
print('replaced occurrences:', n)
print('918967739189 count:', d2.count('918967739189'))
print('918910710136 count:', d2.count('918910710136'))
