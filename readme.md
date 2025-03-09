# Data Transfer Object

A data transfer object (DTO) is an object that takes information from one place 
and moves it to another.  Frequently, I use these to avoid having to pass 
arrays around because my IDE can provide code hinting for objects but less so 
for arrays.

## Repositories

These are the successors to my so-called [repository](https://github.com/dashifen/repository)
object.  With the release of 8.4 and both property hooks and asymmetric 
property visibility, the work that my repository objects was doing to produce
properties that could be publicly read but set only with an object (and, 
perhaps, only once) is unnecessary.
