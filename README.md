# Atlas.Orm

> No annotations. No migrations. No lazy loading. No data-type abstractions.

Atlas is a [data mapper](http://martinfowler.com/eaaCatalog/dataMapper.html)
implementation for your **persistence model** (*not* your domain model).

As such, Atlas uses the term "record" to indicate that its objects are *not*
domain entities. Note that an Atlas record is a *passive* record, not an [active
record](http://martinfowler.com/eaaCatalog/activeRecord.html); it is
disconnected from the database. Use Atlas records as a way to populate your
domain entities, or use them directly for simple data source interactions.

Atlas is ready for side-project and experimental use. Please send bug reports
and pull requests!

Documentation is in [the docs directory](./docs/index.md).

## Rationale

(Or, "Why does Atlas exist?")

I wanted an alternative to Active Record that would allow you to get started
about as easily as Active Record for your *persistence* model, and then refactor
more easily towards a richer *domain* model as needed.

Using a table data gateway for the underlying table Rows, then composing them
into Records and RecordSets via a data mapper, does the trick. As you begin to
need simple behaviors, you can add them to the Record and RecordSet persistence
model objects. (Rows do not have behavior.) Your domain logic layer (e.g. a
service layer) can then use them as needed.

However, per [this article from Mehdi Khalili][mkap], the target end-state for
your modeling should eventually move toward "Domain Model composed of
Persistence Model". That is, the domain Entity and Aggregate classes might use
data source Records and RecordSets internally, but will not expose them. They
can manipulate the persistence model objects internally as much as they wish.
E.g., an Entity might have a `getAddress()`method and read from the internal
Record (which in turn reads from its internal Row or Related objects).
Alternatively, the end state might be "DDD on top of ORM" where Repositories map
the persistence model objects to domain Entities, Value Objects, and Aggregates.

A persistence model alone should get you a long way, especially at the beginning
of a project. Even so, the Row, Record, and RecordSet objects are disconnected
from the database, which should make the refactoring process a lot cleaner than
with Active Record.

[mkap]: http://www.mehdi-khalili.com/orm-anti-patterns-part-4-persistence-domain-model/

Other rationalizations, essentially based around things I *do not* want in an
ORM:

- No annotations. I want the code to be in code, not in comments.

- No migrations or other table-modification logic. Many ORMs read the PHP objects
and then create or modify tables from them. I want the persistence system to be
a *model* of the schema, not a *creator* of it. If I need a migration, I'll use
a tool specifically for migrations.

- No lazy-loading. Lazy-loading is seductive but eventually is more trouble than
it's worth; I don't want it to be available at all, so that it cannot accidently
be invoked.

- No data-type abstractions. I used to think data-type abstraction was great,
but it turns out to be another thing that's just not worth the cost. I want the
actual underlying database types to be exposed and available as much as
possible.

Possible deal-breakers for potential users:

- Atlas uses code generation, though only in a very limited way. I'm not a fan
of code generation myself, but it turns out to be useful for building the SQL
table classes. Each table is described as a PHP class, one that just returns
things like the table name, the column names, etc. That's the only class that
really gets generated by Atlas; the others are just empty extensions of parent
classes.

- Atlas uses base Row, Record, and RecordSet classes, instead of plain-old PHP
objects. If this were a domain modeling system, a base class would be
unacceptable. Because Atlas is a *persistence* modeling system, I think a base
class is less objectionable, but for some people that's going to be a real
problem.

Finally, Atlas supports **composite primary keys** and **composite foreign keys.**
Performance in these cases is sure to be slower, but it is in fact supported.
