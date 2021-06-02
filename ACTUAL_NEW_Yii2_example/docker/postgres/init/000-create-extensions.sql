BEGIN;
CREATE EXTENSION "uuid-ossp";
CREATE EXTENSION "pg_trgm";
CREATE EXTENSION "btree_gin";

-- alternative operator for '?'
CREATE OPERATOR @ (LEFTARG = jsonb, RIGHTARG = text, PROCEDURE = jsonb_exists);
-- alternative operator for '?|'
CREATE OPERATOR @| (LEFTARG = jsonb, RIGHTARG = text[], PROCEDURE = jsonb_exists_any);
-- alternative operator for '?&'
CREATE OPERATOR @& (LEFTARG = jsonb, RIGHTARG = text[], PROCEDURE = jsonb_exists_all);

COMMIT;