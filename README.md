MySQL-Allocator-PHP
===================

Allocates MySQL users and databases, authenticated using the Duke Shibboleth System

Implemented at sql.duke.ly, used to give Duke students an independent MySQL database for projects that require one, but don't justify the allocation of an entire server.

Creates a database and user, both of which are named the students' netids.

This system requires an assumed secure shibboleth connection, with the parameters offered by Duke. Without such a connection, this will not operate.
